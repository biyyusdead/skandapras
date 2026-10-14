<?php

namespace App\Http\CacheProfiles;

use DateTime;
use Illuminate\Http\Request;
use Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests;
use Symfony\Component\HttpFoundation\Response;

class PublicCacheProfile extends CacheAllSuccessfulGetRequests
{
    /**
     * Route yang SKIP cache (form, POST, user-specific).
     */
    protected array $skipRoutes = [
        'login',
        'logout',
        'chatbot.chat',
        'public.sarpras.pengaduan',
        'public.sarpras.pengaduan.store',
    ];

    /**
     * Route yang BOLEH cache + TTL (detik).
     */
    protected array $cacheableRoutes = [
        'public.home'             => 300,    // 5 menit
        'public.profile.*'        => 3600,   // 1 jam
        'public.rooms.*'          => 600,    // 10 menit
        'public.akademik.*'       => 1800,   // 30 menit
        'public.kesiswaan.*'      => 600,    // 10 menit
        'public.news.*'           => 300,    // 5 menit
    ];

    /**
     * Cek apakah request boleh di-cache.
     */
    public function shouldCacheRequest(Request $request): bool
    {
        // Hanya GET
        if (!$request->isMethod('GET')) {
            return false;
        }

        // Skip kalau user login
        if (auth()->check()) {
            return false;
        }

        // Skip route form/POST
        if ($request->routeIs(...$this->skipRoutes)) {
            return false;
        }

        // Cek apakah route termasuk cacheable
        foreach (array_keys($this->cacheableRoutes) as $pattern) {
            if ($request->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * TTL per route (dipanggil oleh Spatie).
     */
    public function cacheRequestUntil(Request $request): DateTime
    {
        foreach ($this->cacheableRoutes as $pattern => $ttl) {
            if ($request->routeIs($pattern)) {
                return now()->addSeconds($ttl);
            }
        }

        return now()->addMinutes(5); // default 5 menit
    }

    /**
     * Nama cache suffix — sama untuk semua user publik.
     */
    public function useCacheNameSuffix(Request $request): string
    {
        return 'public';
    }

    /**
     * Override Cache-Control header — biar browser & CDN cache juga.
     */
    public function shouldCacheResponse(Response $response): bool
    {
        // Hanya cache response 200/301/302
        if (!in_array($response->getStatusCode(), [200, 301, 302])) {
            return false;
        }

        // Force Cache-Control: public
        $response->headers->set(
            'Cache-Control',
            'public, max-age=300, s-maxage=600'
        );

        return true;
    }
}