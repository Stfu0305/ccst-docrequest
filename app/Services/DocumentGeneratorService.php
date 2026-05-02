<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\DocumentRequestItem;
use Carbon\Carbon;

class DocumentGeneratorService
{
    /**
     * Generate a document from a .docx template
     *
     * @param DocumentRequest $request
     * @param DocumentType $docType
     * @param DocumentRequestItem $item
     * @param array $customData
     * @return string File path to the generated document
     */
    public function generateFromTemplate(DocumentRequest $request, DocumentType $docType, DocumentRequestItem $item, array $customData = [])
    {
        if (!$docType->template_path || !Storage::exists($docType->template_path)) {
            throw new \Exception("Template not found for document type: " . $docType->name);
        }

        $templatePath = Storage::path($docType->template_path);
        $templateProcessor = new TemplateProcessor($templatePath);

        // Prepare data
        $student = $request->user;
        $defaultData = [
            'student_name' => strtoupper($request->full_name),
            'student_number' => $request->student_number,
            'grade_level' => $student->grade_level ?? 'N/A',
            'strand' => $student->strand ?? 'N/A',
            'section' => $student->section ?? 'N/A',
            'school_year' => $item->assessment_year ?? $this->getCurrentSchoolYear(),
            'semester' => $item->semester ?? 'N/A',
            'current_date' => Carbon::now()->format('F j, Y'),
            'date' => Carbon::now()->format('F j, Y'),
            'completion_date' => Carbon::now()->format('F j, Y'),
            'reference_number' => $request->reference_number,
            'purpose' => 'for the request of new issuance',
        ];

        // Merge custom data if provided
        $data = array_merge($defaultData, $customData);

        // Replace placeholders (both {tag} and ${TAG} for compatibility)
        foreach ($data as $key => $value) {
            $templateProcessor->setValue($key, $value);
            $templateProcessor->setValue(strtoupper($key), $value);
            $templateProcessor->setValue('{' . $key . '}', $value);
        }

        // Special logic for grade level/strand combined if needed
        $templateProcessor->setValue('GRADE_STRAND', ($student->grade_level ?? '') . ' - ' . ($student->strand ?? ''));

        // Save generated file
        $fileName = $request->reference_number . '_' . $docType->code . '_' . time() . '.docx';
        $directory = 'generated_documents';
        
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        $savePath = Storage::path($directory . '/' . $fileName);
        $templateProcessor->saveAs($savePath);

        return $directory . '/' . $fileName;
    }

    /**
     * Get current school year
     */
    private function getCurrentSchoolYear()
    {
        $year = date('Y');
        $month = date('n');
        
        if ($month < 6) {
            return ($year - 1) . '-' . $year;
        }
        
        return $year . '-' . ($year + 1);
    }
}
