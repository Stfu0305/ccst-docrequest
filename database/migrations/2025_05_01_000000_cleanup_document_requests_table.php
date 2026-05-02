<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // Remove payment-related columns
            if (Schema::hasColumn('document_requests', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('document_requests', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('document_requests', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
            if (Schema::hasColumn('document_requests', 'receipt_number')) {
                $table->dropColumn('receipt_number');
            }
            if (Schema::hasColumn('document_requests', 'cashier_name')) {
                $table->dropColumn('cashier_name');
            }
            
            // Add document_type column (store comma-separated document codes)
            $table->string('document_types', 255)->nullable()->after('total_fee');
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('document_types');
            $table->enum('payment_method', ['gcash', 'bank_transfer', 'cash'])->nullable();
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->string('receipt_number', 50)->nullable();
            $table->string('cashier_name', 100)->nullable();
        });
    }
};
