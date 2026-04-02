<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `official_receipts` table.
     * A receipt row is created when the cashier:
     *   - Verifies a GCash or bank transfer proof, OR
     *   - Clicks "Mark as Paid" for a cash payment
     *
     * The `file_path` column stores the path to the dompdf-generated PDF.
     * - For GCash/Bank: PDF is generated and stored — student downloads it from history.
     * - For Cash: PDF is generated and the cashier's browser opens a print dialog.
     *
     * `receipt_number` format: OR-2026-00042
     * It's nullable at first because the number is generated after insert (needs the ID).
     * The controller updates it immediately after the row is created.
     */
    public function up(): void
    {
        Schema::create('official_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 30)->nullable()->unique(); // OR-2026-00042 — set right after insert
            $table->foreignId('document_request_id')->constrained('document_requests')->cascadeOnDelete();
            $table->enum('payment_method', ['gcash', 'bank_transfer', 'cash']);
            $table->decimal('amount_paid', 10, 2);                  // Confirmed total by cashier
            $table->string('reference_number', 100)->nullable();    // GCash/bank ref — null for cash payments
            $table->foreignId('issued_by')->constrained('users')->restrictOnDelete(); // Cashier who generated this
            $table->timestamp('issued_at');                         // When the receipt was generated
            $table->string('file_path', 500)->nullable();           // Path to PDF in storage/app/private/receipts/
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('official_receipts');
    }
};
