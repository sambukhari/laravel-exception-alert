<?php

return [
    'enabled' => env('EXCEPTION_ALERT_ENABLED', true),

    'to' => env('EXCEPTION_ALERT_EMAIL', 'developer@example.com'),

    'exceptions' => [
        401 => true,
        403 => true,
        404 => false,
        419 => true,
        429 => true,
        500 => true,
        503 => true,
    ],
];
