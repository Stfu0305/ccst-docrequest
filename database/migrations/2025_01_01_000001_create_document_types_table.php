<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `document_types` table.
     * It stores the 5 document types students can request (REG, COG, COE, TOR, CGMC).
     * The registrar can activate/deactivate types. If `has_school_year` is true,
     * the request form shows extra dropdowns for Assessment Year and Semester.
     */
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // e.g. "Registration Form"
            $table->string('code', 50)->unique();           // REG, COG, COE, TOR, CGMC — short code used internally
            $table->decimal('fee', 8, 2);                   // Processing fee in PHP (e.g. 80.00)
            $table->boolean('has_school_year')->default(false); // If true: show Assessment Year + Semester on request form
            $table->integer('processing_days');             // Estimated days to fulfill the request
            $table->text('description')->nullable();        // What this document is — shown to students
            $table->boolean('is_active')->default(true);    // If false: hidden from new request form
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
