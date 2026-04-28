<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Student verification fields
            $table->boolean('is_verified')->default(false)->after('profile_photo');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            
            // Student ID upload
            $table->string('student_id_photo', 500)->nullable()->after('profile_photo');
            
            // Walk-in student fields
            $table->boolean('is_walk_in')->default(false)->after('is_verified');
            $table->foreignId('walk_in_registered_by')->nullable()->after('is_walk_in')->constrained('users')->nullOnDelete();
            $table->timestamp('walk_in_registered_at')->nullable()->after('walk_in_registered_by');
            
            // Remove cashier from role enum
            // Note: MySQL doesn't support removing enum values directly. We'll modify in a separate step.
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_verified',
                'verified_at',
                'verified_by',
                'student_id_photo',
                'is_walk_in',
                'walk_in_registered_by',
                'walk_in_registered_at'
            ]);
        });
    }
};