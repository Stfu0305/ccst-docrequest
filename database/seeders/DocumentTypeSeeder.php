<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            ['code' => 'REG', 'name' => 'Registration Form', 'fee' => 100.00, 'processing_days' => 3, 'has_school_year' => true, 'is_printable' => false],
            ['code' => 'COG', 'name' => 'Certificate of Grades', 'fee' => 100.00, 'processing_days' => 3, 'has_school_year' => true, 'is_printable' => false],
            ['code' => 'COE', 'name' => 'Certificate of Enrollment', 'fee' => 100.00, 'processing_days' => 2, 'has_school_year' => false, 'is_printable' => true, 'template_path' => 'templates/certificateofenrollment.docx'],
            ['code' => 'TOR', 'name' => 'FORM 138', 'fee' => 100.00, 'processing_days' => 7, 'has_school_year' => true, 'is_printable' => false],
            ['code' => 'CGMC', 'name' => 'Good Moral Certificate', 'fee' => 100.00, 'processing_days' => 2, 'has_school_year' => false, 'is_printable' => true],
            ['code' => 'CGWA', 'name' => 'Certificate of General Weighted Average', 'fee' => 100.00, 'processing_days' => 3, 'has_school_year' => true, 'is_printable' => true],
            ['code' => 'CR', 'name' => 'Certificate of Ranking', 'fee' => 100.00, 'processing_days' => 3, 'has_school_year' => true, 'is_printable' => true],
            ['code' => 'CLI', 'name' => 'Certificate of Lost I.D', 'fee' => 100.00, 'processing_days' => 2, 'has_school_year' => true, 'is_printable' => true, 'template_path' => 'templates/certificcateoflostid.docx'],
            ['code' => 'CC', 'name' => 'Certificate of Completion', 'fee' => 100.00, 'processing_days' => 3, 'has_school_year' => true, 'is_printable' => true, 'template_path' => 'templates/certificateofcompletion.docx'],
            ['code' => 'CG', 'name' => 'Certificate of Graduation', 'fee' => 100.00, 'processing_days' => 3, 'has_school_year' => true, 'is_printable' => true],
        ];

        foreach ($documents as $doc) {
            DocumentType::updateOrCreate(
                ['code' => $doc['code']],
                $doc
            );
        }
    }
}