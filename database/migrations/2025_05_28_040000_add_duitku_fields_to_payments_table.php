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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('duitku_payment_id')->nullable()->after('nowpayments_order_id');
            $table->string('duitku_order_id')->nullable()->after('duitku_payment_id');
            $table->string('payment_gateway')->default('nowpayments')->after('duitku_order_id'); // nowpayments, duitku
            $table->string('payment_method')->nullable()->after('payment_gateway'); // SP, OV, DA, etc for Duitku
            $table->string('va_number')->nullable()->after('pay_address');
            $table->text('qr_string')->nullable()->after('va_number');
            $table->string('payment_url')->nullable()->after('qr_string');
            $table->timestamp('expires_at')->nullable()->after('payment_updated_at');
            
            // Add indexes for better performance
            $table->index('duitku_payment_id');
            $table->index('duitku_order_id');
            $table->index('payment_gateway');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['duitku_payment_id']);
            $table->dropIndex(['duitku_order_id']);
            $table->dropIndex(['payment_gateway']);
            $table->dropIndex(['expires_at']);
            
            $table->dropColumn([
                'duitku_payment_id',
                'duitku_order_id',
                'payment_gateway',
                'payment_method',
                'va_number',
                'qr_string',
                'payment_url',
                'expires_at'
            ]);
        });
    }
};