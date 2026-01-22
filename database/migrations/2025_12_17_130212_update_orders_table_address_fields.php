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
        Schema::table('orders', function (Blueprint $table) {
            // Remove old customer fields
            $table->dropColumn(['customer_name', 'customer_email', 'customer_phone']);

            // Add invoice address fields (matching form)
            $table->string('invoice_salutation')->nullable()->after('uuid');
            $table->string('invoice_firstname')->after('invoice_salutation');
            $table->string('invoice_lastname')->after('invoice_firstname');
            $table->string('invoice_email')->after('invoice_country');
            $table->string('invoice_phone')->nullable()->after('invoice_email');

            // Add shipping address fields (matching form)
            $table->string('shipping_salutation')->nullable()->after('use_invoice_address');
            $table->string('shipping_firstname')->nullable()->after('shipping_salutation');
            $table->string('shipping_lastname')->nullable()->after('shipping_firstname');

            // Drop invoice_company and shipping_company (not in forms)
            $table->dropColumn(['invoice_company', 'shipping_company']);

            // Add payment reference for tracking
            $table->string('payment_reference')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Restore old customer fields
            $table->string('customer_name')->after('uuid');
            $table->string('customer_email')->after('customer_name');
            $table->string('customer_phone')->nullable()->after('customer_email');

            // Remove new invoice fields
            $table->dropColumn([
                'invoice_salutation',
                'invoice_firstname',
                'invoice_lastname',
                'invoice_email',
                'invoice_phone',
            ]);

            // Remove new shipping fields
            $table->dropColumn([
                'shipping_salutation',
                'shipping_firstname',
                'shipping_lastname',
            ]);

            // Restore company fields
            $table->string('invoice_company')->nullable()->after('customer_phone');
            $table->string('shipping_company')->nullable()->after('use_invoice_address');

            // Remove payment reference
            $table->dropColumn('payment_reference');
        });
    }
};
