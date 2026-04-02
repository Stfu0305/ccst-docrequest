<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `announcements` table.
     * There are only 2 announcement types in the system:
     *   - 'announcement': general announcements shown to students on the dashboard
     *   - 'transaction_days': notices about when the registrar office is open
     *
     * The seeder creates exactly 2 rows — one of each type.
     * The registrar edits the content and clicks "Publish" to make it visible.
     * `is_published` = false means students don't see it yet.
     *
     * These are NOT a list of many announcements — it's just 2 configurable
     * text blocks shown as cards on the student dashboard.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['announcement', 'transaction_days'])->unique(); // One row per type
            $table->text('content');                                // The announcement text
            $table->boolean('is_published')->default(false);       // If true: visible on student dashboard
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();         // When it was last published
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
