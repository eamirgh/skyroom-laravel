<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Skyroom API Key
    |--------------------------------------------------------------------------
    |
    | Your 50-character Skyroom API key obtained from Skyroom dashboard.
    |
    */
    'api_key' => env('SKYROOM_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Skyroom Base API URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Skyroom web service.
    |
    */
    'base_url' => env('SKYROOM_BASE_URL', 'https://www.skyroom.online/skyroom/api/'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for HTTP requests sent to Skyroom API.
    |
    */
    'timeout' => (int) env('SKYROOM_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Automatic retry configuration for transient network/server failures.
    |
    */
    'retry' => [
        'times' => (int) env('SKYROOM_RETRY_TIMES', 3),
        'sleep_milliseconds' => (int) env('SKYROOM_RETRY_SLEEP', 100),
    ],
];
