<?php

namespace Tests\Feature;

use App\Enums\ReporterType;
use App\Models\Room;
use App\Services\TurnstileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TurnstileBotProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Cache::flush();
        RateLimiter::clear('damage-report');

        // Nonaktifkan fake default agar dapat menguji alur verifikasi nyata dengan Http::fake()
        TurnstileService::resetFake();

        // Atur konfigurasi environment Turnstile untuk testing
        Config::set('services.turnstile.site_key', 'test-site-key-0x4AAAAAA');
        Config::set('services.turnstile.secret_key', 'test-secret-key-0x4AAAAAA');
        Config::set('services.turnstile.verify_url', 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
        Config::set('services.turnstile.action', 'pengaduan_sarpras');
        Config::set('services.turnstile.allowed_hostnames', ['localhost', '127.0.0.1']);

        $this->room = Room::create([
            'name' => 'Lab Komputer RPL',
            'code' => 'LAB-RPL-01',
            'type' => 'laboratorium',
            'capacity' => 36,
            'status' => 'tersedia',
        ]);
    }

    protected function tearDown(): void
    {
        TurnstileService::resetFake();
        parent::tearDown();
    }

    /**
     * 1. Token Turnstile yang valid diverifikasi ke Cloudflare dan pengaduan berhasil tersimpan.
     */
    public function test_valid_turnstile_token_allows_submission(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'pengaduan_sarpras',
                'hostname' => 'localhost',
                'challenge_ts' => now()->toIso8601String(),
            ], 200),
        ]);

        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => 'Ahmad Fauzi',
            'reporter_phone' => '081234567890',
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'Rekayasa Perangkat Lunak',
            'room_id' => $this->room->id,
            'description' => 'Monitor komputer nomor 12 tidak menyala sama sekali.',
            'evidence_photo' => UploadedFile::fake()->image('monitor.jpg'),
            'cf-turnstile-response' => 'valid-turnstile-token-abc123',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('damage_reports', [
            'reporter_name' => 'Ahmad Fauzi',
            'reporter_identifier' => '0051234567',
        ]);
    }

    /**
     * 2. Pengiriman tanpa token CAPTCHA ditolak sebelum diproses.
     */
    public function test_missing_turnstile_token_is_rejected(): void
    {
        Http::fake();

        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => 'Ahmad Fauzi',
            'reporter_phone' => '081234567890',
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'RPL',
            'room_id' => $this->room->id,
            'description' => 'Kerusakan tanpa menyelesaikan CAPTCHA terlebih dahulu.',
            'cf-turnstile-response' => '',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        Http::assertNothingSent();
        $this->assertDatabaseCount('damage_reports', 0);
    }

    /**
     * 3. Token Turnstile yang tidak valid (invalid token) ditolak.
     */
    public function test_invalid_turnstile_token_is_rejected(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => 'Siswa X',
            'reporter_phone' => '081234567890',
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'RPL',
            'room_id' => $this->room->id,
            'description' => 'Laporan dengan token turnstile palsu.',
            'cf-turnstile-response' => 'invalid-token-xyz',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        $this->assertDatabaseCount('damage_reports', 0);
    }

    /**
     * 4. Token kedaluwarsa atau timeout ditolak dengan pesan yang ramah.
     */
    public function test_expired_or_duplicate_token_from_provider_is_rejected(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['timeout-or-duplicate'],
            ], 200),
        ]);

        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => 'Siswa X',
            'reporter_phone' => '081234567890',
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'RPL',
            'room_id' => $this->room->id,
            'description' => 'Laporan dengan token yang sudah kedaluwarsa.',
            'cf-turnstile-response' => 'expired-token-123',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        $this->assertDatabaseCount('damage_reports', 0);
    }

    /**
     * 5. Penggunaan ulang token yang sama (replay attack) ditolak oleh cache lock backend.
     */
    public function test_token_reuse_is_prevented_by_cache_lock(): void
    {
        $token = 'reusable-test-token-777';

        // Set kunci bahwa token ini baru saja digunakan
        $tokenHash = hash('sha256', $token);
        Cache::put('turnstile_token_used:'.$tokenHash, true, now()->addMinutes(10));

        Http::fake();

        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => 'Penyerang Replay',
            'reporter_phone' => '081234567890',
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'RPL',
            'room_id' => $this->room->id,
            'description' => 'Mencoba mengirim kembali token yang sudah terpakai.',
            'cf-turnstile-response' => $token,
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        // Cloudflare tidak dipanggil karena langsung dicegat di level cache lock
        Http::assertNothingSent();
        $this->assertDatabaseCount('damage_reports', 0);
    }

    /**
     * 6. Token dengan hostname yang tidak sesuai ditolak.
     */
    public function test_hostname_mismatch_is_rejected(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'pengaduan_sarpras',
                'hostname' => 'attacker-malicious-domain.com', // Bukan domain sekolah
            ], 200),
        ]);

        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => 'Siswa X',
            'reporter_phone' => '081234567890',
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'RPL',
            'room_id' => $this->room->id,
            'description' => 'Laporan dari domain yang tidak diizinkan.',
            'cf-turnstile-response' => 'foreign-domain-token',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        $this->assertDatabaseCount('damage_reports', 0);
    }

    /**
     * 7. Timeout koneksi penyedia CAPTCHA ditangani dengan aman.
     */
    public function test_turnstile_provider_timeout_is_handled_gracefully(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => function () {
                throw new ConnectionException('Connection timed out after 5000 milliseconds');
            },
        ]);

        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => 'Siswa X',
            'reporter_phone' => '081234567890',
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'RPL',
            'room_id' => $this->room->id,
            'description' => 'Laporan saat jaringan Cloudflare sedang timeout.',
            'cf-turnstile-response' => 'timeout-token-test',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        $this->assertDatabaseCount('damage_reports', 0);
    }

    /**
     * 8. Pengiriman ganda cepat (double submit lock 5 detik) dicegah.
     */
    public function test_rapid_double_submission_is_prevented(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'action' => 'pengaduan_sarpras',
                'hostname' => 'localhost',
            ], 200),
        ]);

        $payload = [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => 'Siswa Rajin',
            'reporter_phone' => '081234567890',
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'RPL',
            'room_id' => $this->room->id,
            'description' => 'Kerusakan stop kontak meja 3 percikkan api.',
            'cf-turnstile-response' => 'first-valid-token',
        ];

        // Submission 1: Berhasil
        $res1 = $this->post(route('public.sarpras.pengaduan.store'), $payload);
        $res1->assertSessionHasNoErrors();
        $this->assertDatabaseCount('damage_reports', 1);

        // Submission 2: Pengiriman ulang cepat dalam 5 detik ditolak
        $payload['cf-turnstile-response'] = 'second-token-attempt';
        $res2 = $this->post(route('public.sarpras.pengaduan.store'), $payload);
        $res2->assertSessionHasErrors(['cf-turnstile-response']);
        // Jumlah pengaduan tetap 1, tidak menjadi 2!
        $this->assertDatabaseCount('damage_reports', 1);
    }

    /**
     * 9. Jika Turnstile belum dikonfigurasi, pengaduan ditolak dengan pesan yang jelas untuk admin.
     * Tidak ada fallback diam-diam yang meloloskan bot tanpa verifikasi di server produksi.
     */
    public function test_unconfigured_turnstile_blocks_submission_with_clear_admin_message(): void
    {
        Config::set('services.turnstile.site_key', null);
        Config::set('services.turnstile.secret_key', null);

        Http::fake();

        $response = $this->post(route('public.sarpras.pengaduan.store'), [
            'reporter_type' => ReporterType::SISWA->value,
            'reporter_name' => 'Siswa X',
            'reporter_phone' => '081234567890',
            'reporter_identifier' => '0051234567',
            'student_class' => 'XII',
            'student_major' => 'RPL',
            'room_id' => $this->room->id,
            'description' => 'Percobaan submit saat server belum memasang Turnstile key.',
            'cf-turnstile-response' => 'dummy-token',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        $this->assertDatabaseCount('damage_reports', 0);
        Http::assertNothingSent();
    }
}
