<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Temporarily disable foreign key checks so truncate works.
        // document_types is referenced by document_request_items, which blocks plain truncate().
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DocumentType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $types = [
            [
                'name'             => 'Registration Form',
                'code'             => 'REG',
                'fee'              => 80.00,
                'has_school_year'  => true,   // shows Assessment Year + Semester dropdowns on the request form
                'processing_days'  => 3,
                'description'      => 'Official registration form for the current school year.',
                'is_active'        => true,
            ],
            [
                'name'             => 'Certificate of Grades',
                'code'             => 'COG',
                'fee'              => 75.00,
                'has_school_year'  => true,
                'processing_days'  => 3,
                'description'      => 'Official record of grades for a specific semester.',
                'is_active'        => true,
            ],
            [
                'name'             => 'Certificate of Enrollment',
                'code'             => 'COE',
                'fee'              => 50.00,
                'has_school_year'  => false,
                'processing_days'  => 2,
                'description'      => 'Certifies that the student is currently enrolled.',
                'is_active'        => true,
            ],
            [
                'name'             => 'Transcript of Records',
                'code'             => 'TOR',
                'fee'              => 70.00,
                'has_school_year'  => false,
                'processing_days'  => 7,
                'description'      => 'Complete academic record from Grade 11 to Grade 12.',
                'is_active'        => true,
            ],
            [
                'name'             => 'Good Moral Certificate',
                'code'             => 'CGMC',
                'fee'              => 50.00,
                'has_school_year'  => false,
                'processing_days'  => 2,
                'description'      => 'Certificate of good moral character issued by the school.',
                'is_active'        => true,
            ],
        ];

        foreach ($types as $type) {
            DocumentType::create($type);
        }
    }
}