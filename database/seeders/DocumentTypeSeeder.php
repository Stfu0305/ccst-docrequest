<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            ['code' => 'REG', 'name' => 'Registration Form', 'fee' => 80.00, 'processing_days' => 3, 'has_school_year' => true, 'is_printable' => false],
            ['code' => 'COG', 'name' => 'Certificate of Grades', 'fee' => 75.00, 'processing_days' => 3, 'has_school_year' => true, 'is_printable' => false],
            ['code' => 'COE', 'name' => 'Certificate of Enrollment', 'fee' => 50.00, 'processing_days' => 2, 'has_school_year' => false, 'is_printable' => true],
            ['code' => 'TOR', 'name' => 'Transcript of Records', 'fee' => 70.00, 'processing_days' => 7, 'has_school_year' => false, 'is_printable' => false],
            ['code' => 'CGMC', 'name' => 'Good Moral Certificate', 'fee' => 50.00, 'processing_days' => 2, 'has_school_year' => false, 'is_printable' => true],
        ];

        foreach ($documents as $doc) {
            DocumentType::updateOrCreate(
                ['code' => $doc['code']],
                $doc
            );
        }
    }
}