<?php

use App\Http\Controllers\XPayrCheckoutController;
use App\Http\Controllers\XPayrWebhookController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->post('/api/checkout/xpayr', [XPayrCheckoutController::class, 'store']);
Route::post('/api/webhooks/xpayr', XPayrWebhookController::class)->withoutMiddleware([ValidateCsrfToken::class]);
