<?php

namespace Tests\Feature;

use App\Models\ChatbotUsage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ChatbotEfficiencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        RateLimiter::clear('chatbot');
        Config::set('services.rindri.key', 'test-api-key');
        Config::set('services.rindri.base_url', 'https://api.rindri.test/v1');
        Config::set('services.rindri.model', 'claude-sonnet-4-5');
    }

    /**
     * 1. Permintaan valid menghasilkan respons dan mencatat penggunaan token secara aman.
     */
    public function test_valid_message_generates_response_and_records_usage(): void
    {
        Http::fake([
            'https://api.rindri.test/v1/chat/completions' => Http::response([
                'id' => 'chatcmpl-test-123',
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'SMK Negeri 2 Karanganyar memiliki 6 program keahlian unggulan.',
                        ],
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 120,
                    'completion_tokens' => 35,
                    'total_tokens' => 155,
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('public.chatbot.chat'), [
            'message' => 'Apa saja jurusan di SMKN 2 Karanganyar?',
            'history' => [],
        ]);

        $response->assertOk();
        $response->assertJson([
            'reply' => 'SMK Negeri 2 Karanganyar memiliki 6 program keahlian unggulan.',
        ]);

        // Verifikasi pencatatan kuota dan token di database
        $this->assertDatabaseHas('chatbot_usages', [
            'status' => 'success',
            'prompt_tokens' => 120,
            'completion_tokens' => 35,
            'total_tokens' => 155,
            'is_estimated' => false,
        ]);

        // Pastikan tidak ada data sensitif yang tercatat
        $usage = ChatbotUsage::latest()->first();
        $this->assertNotNull($usage);
        $this->assertStringNotContainsString('test-api-key', json_encode($usage->toArray()));
    }

    /**
     * 2. Pesan melebihi batas karakter (1000) ditolak sebelum memanggil provider AI.
     */
    public function test_message_exceeding_character_limit_is_rejected_without_calling_provider(): void
    {
        Http::fake();

        $longMessage = str_repeat('A', 1005);

        $response = $this->postJson(route('public.chatbot.chat'), [
            'message' => $longMessage,
            'history' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);

        // AI Provider tidak boleh terpanggil dan tidak ada token yang terbuang
        Http::assertNothingSent();
    }

    /**
     * 3. Konkurensi request (in-flight lock) menolak klik ganda atau pemanggilan bersamaan.
     */
    public function test_concurrent_in_flight_request_is_blocked(): void
    {
        Http::fake();

        $user = User::factory()->create(['is_active' => true]);

        // Simulasikan request yang sedang berjalan untuk user ini
        $lockKey = 'chatbot_inflight:user_'.$user->id;
        Cache::add($lockKey, true, now()->addSeconds(30));

        $response = $this->actingAs($user)->postJson(route('public.chatbot.chat'), [
            'message' => 'Pertanyaan ketika sedang memproses...',
        ]);

        $response->assertStatus(429);
        $response->assertJsonFragment([
            'reply' => 'Permintaan Anda sebelumnya masih sedang diproses. Mohon tunggu beberapa detik sebelum mengirim pesan berikutnya.',
        ]);

        Http::assertNothingSent();
    }

    /**
     * 4. Riwayat percakapan yang panjang dipotong ke batas aman (maks 6 pesan).
     */
    public function test_long_conversation_history_is_trimmed(): void
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Jawaban setelah riwayat dipotong.']],
                ],
            ], 200),
        ]);

        $history = [];
        for ($i = 1; $i <= 12; $i++) {
            $history[] = [
                'role' => $i % 2 === 1 ? 'user' : 'assistant',
                'text' => "Pesan riwayat nomor {$i}",
            ];
        }

        $response = $this->postJson(route('public.chatbot.chat'), [
            'message' => 'Pertanyaan ke 13',
            'history' => $history,
        ]);

        $response->assertOk();

        Http::assertSent(function ($request) {
            $messages = $request['messages'];

            // 1 system message + max 6 history + 1 current user message = max 8
            return count($messages) <= 8;
        });
    }

    /**
     * 5. Pertanyaan FAQ standalone disimpan dalam cache dan tidak memanggil API kedua kalinya.
     */
    public function test_standalone_faq_question_is_cached(): void
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'SMKN 2 Karanganyar beralamat di Jl. Yos Sudarso.']],
                ],
                'usage' => [
                    'prompt_tokens' => 50,
                    'completion_tokens' => 15,
                    'total_tokens' => 65,
                ],
            ], 200),
        ]);

        // Request 1: Memanggil AI provider
        $res1 = $this->postJson(route('public.chatbot.chat'), [
            'message' => 'Di mana alamat SMK Negeri 2 Karanganyar?',
            'history' => [],
        ]);
        $res1->assertOk();
        $this->assertEquals(1, Http::recorded()->count());

        // Request 2: Pertanyaan yang sama tanpa riwayat percakapan diambil dari cache
        $res2 = $this->postJson(route('public.chatbot.chat'), [
            'message' => 'Di mana alamat SMK Negeri 2 Karanganyar?',
            'history' => [],
        ]);
        $res2->assertOk();
        $res2->assertJson([
            'reply' => 'SMKN 2 Karanganyar beralamat di Jl. Yos Sudarso.',
        ]);

        // Jumlah panggilan ke provider tetap 1 (tidak memanggil provider kedua kalinya!)
        $this->assertEquals(1, Http::recorded()->count());

        // Verifikasi pencatatan status cached
        $this->assertDatabaseHas('chatbot_usages', [
            'status' => 'cached',
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
            'total_tokens' => 0,
        ]);
    }

    /**
     * 6. Rate limiter menolak request jika melebihi batas 5 request per menit.
     */
    public function test_rate_limiter_enforces_per_minute_limit(): void
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [['message' => ['content' => 'Ok']]],
            ], 200),
        ]);

        $user = User::factory()->create(['is_active' => true]);

        // Kirim 5 request pertama (sesuai kuota)
        for ($i = 0; $i < 5; $i++) {
            $res = $this->actingAs($user)->postJson(route('public.chatbot.chat'), [
                'message' => "Pesan unik ke-{$i}",
                'history' => [['role' => 'user', 'text' => "konteks-{$i}"]], // hindari cache matching
            ]);
            $res->assertOk();
        }

        // Request ke-6 ditolak oleh middleware RateLimiter
        $res6 = $this->actingAs($user)->postJson(route('public.chatbot.chat'), [
            'message' => 'Pesan ke-6 melebihi batas per menit',
        ]);

        $res6->assertStatus(429);
        $res6->assertJsonStructure(['reply', 'retry_after']);

        // AI Provider tidak dipanggil pada request ke-6
        $this->assertEquals(5, Http::recorded()->count());
    }

    /**
     * 7. Respons kegagalan provider ditangani secara aman tanpa membocorkan API key atau trace.
     */
    public function test_provider_failure_returns_safe_error_response(): void
    {
        Http::fake([
            '*' => Http::response([
                'error' => 'Internal server error on provider',
            ], 500),
        ]);

        $response = $this->postJson(route('public.chatbot.chat'), [
            'message' => 'Pertanyaan yang memicu error provider',
        ]);

        $response->assertOk();
        $this->assertNotEmpty($response->json('reply'));

        // Pastikan tidak ada kebocoran rahasia atau API key
        $this->assertStringNotContainsString('test-api-key', $response->getContent());

        // Pastikan log kegagalan tercatat
        $this->assertDatabaseHas('chatbot_usages', [
            'status' => 'error',
        ]);
    }

    /**
     * 8. Pengguna terautentikasi diidentifikasi berdasarkan User ID resmi, bukan payload manipulatif.
     */
    public function test_authenticated_user_identity_is_enforced(): void
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [['message' => ['content' => 'Halo Waka!']]],
            ], 200),
        ]);

        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->postJson(route('public.chatbot.chat'), [
            'message' => 'Pertanyaan dari user terautentikasi',
            'user_id' => 999999, // Percobaan spoofing payload
        ]);

        $response->assertOk();

        // Identifier yang tersimpan adalah ID asli user ($user->id), bukan 999999
        $this->assertDatabaseHas('chatbot_usages', [
            'user_id' => $user->id,
            'status' => 'success',
        ]);
        $this->assertDatabaseMissing('chatbot_usages', [
            'user_id' => 999999,
        ]);
    }
}
