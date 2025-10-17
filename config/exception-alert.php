<?php

return [
    // Master switch (toggle all alerts)
    'enabled' => env('EXCEPTION_ALERT_ENABLED', true),

    // Developer email (recipient)
    'to' => env('EXCEPTION_ALERT_EMAIL', null),

    // Which HTTP status codes to alert about (boolean)
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
