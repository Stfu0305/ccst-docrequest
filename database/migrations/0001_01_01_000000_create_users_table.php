<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `users` table — the foundation of the whole system.
     * Every student, registrar, and cashier is stored here.
     * The `role` column tells Laravel which dashboard to redirect to after login.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                           // BIGINT UNSIGNED, auto-increment primary key
            $table->string('name');                                 // Full name
            $table->string('email')->unique();                      // Login email — must be unique across all users
            $table->timestamp('email_verified_at')->nullable();     // Laravel email verification — null until verified
            $table->string('password');                             // Bcrypt hashed password
            $table->enum('role', ['student', 'registrar', 'cashier']); // Which dashboard this user sees
            $table->string('student_number', 50)->nullable();       // School ID — only students have this
            $table->string('contact_number', 20)->nullable();       // Mobile number
            $table->text('address')->nullable();                    // Home address
            $table->string('strand', 100)->nullable();              // ABM, ICT, HUMSS, STEM, GAS, HE — students only
            $table->string('grade_level', 20)->nullable();          // Grade 11 or Grade 12 — students only
            $table->string('section', 50)->nullable();              // Section name — students only
            $table->string('profile_photo', 500)->nullable();       // Path to uploaded profile photo
            $table->rememberToken();                                // Laravel "remember me" cookie token
            $table->timestamps();                                   // created_at and updated_at
        });

        // These two tables are required by Laravel's built-in password reset system (included with Breeze).
        // We keep them exactly as Laravel expects — do not change these.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * `down()` is called when you run `php artisan migrate:rollback`.
     * It drops the tables in reverse order so foreign keys don't cause errors.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
