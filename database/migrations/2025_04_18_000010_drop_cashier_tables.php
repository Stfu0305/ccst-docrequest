<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop cashier-related tables (already backed up)
        Schema::dropIfExists('payment_proofs');
        Schema::dropIfExists('payment_settings');
        Schema::dropIfExists('official_receipts');
    }

    public function down(): void
    {
        // Cannot restore automatically - restore from backup
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained();
            $table->string('file_path');
            $table->string('original_filename');
            $table->integer('file_size_kb')->nullable();
            $table->decimal('amount_declared', 10, 2)->nullable();
            $table->string('reference_number')->nullable();
            $table->boolean('is_resubmission')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
        
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('method', ['gcash', 'bdo', 'bpi', 'cash']);
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->text('extra_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
        
        Schema::create('official_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('document_request_id')->constrained();
            $table->enum('payment_method', ['gcash', 'bank_transfer', 'cash']);
            $table->decimal('amount_paid', 10, 2);
            $table->string('reference_number')->nullable();
            $table->foreignId('issued_by')->constrained('users');
            $table->timestamp('issued_at');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }
};