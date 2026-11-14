<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();

            // Data penerima
            $table->string('recipient_name');
            $table->string('phone');
            $table->text('address');
            $table->string('city');
            $table->string('postal_code', 10);

            $table->unsignedBigInteger('total_amount');

            // Status pembayaran (hanya diubah oleh webhook Midtrans)
            $table->string('payment_status')->default('pending'); // pending, paid, expired, failed
            $table->string('payment_type')->nullable();
            $table->string('snap_token')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Status pemrosesan (diubah admin)
            $table->string('order_status')->default('pending'); // pending, processing, shipped, completed, cancelled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};