<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `document_requests` table — the central table of the whole system.
     * Every document request a student submits is one row here.
     *
     * Important design decisions:
     * - `reference_number` is generated AFTER insert (needs the ID), so it's nullable at first.
     *   The controller updates it immediately after creating the row.
     * - Student info columns (student_number, full_name, etc.) are SNAPSHOTS taken at
     *   submission time. Even if the student updates their profile later, the request
     *   records what was true when they submitted.
     * - `payment_method` is null until the student picks a method on the summary page.
     * - `appointment_id` is a nullable foreign key — set when student books a pickup slot.
     *   We use `constrained` with `nullOnDelete` so deleting an appointment doesn't
     *   break the document request row.
     */
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 30)->nullable()->unique(); // DQST-2026-00042 — set right after insert
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Which student made this request
            $table->string('student_number', 50);           // Snapshot of student_number at submission
            $table->string('full_name');                    // Snapshot of name at submission
            $table->string('contact_number', 20);           // Snapshot of contact_number at submission
            $table->string('course_program');               // Strand or course
            $table->string('year_level', 20);               // Grade 11 / Grade 12
            $table->string('section', 50);                  // Section name
            $table->decimal('total_fee', 10, 2);            // Sum of all item fees — calculated in controller
            $table->enum('payment_method', ['gcash', 'bank_transfer', 'cash'])->nullable(); // Set on summary page
            $table->enum('status', [
                'pending',              // Just submitted — no payment method chosen yet
                'payment_method_set',   // Student chose a payment method
                'payment_uploaded',     // Student uploaded GCash or bank receipt
                'payment_verified',     // Cashier confirmed payment
                'payment_rejected',     // Cashier rejected the uploaded proof
                'processing',           // Registrar is preparing the documents
                'ready_for_pickup',     // Documents ready — claiming number generated
                'received',             // Student physically picked up documents
                'cancelled',            // Student cancelled the request
            ])->default('pending');
            $table->text('remarks')->nullable();            // Registrar notes or rejection reason
            $table->string('claiming_number', 20)->nullable(); // CLM-A4KZ29 — generated when ready_for_pickup
            $table->unsignedBigInteger('appointment_id')->nullable();
            // We do NOT add the foreign key constraint here because `appointments` table doesn't exist yet.
            // The foreign key for appointment_id is added in a SEPARATE migration file AFTER
            // both document_requests and appointments tables have been created.
            // This breaks the circular dependency: document_requests ↔ appointments.
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // Registrar who handled this
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
