<?php

namespace Tests\Feature;

use App\Models\XPayrPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class XPayrWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_webhook_updates_payment_once(): void
    {
        config(['xpayr.webhook_secret' => 'webhook-test-secret']);
        XPayrPayment::create([
            'payment_id' => 'ps_webhook_001',
            'order_id' => 'ORDER-2001',
            'amount' => '10.000000',
            'currency' => 'USDC',
            'network' => 'arc-testnet',
            'status' => 'pending',
            'payment_url' => 'https://xpayr.com/test/ps_webhook_001',
        ]);
        $body = json_encode([
            'id' => 'evt_completed_001',
            'type' => 'payment.completed',
            'data' => [
                'id' => 'ps_webhook_001',
                'status' => 'completed',
                'tx_hash' => '0x'.str_repeat('1', 64),
            ],
        ], JSON_UNESCAPED_SLASHES);
        self::assertIsString($body);
        $signature = hash_hmac('sha256', $body, 'webhook-test-secret');

        $send = fn () => $this->call(
            'POST',
            '/api/webhooks/xpayr',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_XPAYR_SIGNATURE' => 'sha256='.$signature],
            $body,
        );

        $send()->assertOk()->assertJson(['received' => true]);
        $send()->assertOk()->assertJson(['received' => true]);
        $this->assertDatabaseHas('xpayr_payments', [
            'payment_id' => 'ps_webhook_001',
            'status' => 'completed',
        ]);
        $this->assertDatabaseCount('xpayr_webhook_events', 1);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        config(['xpayr.webhook_secret' => 'webhook-test-secret']);
        $this->call(
            'POST',
            '/api/webhooks/xpayr',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_XPAYR_SIGNATURE' => str_repeat('0', 64)],
            '{"id":"evt_invalid","type":"payment.completed"}',
        )->assertBadRequest();
    }
}
