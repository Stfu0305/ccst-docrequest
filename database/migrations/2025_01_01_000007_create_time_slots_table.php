<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `time_slots` table.
     * Time slots are the available pickup windows — e.g. "8:00 AM – 9:00 AM".
     * The registrar can add, edit, or deactivate slots from the appointments page.
     *
     * `max_capacity` limits how many appointments can be booked in one slot on one day.
     * Default is 5. This prevents the registrar office from being overwhelmed.
     *
     * We create time_slots BEFORE appointments because appointments references time_slots.
     */
    public function up(): void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('label', 100);           // Display label: "8:00 AM – 9:00 AM"
            $table->time('start_time');             // Slot start time
            $table->time('end_time');               // Slot end time
            $table->integer('max_capacity')->default(5); // Max appointments per slot per day
            $table->boolean('is_active')->default(true); // Registrar can deactivate slots
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
