<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class XPayrService
{
    private function client(): PendingRequest
    {
        $secretKey = (string) config('xpayr.secret_key');
        if ($secretKey === '') {
            throw new RuntimeException('XPAYR_SECRET_KEY is not configured.');
        }

        return Http::acceptJson()
            ->withToken($secretKey)
            ->baseUrl(rtrim((string) config('xpayr.base_url'), '/'))
            ->timeout((int) config('xpayr.timeout', 20));
    }

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function createPayment(array $payload): array
    {
        return $this->client()->post('/payments', $payload)->throw()->json();
    }

    /** @return array<string, mixed> */
    public function getPayment(string $paymentId): array
    {
        return $this->client()->get('/payments/' . rawurlencode($paymentId))->throw()->json();
    }
}
