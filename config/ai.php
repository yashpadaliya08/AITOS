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
            'timeout' => 120,
        ],
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('AITOS_OPENAI_MODEL', 'gpt-4o-mini'),
            'temperature' => 0.2,
            'max_tokens' => 4000,
            'timeout' => 120,
        ],
        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('AITOS_ANTHROPIC_MODEL', 'claude-3-haiku-20240307'),
            'temperature' => 0.2,
            'max_tokens' => 4000,
            'timeout' => 30,
        ],
    ]
];
