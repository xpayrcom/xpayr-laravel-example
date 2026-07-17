<?php

namespace App\Http\Controllers;

use App\Models\XPayrPayment;
use App\Services\XPayrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class XPayrCheckoutController extends Controller
{
    public function store(Request $request, XPayrService $xpayr): JsonResponse
    {
        $payload = $request->validate([
            'amount' => ['required', 'decimal:0,8', 'gt:0'],
            'currency' => ['required', 'string', 'max:16'],
            'network' => ['required', 'string', 'max:64'],
            'order_id' => ['required', 'string', 'max:128'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $session = $xpayr->createPayment($payload);
        $payment = XPayrPayment::updateOrCreate(
            ['payment_id' => $session['id']],
            [
                'order_id' => $payload['order_id'],
                'amount' => $session['amount'],
                'currency' => $session['currency'],
                'network' => $session['network'],
                'status' => $session['status'],
                'payment_url' => $session['payment_url'],
                'payload' => $session,
            ],
        );

        return response()->json([
            'payment_id' => $payment->payment_id,
            'payment_url' => $payment->payment_url,
            'status' => $payment->status,
        ], 201);
    }
}
