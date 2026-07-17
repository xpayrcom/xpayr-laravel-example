<?php

namespace App\Http\Controllers;

use App\Models\XPayrPayment;
use App\Models\XPayrWebhookEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class XPayrWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $rawBody = $request->getContent();
        $signature = strtolower(trim((string) $request->header('X-XPayr-Signature')));
        $signature = str_starts_with($signature, 'sha256=') ? substr($signature, 7) : $signature;
        $secret = (string) config('xpayr.webhook_secret');

        if ($secret === '' || preg_match('/^[a-f0-9]{64}$/', $signature) !== 1
            || !hash_equals(hash_hmac('sha256', $rawBody, $secret), $signature)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($rawBody, true);
        $eventId = is_array($event) ? ($event['id'] ?? null) : null;
        $eventType = is_array($event) ? ($event['type'] ?? null) : null;
        if (!is_string($eventId) || $eventId === '' || !is_string($eventType)) {
            return response()->json(['error' => 'Invalid event envelope'], 400);
        }

        DB::transaction(function () use ($eventId, $eventType, $event): void {
            $record = XPayrWebhookEvent::firstOrCreate(
                ['event_id' => $eventId],
                ['event_type' => $eventType, 'payload' => $event],
            );
            if (!$record->wasRecentlyCreated) {
                return;
            }

            $paymentId = $event['data']['payment_id'] ?? $event['data']['id'] ?? null;
            $status = $event['data']['status'] ?? null;
            if (is_string($paymentId) && is_string($status)) {
                XPayrPayment::where('payment_id', $paymentId)->update([
                    'status' => $status,
                    'payload' => $event['data'],
                ]);
            }
            $record->update(['processed_at' => now()]);
        }, attempts: 3);

        return response()->json(['received' => true]);
    }
}
