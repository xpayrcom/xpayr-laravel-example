<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class XPayrPayment extends Model
{
    protected $fillable = [
        'payment_id', 'order_id', 'amount', 'currency', 'network', 'status', 'payment_url', 'payload',
    ];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
