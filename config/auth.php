<?php

return [
    'login' => [
        'session_lifespan' => 60 * 30 // 30 minutes
    ],
    'register' => [
        'is_verification_enabled' => false,
        'is_log_in_after_register' => true,
        'token_expiration' => 60 * 30, // 30 minutes
        'upload_path' => "auth"
    ]
];
