<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `notifications` table.
     * This is the STANDARD Laravel database notifications table.
     * We do NOT change any column names here — Laravel's Notification system
     * expects this exact structure.
     *
     * Each notification sent via the database channel creates one row here.
     * `read_at` is null for unread notifications — used to show the bell icon count.
     * `data` is a JSON column containing the notification payload
     * (e.g. reference number, message, link).
     *
     * This table is what powers the bell icon in the top navigation bar.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();          // UUID — Laravel default for notifications
            $table->string('type');                 // Full class name, e.g. App\Notifications\RequestSubmittedNotification
            $table->morphs('notifiable');           // Creates notifiable_type (VARCHAR) + notifiable_id (BIGINT) columns
            $table->text('data');                   // JSON payload — message, links, ref number, etc.
            $table->timestamp('read_at')->nullable(); // Null = unread. Set when student views notification.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
