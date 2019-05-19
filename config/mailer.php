<?php

return [
    'host' => env('MAIL_HOST'),
    'port' => env('MAIL_PORT'),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),

    'settings' => [
        'cache' => filter_var(env('MAIL_ENABLE_CACHE', false), FILTER_VALIDATE_BOOLEAN) ? storage_path("cache/email-views") : false
    ]
];
