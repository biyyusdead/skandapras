<?php

namespace App\Services;

class TurnstileResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $status,
        public readonly string $message,
        public readonly array $raw = []
    ) {}

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isConfigured(): bool
    {
        return $this->status !== 'not_configured';
    }

    public function errorMessage(): string
    {
        return $this->message;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function raw(): array
    {
        return $this->raw;
    }

    public static function success(array $raw = []): self
    {
        return new self(
            success: true,
            status: 'success',
            message: 'Verifikasi keamanan berhasil.',
            raw: $raw
        );
    }

    public static function notConfigured(string $message = 'Layanan verifikasi bot (Turnstile) belum dikonfigurasi di server. Pengaduan ditangguhkan demi keamanan.'): self
    {
        return new self(
            success: false,
            status: 'not_configured',
            message: $message
        );
    }

    public static function missingToken(string $message = 'Silakan selesaikan verifikasi keamanan (CAPTCHA) terlebih dahulu.'): self
    {
        return new self(
            success: false,
            status: 'missing_token',
            message: $message
        );
    }

    public static function duplicateToken(string $message = 'Token verifikasi keamanan telah digunakan atau permintaan sedang diproses. Silakan muat ulang verifikasi.'): self
    {
        return new self(
            success: false,
            status: 'duplicate_token',
            message: $message
        );
    }

    public static function expiredOrDuplicate(string $message = 'Token verifikasi keamanan telah kedaluwarsa atau sudah digunakan. Silakan ulangi verifikasi CAPTCHA.', array $raw = []): self
    {
        return new self(
            success: false,
            status: 'expired_or_duplicate',
            message: $message,
            raw: $raw
        );
    }

    public static function timeout(string $message = 'Gagal menghubungi server verifikasi keamanan (timeout koneksi). Silakan periksa koneksi internet Anda dan coba lagi.'): self
    {
        return new self(
            success: false,
            status: 'provider_timeout',
            message: $message
        );
    }

    public static function providerError(string $message = 'Terjadi kendala pada penyedia verifikasi keamanan. Silakan coba beberapa saat lagi.', array $raw = []): self
    {
        return new self(
            success: false,
            status: 'provider_error',
            message: $message,
            raw: $raw
        );
    }

    public static function hostnameMismatch(string $message = 'Verifikasi keamanan ditolak karena nama domain/hostname tidak sesuai.', array $raw = []): self
    {
        return new self(
            success: false,
            status: 'hostname_mismatch',
            message: $message,
            raw: $raw
        );
    }

    public static function actionMismatch(string $message = 'Verifikasi keamanan ditolak karena konteks tindakan tidak sesuai.', array $raw = []): self
    {
        return new self(
            success: false,
            status: 'action_mismatch',
            message: $message,
            raw: $raw
        );
    }

    public static function invalid(string $message = 'Verifikasi keamanan tidak valid. Silakan coba lagi.', array $raw = []): self
    {
        return new self(
            success: false,
            status: 'invalid_token',
            message: $message,
            raw: $raw
        );
    }
}
