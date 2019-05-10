<?php

return [
    'host' => app_env('MAIL_HOST', "smtp.mailtrap.io"),
    'port' => app_env('MAIL_PORT', 2525),
    'username' => app_env('MAIL_USERNAME'),
    'password' => app_env('MAIL_PASSWORD'),

    'settings' => [
        'cache' => filter_var(app_env('MAIL_ENABLE_CACHE', false), FILTER_VALIDATE_BOOLEAN) ? storage_path("cache/email-views") : false
    ]
];
