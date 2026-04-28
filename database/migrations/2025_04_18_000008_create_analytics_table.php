<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_requests')->default(0);
            $table->integer('total_appointments')->default(0);
            $table->integer('total_completed')->default(0);
            $table->integer('total_cancelled')->default(0);
            $table->string('peak_hour', 20)->nullable();
            $table->string('peak_day', 20)->nullable();
            $table->json('document_type_counts')->nullable();
            $table->decimal('average_processing_time', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};