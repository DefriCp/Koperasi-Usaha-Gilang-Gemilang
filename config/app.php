<?php

return [

    'name' => env('APP_NAME', 'KSU Gilang Gemilang'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => 'Asia/Jakarta',

    'locale' => env('APP_LOCALE', 'id'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'id'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'id_ID'),

    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(explode(',', (string) env('APP_PREVIOUS_KEYS', ''))),
    ],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'brand' => [
        'navy'   => '#0F2E4E',
        'navy2'  => '#143A63',
        'gold'   => '#F2C94C',
        'green'  => '#1E7C4A',
        'paper'  => '#F7F9FC',
        'ink'    => '#1F2937',
        'muted'  => '#6B7280',
        'ring'   => 'rgba(15,46,78,.25)',
        'logo'   => 'img/LOGO-GG.png', 
    ],
];
