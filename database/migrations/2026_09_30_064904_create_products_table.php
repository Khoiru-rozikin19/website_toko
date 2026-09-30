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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('category'); // Telkomsel, Indosat, XL Axiata, Axis, Tri, Smartfren, VPN Premium
            $table->text('description')->nullable();
            $table->string('active_period')->default('30 Hari');
            $table->unsignedInteger('modal_price')->default(0);
            $table->unsignedInteger('sell_price')->default(0);
            $table->integer('margin')->default(0);
            $table->string('status')->default('active'); // active, inactive
            $table->unsignedInteger('sales_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
