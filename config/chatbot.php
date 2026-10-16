<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chatbot Request & Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Batas request per pengguna untuk mencegah spam dan pengendalian biaya API.
    |
    */
    'rate_limit' => [
        // Maksimal request per menit per pengguna/sesi
        'per_minute' => (int) env('CHATBOT_RATE_LIMIT_PER_MINUTE', 5),

        // Maksimal request per hari per pengguna/sesi
        'per_day' => (int) env('CHATBOT_RATE_LIMIT_PER_DAY', 20),

        // Durasi kunci pencegah request aktif bersamaan (in-flight lock)
        'lock_seconds' => (int) env('CHATBOT_LOCK_SECONDS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chatbot Token & Context Controls
    |--------------------------------------------------------------------------
    |
    | Pengendalian panjang pesan dan konsumsi token per request.
    |
    */
    'tokens' => [
        // Batas panjang teks pesan pengguna (karakter)
        'max_message_chars' => (int) env('CHATBOT_MAX_MESSAGE_CHARS', 1000),

        // Batas maksimum token output untuk setiap jawaban AI
        'max_output_tokens' => (int) env('CHATBOT_MAX_OUTPUT_TOKENS', 500),

        // Batas jumlah percakapan history terbaru yang dikirim ke AI
        'max_history_messages' => (int) env('CHATBOT_MAX_HISTORY_MESSAGES', 6),

        // Batas kuota total token harian per pengguna/sesi
        'daily_token_quota' => (int) env('CHATBOT_DAILY_TOKEN_QUOTA', 50000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Caching
    |--------------------------------------------------------------------------
    |
    | Caching jawaban untuk pertanyaan umum yang stabil agar hemat token.
    |
    */
    'cache' => [
        'enabled' => (bool) env('CHATBOT_CACHE_ENABLED', true),
        'ttl' => (int) env('CHATBOT_CACHE_TTL', 3600), // 1 jam dalam detik
    ],

    /*
    |--------------------------------------------------------------------------
    | Network & Timeout
    |--------------------------------------------------------------------------
    |
    | Batas waktu koneksi ke AI provider.
    |
    */
    'timeout' => (int) env('CHATBOT_TIMEOUT', 20),
    'connect_timeout' => (int) env('CHATBOT_CONNECT_TIMEOUT', 8),
];
