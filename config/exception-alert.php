<?php

return [
    'enabled' => env('EXCEPTION_ALERT_ENABLED', true),

    'to' => env('EXCEPTION_ALERT_EMAIL', 'developer@example.com'),

    'exceptions' => [
        400 => true,
        401 => true,
        403 => true,
        404 => true,
        419 => true,
        422 => true,
        429 => true,
        500 => true,
    ],
];
