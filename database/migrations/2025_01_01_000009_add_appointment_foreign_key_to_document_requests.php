<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds the foreign key constraint for `appointment_id` on
     * the `document_requests` table. We could not add it in the original
     * document_requests migration because `appointments` didn't exist yet.
     *
     * Now that both tables exist, we can safely add the constraint.
     * `nullOnDelete` means: if an appointment is deleted, this column is set to null
     * (not deleting the whole document request — that would be too destructive).
     */
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
        });
    }
};
