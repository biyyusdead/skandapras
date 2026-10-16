<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileService
{
    protected ?string $siteKey;

    protected ?string $secretKey;

    protected string $verifyUrl;

    protected ?array $allowedHostnames;

    protected ?string $expectedAction;

    protected int $timeout;

    /**
     * Testing fake state.
     */
    protected static bool $fake = false;

    protected static ?TurnstileResult $fakeResult = null;

    public function __construct()
    {
        $this->siteKey = config('services.turnstile.site_key');
        $this->secretKey = config('services.turnstile.secret_key');
        $this->verifyUrl = config('services.turnstile.verify_url', 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
        $this->allowedHostnames = config('services.turnstile.allowed_hostnames');
        $this->expectedAction = config('services.turnstile.action', 'pengaduan_sarpras');
        $this->timeout = (int) config('services.turnstile.timeout', 5);
    }

    /**
     * Cek apakah Turnstile sudah dikonfigurasi di environment.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->siteKey) && ! empty($this->secretKey);
    }

    public function siteKey(): ?string
    {
        return $this->siteKey;
    }

    public function expectedAction(): ?string
    {
        return $this->expectedAction;
    }

    /**
     * Aktifkan fake verification untuk pengujian unit/feature.
     */
    public static function fake(?TurnstileResult $result = null): void
    {
        self::$fake = true;
        self::$fakeResult = $result ?? TurnstileResult::success(['fake' => true]);
    }

    /**
     * Nonaktifkan fake verification untuk menguji alur verifikasi asli / Http::fake().
     */
    public static function resetFake(): void
    {
        self::$fake = false;
        self::$fakeResult = null;
    }

    public static function isFake(): bool
    {
        return self::$fake;
    }

    /**
     * Verifikasi token Turnstile melalui endpoint resmi Cloudflare.
     */
    public function verify(?string $token, ?string $clientIp = null, ?string $requestHost = null): TurnstileResult
    {
        // 1. Cek mode fake test
        if (self::$fake) {
            return self::$fakeResult ?? TurnstileResult::success(['fake' => true]);
        }

        // 2. Cek apakah provider sudah dikonfigurasi
        if (! $this->isConfigured()) {
            return TurnstileResult::notConfigured(
                'Layanan verifikasi bot (Cloudflare Turnstile) belum dikonfigurasi di server. Silakan hubungi administrator sistem untuk melengkapi TURNSTILE_SITE_KEY dan TURNSTILE_SECRET_KEY pada file .env.'
            );
        }

        // 3. Cek ketersediaan token
        $trimmedToken = trim((string) $token);
        if ($trimmedToken === '') {
            return TurnstileResult::missingToken(
                'Silakan selesaikan verifikasi keamanan (CAPTCHA) terlebih dahulu.'
            );
        }

        // 4. Perlindungan pengiriman ganda & replay attack (token reuse)
        $tokenHash = hash('sha256', $trimmedToken);
        $lockKey = 'turnstile_token_used:'.$tokenHash;
        $acquired = Cache::add($lockKey, true, now()->addMinutes(10));
        if (! $acquired) {
            return TurnstileResult::duplicateToken(
                'Token verifikasi telah kedaluwarsa atau sudah digunakan. Silakan muat ulang CAPTCHA jika diperlukan.'
            );
        }

        // 5. Kirim permintaan verifikasi ke Cloudflare Siteverify Endpoint
        $payload = [
            'secret' => $this->secretKey,
            'response' => $trimmedToken,
        ];

        if ($clientIp) {
            $payload['remoteip'] = $clientIp;
        }

        try {
            $response = Http::asForm()
                ->timeout($this->timeout)
                ->post($this->verifyUrl, $payload);
        } catch (ConnectionException $e) {
            Log::warning('[Turnstile] Connection timeout or failure to Cloudflare API', [
                'error' => $e->getMessage(),
                'ip' => $clientIp,
            ]);

            return TurnstileResult::timeout(
                'Gagal menghubungi server verifikasi keamanan (timeout koneksi). Silakan periksa koneksi internet Anda dan coba lagi.'
            );
        } catch (\Throwable $e) {
            Log::error('[Turnstile] Unexpected error during verification request', [
                'error' => $e->getMessage(),
                'ip' => $clientIp,
            ]);

            return TurnstileResult::providerError(
                'Terjadi kendala teknis saat memverifikasi keamanan. Silakan coba beberapa saat lagi.'
            );
        }

        // 6. Cek status HTTP response
        if (! $response->successful()) {
            Log::error('[Turnstile] Provider returned HTTP error status', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return TurnstileResult::providerError(
                'Layanan verifikasi keamanan sedang mengalami gangguan sementara (HTTP '.$response->status().'). Silakan coba lagi.'
            );
        }

        $json = $response->json();
        if (! is_array($json)) {
            return TurnstileResult::providerError(
                'Format respons verifikasi keamanan tidak valid.'
            );
        }

        // 7. Cek status keberhasilan dari Cloudflare
        if (empty($json['success'])) {
            $errorCodes = $json['error-codes'] ?? [];

            Log::info('[Turnstile] Verification failed', [
                'error_codes' => $errorCodes,
                'ip' => $clientIp,
            ]);

            if (in_array('timeout-or-duplicate', $errorCodes, true)) {
                return TurnstileResult::expiredOrDuplicate(
                    'Token verifikasi telah kedaluwarsa atau sudah digunakan. Silakan ulangi verifikasi CAPTCHA.',
                    $json
                );
            }

            return TurnstileResult::invalid(
                'Verifikasi keamanan (CAPTCHA) tidak valid atau gagal. Silakan coba kembali.',
                $json
            );
        }

        // 8. Verifikasi Hostname yang diizinkan
        $responseHostname = $json['hostname'] ?? null;
        if (! empty($responseHostname)) {
            if (! $this->isHostnameAllowed($responseHostname, $requestHost)) {
                Log::warning('[Turnstile] Hostname mismatch detected', [
                    'received_hostname' => $responseHostname,
                    'request_host' => $requestHost,
                    'allowed_hostnames' => $this->allowedHostnames,
                ]);

                return TurnstileResult::hostnameMismatch(
                    "Verifikasi keamanan ditolak karena nama domain asal ({$responseHostname}) tidak sesuai.",
                    $json
                );
            }
        }

        // 9. Verifikasi Tindakan / Context (Action)
        if (! empty($this->expectedAction) && ! empty($json['action'])) {
            if ($json['action'] !== $this->expectedAction) {
                Log::warning('[Turnstile] Action mismatch detected', [
                    'expected_action' => $this->expectedAction,
                    'received_action' => $json['action'],
                ]);

                return TurnstileResult::actionMismatch(
                    'Verifikasi keamanan ditolak karena konteks tindakan tidak sesuai.',
                    $json
                );
            }
        }

        return TurnstileResult::success($json);
    }

    /**
     * Evaluasi apakah hostname respons diizinkan.
     */
    protected function isHostnameAllowed(string $responseHostname, ?string $requestHost = null): bool
    {
        $responseHost = strtolower(trim($responseHostname));

        // Jika ada konfigurasi eksplisit allowed_hostnames
        if (! empty($this->allowedHostnames) && is_array($this->allowedHostnames)) {
            foreach ($this->allowedHostnames as $allowed) {
                if ($responseHost === strtolower(trim($allowed))) {
                    return true;
                }
            }

            return false;
        }

        // Nilai hostname standar untuk testing/lokal Cloudflare Turnstile
        $localAllowed = ['localhost', '127.0.0.1', '::1', 'dummy-hostname', 'example.com'];
        if (in_array($responseHost, $localAllowed, true)) {
            return true;
        }

        // Cek dengan APP_URL
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if ($appHost && $responseHost === strtolower(trim($appHost))) {
            return true;
        }

        // Cek dengan host request saat ini
        if ($requestHost && $responseHost === strtolower(trim($requestHost))) {
            return true;
        }

        return false;
    }
}
