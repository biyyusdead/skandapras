<?php

namespace App\Http\CacheProfiles\Replacers;

use Symfony\Component\HttpFoundation\Response;
use Spatie\ResponseCache\Replacers\Replacer;

class RemoveCookieReplacer implements Replacer
{
    /**
     * Dipanggil SEBELUM response disimpan ke cache.
     * Hapus Set-Cookie header.
     */
    public function prepareResponseToCache(Response $response): void
    {
        $response->headers->remove('Set-Cookie');
        
        // Force Cache-Control public
        $response->headers->set('Cache-Control', 'public, max-age=300, s-maxage=600');
    }

    /**
     * Dipanggil SAAT response di-serve dari cache.
     * Kosong — tidak perlu tambah apapun.
     */
    public function replaceInCachedResponse(Response $response): void
    {
        // Nothing to do
    }
}