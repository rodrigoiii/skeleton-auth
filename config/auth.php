<?php

return [
    'login' => [
        'session_lifespan' => 60 * 30 // 30 minutes
    ],
    'register' => [
        'is_verification_enabled' => true,
        'is_log_in_after_register' => true,
        'token_lifespan' => 60 * 30, // 30 minutes
        'upload_path' => "auth"
    ]
];
