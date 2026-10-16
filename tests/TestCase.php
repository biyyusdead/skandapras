<?php

namespace Tests;

use App\Services\TurnstileService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Default fake Turnstile untuk test suite yang sudah ada agar tidak terganggu
        TurnstileService::fake();
    }
}
