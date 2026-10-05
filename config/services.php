<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
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

    // ============================================
    // FIREBASE / FIRESTORE
    // ============================================
    'firebase' => [
    'base_url' => env('FIREBASE_FIRESTORE_BASE_URL'),
    'api_key' => env('FIREBASE_API_KEY'),
    'project_id' => env('FIREBASE_PROJECT_ID', 'gps1-e12e5'),
    'credentials' => storage_path('app/firebase/service-account.json'),
],

];