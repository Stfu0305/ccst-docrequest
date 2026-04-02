<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `payment_proofs` table.
     * When a student uploads a GCash or bank transfer screenshot, it goes here.
     * Cash payments do NOT create a row here — cashier marks them directly.
     *
     * Key columns:
     * - `file_path`: stored in storage/app/private/payments/ — NOT publicly accessible.
     *   Files are served only through an authenticated controller route.
     * - `amount_declared`: what the student SAYS they paid — cashier compares this
     *   against `total_fee` on the document request. A ₱1+ mismatch shows a warning.
     * - `reference_number`: GCash/bank transaction reference. Checked for duplicates
     *   against other verified payments before the upload is accepted.
     * - `is_resubmission`: true if this replaced a previously rejected proof.
     *   Shows a yellow RESUBMITTED badge in the cashier's list.
     * - `verified_at`: null means cashier has not acted yet. If null, student can
     *   still re-upload. Once set, the payment is locked.
     */
    public function up(): void
    {
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained('document_requests')->cascadeOnDelete();
            $table->string('file_path', 500);                       // Path in private storage
            $table->string('original_filename');                    // Original name of the uploaded file
            $table->integer('file_size_kb')->nullable();            // File size in KB — for display
            $table->decimal('amount_declared', 10, 2)->nullable();  // What the student says they paid
            $table->string('reference_number', 100)->nullable();    // GCash or bank transaction reference
            $table->boolean('is_resubmission')->default(false);     // True if re-uploaded after rejection
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete(); // Cashier who acted
            $table->timestamp('verified_at')->nullable();           // When cashier verified or rejected
            $table->text('rejection_reason')->nullable();           // Sent to student by email on rejection
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
    }
};
