<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->boolean('is_printable')->default(false)->after('has_school_year');
        });
        
        // Update existing document types
        DB::table('document_types')->whereIn('code', ['COE', 'CGMC'])->update(['is_printable' => true]);
        DB::table('document_types')->whereIn('code', ['REG', 'COG', 'TOR'])->update(['is_printable' => false]);
    }

    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn('is_printable');
        });
    }
};