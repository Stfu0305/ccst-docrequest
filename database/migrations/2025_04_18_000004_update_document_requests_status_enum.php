<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update status enum to simplified statuses
        DB::statement("ALTER TABLE document_requests MODIFY COLUMN status ENUM('pending', 'ready_for_pickup', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Rollback to original statuses
        DB::statement("ALTER TABLE document_requests MODIFY COLUMN status ENUM('pending', 'payment_method_set', 'payment_uploaded', 'payment_verified', 'payment_rejected', 'processing', 'ready_for_pickup', 'received', 'cancelled') DEFAULT 'pending'");
    }
};