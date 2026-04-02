<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This creates the `payment_settings` table.
     * It stores the cashier's payment account details — one row per payment method.
     * The seeder creates 4 rows: gcash, bdo, bpi, cash.
     *
     * The cashier edits these from the Settings page (/cashier/settings).
     * No developer involvement needed to update GCash numbers or bank details.
     *
     * `is_active` controls visibility:
     * - If gcash is_active = false → GCash option hidden on the student's summary page
     * - Cash is always active and cannot be deactivated (enforced in the controller)
     *
     * `method` has 4 values: gcash, bdo, bpi, cash
     * BDO and BPI are separate rows so the cashier can manage them independently.
     */
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('method', ['gcash', 'bdo', 'bpi', 'cash'])->unique(); // One row per method
            $table->string('account_name')->default('');            // Name on the GCash/bank account
            $table->string('account_number', 100)->default('');     // GCash mobile or bank account number
            $table->string('bank_name', 100)->nullable();           // "BDO" or "BPI" — null for gcash/cash
            $table->string('branch')->nullable();                   // Bank branch — optional
            $table->text('extra_info')->nullable();                 // Cash: office address + hours; others: extra notes
            $table->boolean('is_active')->default(true);            // If false: this method hidden from students
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // Last cashier to edit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
