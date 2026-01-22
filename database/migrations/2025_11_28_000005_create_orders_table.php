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
            $table->uuid('uuid')->unique();

            // Customer information
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            // Invoice address
            $table->string('invoice_company')->nullable();
            $table->string('invoice_street');
            $table->string('invoice_street_number');
            $table->string('invoice_zip');
            $table->string('invoice_city');
            $table->string('invoice_country')->default('CH');

            // Shipping address (optional)
            $table->boolean('use_invoice_address')->default(true);
            $table->string('shipping_company')->nullable();
            $table->string('shipping_street')->nullable();
            $table->string('shipping_street_number')->nullable();
            $table->string('shipping_zip')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_country')->nullable();

            // Order details
            $table->decimal('total', 10, 2);
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
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
