<?php

return [
    'secret_key' => env('XPAYR_SECRET_KEY'),
    'webhook_secret' => env('XPAYR_WEBHOOK_SECRET'),
    'base_url' => env('XPAYR_BASE_URL', 'https://xpayr.com/api/v1'),
    'timeout' => (int) env('XPAYR_TIMEOUT', 20),
];
