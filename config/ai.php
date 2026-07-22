<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | Supported providers: "gemini", "openai", "anthropic"
    |
    */
    'default_provider' => env('AITOS_DEFAULT_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Provider Configurations
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('AITOS_GEMINI_MODEL', 'gemini-3.5-flash'),
            'temperature' => 0.2,
            'max_tokens' => 4000,
            'timeout' => 180,
        ],
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('AITOS_OPENAI_MODEL', 'nvidia/nemotron-3-ultra-550b-a55b:free'),
            'temperature' => 0.2,
            'max_tokens' => 4000,
            'timeout' => 180,
        ],
        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('AITOS_ANTHROPIC_MODEL', 'claude-3-haiku-20240307'),
            'temperature' => 0.2,
            'max_tokens' => 3000,
            'timeout' => 40,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Analysis Cache TTL (seconds)
    |--------------------------------------------------------------------------
    |
    | How long AI analysis results are cached before requiring a fresh API call.
    | Default: 3600 seconds (1 hour). Set to 0 to disable caching.
    |
    */
    'cache_ttl' => (int) env('AI_CACHE_TTL', 3600),
];
