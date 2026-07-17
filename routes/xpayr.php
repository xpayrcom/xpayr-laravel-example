<?php

use App\Http\Controllers\XPayrCheckoutController;
use App\Http\Controllers\XPayrWebhookController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->post('/checkout/xpayr', [XPayrCheckoutController::class, 'store']);
Route::post('/webhooks/xpayr', XPayrWebhookController::class)->withoutMiddleware([ValidateCsrfToken::class]);
