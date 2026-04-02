<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `appointments` table.
     * Students book an appointment after their request is marked ready_for_pickup.
     * They pick a date using Flatpickr and a time slot from the available options.
     *
     * `claimed_at` is set by the registrar when the student physically arrives and
     * shows their claiming number. At that point, status also becomes 'received'.
     *
     * Note on the circular dependency fix:
     * `document_requests` has an `appointment_id` column (added as a plain integer column
     * with no foreign key constraint in that migration). After BOTH tables are created,
     * a SEPARATE migration file adds the foreign key constraint. This is the standard
     * Laravel pattern for circular/mutual references between two tables.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained('document_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // The student
            $table->foreignId('time_slot_id')->constrained('time_slots')->restrictOnDelete();
            $table->date('appointment_date');           // The chosen pickup date
            $table->enum('status', ['scheduled', 'completed', 'missed', 'cancelled'])->default('scheduled');
            $table->timestamp('claimed_at')->nullable(); // Set when registrar confirms pickup
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
