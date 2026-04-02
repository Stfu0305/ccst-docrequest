<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `status_logs` table.
     * Every time a document request's status changes, a new row is inserted here.
     * This gives you a complete audit trail — you can see the full history of
     * every request: who changed it, when, from what, to what.
     *
     * `old_status` is null on the very first log entry (when request is first created).
     * `notes` stores extra context — e.g. the rejection reason when cashier rejects,
     * or a note from the registrar when processing.
     *
     * This table is append-only — rows are never updated or deleted.
     * We only have `created_at` here, but we keep `updated_at` for Laravel compatibility.
     */
    public function up(): void
    {
        Schema::create('status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained('document_requests')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete(); // Who triggered the change
            $table->string('old_status', 50)->nullable();   // Previous status — null on first log
            $table->string('new_status', 50);               // New status value
            $table->text('notes')->nullable();              // Optional reason or context
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_logs');
    }
};
