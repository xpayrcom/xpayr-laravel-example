<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class XPayrCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_persist_a_testnet_session(): void
    {
        config([
            'xpayr.secret_key' => 'sk_test_example_only',
            'xpayr.base_url' => 'https://xpayr.com/api/v1',
        ]);
        Http::fake([
            'https://xpayr.com/api/v1/payments' => Http::response([
                'id' => 'ps_laravel_example_001',
                'amount' => '19.900000',
                'currency' => 'USDC',
                'network' => 'arc-testnet',
                'status' => 'pending',
                'payment_url' => 'https://xpayr.com/test/ps_laravel_example_001',
                'livemode' => false,
            ], 201),
        ]);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/checkout/xpayr', [
            'amount' => '19.90',
            'currency' => 'USDC',
            'network' => 'arc-testnet',
            'order_id' => 'ORDER-1001',
            'description' => 'Laravel checkout test',
        ]);

        $response->assertCreated()->assertJson([
            'payment_id' => 'ps_laravel_example_001',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('xpayr_payments', [
            'payment_id' => 'ps_laravel_example_001',
            'order_id' => 'ORDER-1001',
            'network' => 'arc-testnet',
            'status' => 'pending',
        ]);
        Http::assertSent(fn (Request $request): bool =>
            $request->url() === 'https://xpayr.com/api/v1/payments'
            && $request->hasHeader('Authorization', 'Bearer sk_test_example_only')
            && $request['network'] === 'arc-testnet');
    }

    public function test_checkout_requires_authentication(): void
    {
        $this->postJson('/api/checkout/xpayr', [])->assertUnauthorized();
    }
}
