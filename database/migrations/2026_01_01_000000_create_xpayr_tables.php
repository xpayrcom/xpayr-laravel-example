<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('xpayr_payments', function (Blueprint $table): void {
            $table->id();
            $table->string('payment_id', 80)->unique();
            $table->string('order_id', 128)->index();
            $table->decimal('amount', 36, 18);
            $table->string('currency', 16);
            $table->string('network', 64);
            $table->string('status', 32)->index();
            $table->text('payment_url');
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('xpayr_webhook_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_id', 100)->unique();
            $table->string('event_type', 80)->index();
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xpayr_webhook_events');
        Schema::dropIfExists('xpayr_payments');
    }
};
