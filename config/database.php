<?php

return [
    'redis' => [

        'cluster' => false,

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 1,
        ],
        'options' => [
            'parameters' => ['password' => env('REDIS_PASSWORD', null)],
        ],
    ],
];
