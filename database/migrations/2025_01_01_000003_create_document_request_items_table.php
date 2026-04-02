<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `document_request_items` table.
     * One document request can include MULTIPLE document types.
     * Example: a student might request 1 COE + 2 COG in the same request.
     * Each of those is one row in this table.
     *
     * `fee` is a snapshot of the price at submission time — if admin
     * changes the fee later, old requests are unaffected.
     * `assessment_year` and `semester` are only filled for document types
     * where `has_school_year` = true (REG and COG).
     */
    public function up(): void
    {
        Schema::create('document_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained('document_requests')->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained('document_types')->restrictOnDelete();
            $table->integer('copies');                          // How many copies the student needs
            $table->string('assessment_year', 20)->nullable();  // e.g. "A.Y. 2025-2026" — null if not applicable
            $table->string('semester', 20)->nullable();         // "1st Sem" or "2nd Sem" — null if not applicable
            $table->decimal('fee', 8, 2);                       // Price snapshot at time of request
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_request_items');
    }
};
