<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_ref')->unique();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->string('category')->default('Data');
            $table->string('sku')->default('-');
            $table->string('target');
            $table->text('notes')->nullable();
            $table->unsignedInteger('base_price')->default(0);
            $table->unsignedInteger('unique_code')->default(0);
            $table->unsignedInteger('total_amount')->default(0);
            $table->text('qris_payload')->nullable();
            $table->string('status')->default('pending'); // pending, paid, processing, success, failed, expired
            $table->string('payment_method')->default('QRIS DANA Bisnis');
            $table->string('serial_number')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
