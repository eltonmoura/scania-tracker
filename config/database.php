<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver'    => env('DB_CONNECTION'),
            'host'      => env('DB_HOST'),
            'port'      => env('DB_PORT'),
            'database'  => env('DB_DATABASE'),
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
            'charset'   => env('DB_CHARSET', 'utf8'),
            'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
        ],
        'mysql2' => [
            'driver'    => env('DB2_CONNECTION'),
            'host'      => env('DB2_HOST'),
            'port'      => env('DB2_PORT'),
            'database'  => env('DB2_DATABASE'),
            'username'  => env('DB2_USERNAME'),
            'password'  => env('DB2_PASSWORD'),
            'charset'   => env('DB2_CHARSET', 'utf8'),
            'collation' => env('DB2_COLLATION', 'utf8_unicode_ci'),
        ],
    ],
];
