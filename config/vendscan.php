<?php

return [
    'qr' => [
        'size' => 300,
        'format' => 'png',
        'base_url' => env('APP_URL').'/menu/'
    ],
    'media' => [
        'max_logo_size' => 2048, // KB
        'max_hero_size' => 5120
    ]
];
