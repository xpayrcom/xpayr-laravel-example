<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class XPayrWebhookEvent extends Model
{
    protected $table = 'xpayr_webhook_events';

    protected $fillable = ['event_id', 'event_type', 'payload', 'processed_at'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'processed_at' => 'immutable_datetime'];
    }
}
