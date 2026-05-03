<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Ready to Print (can book appointment immediately)
        $documents = [
            ['code' => 'COE', 'name' => 'Certificate of Enrollment', 'fee' => 50.00, 'processing_days' => 0, 'has_school_year' => false, 'is_printable' => true, 'is_active' => true],
            ['code' => 'CGMC', 'name' => 'Certificate of Good Moral Character', 'fee' => 50.00, 'processing_days' => 0, 'has_school_year' => false, 'is_printable' => true, 'is_active' => true],
            
            // Not Ready to Print (needs processing)
            ['code' => 'REG', 'name' => 'Registration Form', 'fee' => 80.00, 'processing_days' => 2, 'has_school_year' => true, 'is_printable' => false, 'is_active' => true],
            ['code' => 'COG', 'name' => 'Certificate of Grades', 'fee' => 75.00, 'processing_days' => 2, 'has_school_year' => true, 'is_printable' => false, 'is_active' => true],
            ['code' => 'TOR', 'name' => 'Transcript of Records', 'fee' => 70.00, 'processing_days' => 3, 'has_school_year' => false, 'is_printable' => false, 'is_active' => true],
        ];

        foreach ($documents as $doc) {
            DocumentType::updateOrCreate(
                ['code' => $doc['code']],
                $doc
            );
        }
    }
}