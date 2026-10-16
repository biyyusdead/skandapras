<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'qwen' => [
        'key' => env('QWEN_API_KEY'),
        'model' => env('QWEN_MODEL', 'Qwen-3.8-Max'),
        'base_url' => env('QWEN_BASE_URL', 'https://top-tools-ai.com/api/v1'),
    ],

    // Ganti jadi (atau tambah):
    'rindri' => [
        'key' => env('RINDRI_API_KEY'),
        'base_url' => env('RINDRI_BASE_URL', 'https://api.rindri.com/v1'),
        'model' => env('RINDRI_MODEL', 'claude-sonnet-4-5'),
    ],

];
