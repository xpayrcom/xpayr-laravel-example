<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$required = [
    'app/Services/XPayrService.php',
    'app/Http/Controllers/XPayrCheckoutController.php',
    'app/Http/Controllers/XPayrWebhookController.php',
    'database/migrations/2026_01_01_000000_create_xpayr_tables.php',
    'routes/xpayr.php',
];
foreach ($required as $file) {
    assert(is_file($root . '/' . $file), "Missing {$file}");
}
$webhook = file_get_contents($root . '/app/Http/Controllers/XPayrWebhookController.php');
assert(str_contains($webhook, 'getContent()'), 'Webhook must verify the raw body.');
assert(str_contains($webhook, 'hash_equals'), 'Webhook must use constant-time verification.');
assert(str_contains($webhook, 'firstOrCreate'), 'Webhook must be idempotent.');
echo "Laravel integration structure OK\n";
