<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TurnstileRule implements ValidationRule
{
    /**
     * Menandakan bahwa rule ini harus selalu dievaluasi meskipun atribut kosong atau tidak dikirim.
     */
    public bool $implicit = true;

    public function __construct(
        protected ?TurnstileService $service = null,
        protected ?string $clientIp = null,
        protected ?string $requestHost = null
    ) {
        $this->service = $service ?? app(TurnstileService::class);
    }

    /**
     * Jalankan validasi token Turnstile.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $token = is_string($value) ? $value : null;
        $result = $this->service->verify($token, $this->clientIp, $this->requestHost);

        if (! $result->isSuccess()) {
            $fail($result->errorMessage());
        }
    }
}
