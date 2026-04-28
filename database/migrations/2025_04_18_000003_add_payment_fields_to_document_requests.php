<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // Payment tracking fields
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->string('receipt_number', 50)->nullable()->after('paid_at');
            $table->string('cashier_name', 100)->nullable()->after('receipt_number');
            
            // Walk-in tracking
            $table->boolean('is_walk_in')->default(false)->after('payment_status');
            $table->foreignId('walk_in_handled_by')->nullable()->after('is_walk_in')->constrained('users')->nullOnDelete();
            
            // Printable document flag (derived from document type, but cached for performance)
            $table->boolean('is_printable')->default(false)->after('is_walk_in');
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'paid_at',
                'receipt_number',
                'cashier_name',
                'is_walk_in',
                'walk_in_handled_by',
                'is_printable'
            ]);
        });
    }
};