<?php

return [
    'redis' => [

        'cluster' => true,

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],
        'options' => [
            'cluster' => 'redis',
            'parameters' => ['password' => env('REDIS_PASSWORD', null)],
        ],
    ],
];
