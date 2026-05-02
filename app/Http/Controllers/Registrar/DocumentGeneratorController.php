<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\GeneratedDocument;
use App\Traits\SendsDatabaseNotifications;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DocumentGeneratorController extends Controller
{
    use SendsDatabaseNotifications;

    /**
     * Show preview/edit form before generation
     */
    public function prepare($requestId, $documentTypeId)
    {
        $documentRequest = DocumentRequest::with(['user', 'items.documentType'])
            ->findOrFail($requestId);
        
        $documentType = DocumentType::findOrFail($documentTypeId);
        
        $requestedItem = $documentRequest->items->firstWhere('document_type_id', $documentTypeId);
        if (!$requestedItem) {
            return back()->with('error', 'This document was not requested.');
        }
        
        $data = $this->prepareDocumentData($documentRequest, $documentType, $requestedItem);
        
        return view('registrar.documents.prepare', compact('documentRequest', 'documentType', 'data', 'requestId', 'documentTypeId'));
    }

    /**
     * Generate a single document
     * GET/POST /registrar/requests/{id}/generate/{document_type_id}
     */
    public function generate(Request $request, $requestId, $documentTypeId)
    {
        $documentRequest = DocumentRequest::with(['user', 'items.documentType'])
            ->findOrFail($requestId);
        
        $documentType = DocumentType::findOrFail($documentTypeId);
        
        // Check if this document type was requested
        $requestedItem = $documentRequest->items->firstWhere('document_type_id', $documentTypeId);
        if (!$requestedItem) {
            return back()->with('error', 'This document was not requested by the student.');
        }
        
        // Get custom data from form if it exists
        $customData = $request->except(['_token']);

        // 1. CHECK FOR .DOCX TEMPLATE
        if ($documentType->template_path && Storage::exists($documentType->template_path)) {
            try {
                $service = new \App\Services\DocumentGeneratorService();
                $filePath = $service->generateFromTemplate($documentRequest, $documentType, $requestedItem, $customData);
                
                // Record the generation
                GeneratedDocument::create([
                    'document_request_id' => $documentRequest->id,
                    'document_type_id' => $documentType->id,
                    'file_path' => $filePath,
                    'printed_at' => now(),
                    'printed_by' => Auth::id(),
                ]);

                // Notify
                $message = "✅ Document '{$documentType->name}' for request {$documentRequest->reference_number} has been generated (.docx).";
                $this->sendNotificationToCurrentUser($message, route('registrar.requests.show', $documentRequest->id));
                session()->flash('check_notifications', true);

                return response()->download(Storage::path($filePath));
            } catch (\Exception $e) {
                return back()->with('error', 'Error generating .docx: ' . $e->getMessage());
            }
        }

        // 2. FALLBACK TO PDF
        // Prepare data for template
        $data = $this->prepareDocumentData($documentRequest, $documentType, $requestedItem);
        
        // Load the appropriate template
        $pdf = $this->loadTemplate($documentType->code, $data);
        
        // Store the generated document record
        $generatedDocument = GeneratedDocument::create([
            'document_request_id' => $documentRequest->id,
            'document_type_id' => $documentType->id,
            'printed_by' => Auth::id(),
        ]);
        
        // Save PDF to storage
        $fileName = $documentRequest->reference_number . '_' . $documentType->code . '_' . time() . '.pdf';
        $filePath = 'generated_documents/' . $fileName;
        Storage::disk('local')->put($filePath, $pdf->output());
        
        $generatedDocument->update([
            'file_path' => $filePath,
            'printed_at' => now(),
        ]);
        
        // Send notification to registrar
        $message = "✅ Document '{$documentType->name}' for request {$documentRequest->reference_number} has been generated (PDF).";
        $this->sendNotificationToCurrentUser($message, route('registrar.requests.show', $documentRequest->id));
        session()->flash('check_notifications', true);
        
        // Return PDF for download/print
        return $pdf->stream($fileName);
    }
    
    /**
     * Generate all printable documents for a request
     * GET /registrar/requests/{id}/generate-all
     */
    public function generateAll($requestId)
    {
        $documentRequest = DocumentRequest::with(['items.documentType', 'user'])
            ->findOrFail($requestId);
        
        $generatedFiles = [];
        
        foreach ($documentRequest->items as $item) {
            if (!$item->documentType->is_printable) {
                continue; // Skip non-printable documents
            }
            
            $data = $this->prepareDocumentData($documentRequest, $item->documentType, $item);
            $pdf = $this->loadTemplate($item->documentType->code, $data);
            
            $fileName = $documentRequest->reference_number . '_' . $item->documentType->code . '.pdf';
            $filePath = 'generated_documents/' . $fileName;
            Storage::disk('local')->put($filePath, $pdf->output());
            $generatedFiles[] = $filePath;
            
            GeneratedDocument::create([
                'document_request_id' => $documentRequest->id,
                'document_type_id' => $item->document_type_id,
                'file_path' => $filePath,
                'printed_at' => now(),
                'printed_by' => Auth::id(),
            ]);
        }
        
        if (empty($generatedFiles)) {
            return back()->with('error', 'No printable documents found for this request.');
        }
        
        // Create ZIP file if multiple documents
        if (count($generatedFiles) > 1) {
            $zipFileName = $documentRequest->reference_number . '_documents.zip';
            $zipPath = storage_path('app/private/generated_documents/' . $zipFileName);
            
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                foreach ($generatedFiles as $file) {
                    $fullPath = storage_path('app/private/' . $file);
                    $zip->addFile($fullPath, basename($file));
                }
                $zip->close();
            }
            
            $message = "✅ All documents for request {$documentRequest->reference_number} have been generated.";
            $this->sendNotificationToCurrentUser($message, route('registrar.requests.show', $documentRequest->id));
            session()->flash('check_notifications', true);
            
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }
        
        // Single file - return PDF
        $filePath = storage_path('app/private/' . $generatedFiles[0]);
        $message = "✅ Document for request {$documentRequest->reference_number} has been generated.";
        $this->sendNotificationToCurrentUser($message, route('registrar.requests.show', $documentRequest->id));
        session()->flash('check_notifications', true);
        
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    
    /**
     * Preview document before printing
     * GET /registrar/requests/{id}/preview/{document_type_id}
     */
    public function preview($requestId, $documentTypeId)
    {
        $documentRequest = DocumentRequest::with(['user', 'items.documentType'])
            ->findOrFail($requestId);
        
        $documentType = DocumentType::findOrFail($documentTypeId);
        
        $requestedItem = $documentRequest->items->firstWhere('document_type_id', $documentTypeId);
        if (!$requestedItem) {
            return back()->with('error', 'This document was not requested by the student.');
        }
        
        $data = $this->prepareDocumentData($documentRequest, $documentType, $requestedItem);
        
        $pdf = $this->loadTemplate($documentType->code, $data);
        
        return $pdf->stream('preview.pdf');
    }
    
    /**
     * Prepare data for document templates
     */
    private function prepareDocumentData($documentRequest, $documentType, $requestedItem)
    {
        $user = $documentRequest->user;
        
        // Format date
        $currentDate = now()->format('F d, Y');
        
        // Get grades if applicable (placeholder - will be implemented with grade management)
        $grades = $this->getStudentGrades($user);
        
        // Get school year
        $schoolYear = $this->getCurrentSchoolYear();
        
        return [
            // Student Information
            'student_name' => $documentRequest->full_name,
            'student_number' => $documentRequest->student_number,
            'strand' => $user->strand,
            'grade_level' => $user->grade_level,
            'section' => $user->section,
            'contact_number' => $user->contact_number,
            'address' => $user->address,
            
            // Document-specific
            'document_name' => $documentType->name,
            'document_code' => $documentType->code,
            'copies' => $requestedItem->copies,
            'assessment_year' => $requestedItem->assessment_year ?? 'N/A',
            'semester' => $requestedItem->semester ?? 'N/A',
            'purpose' => $requestedItem->purpose ?? 'For school requirements',
            
            // Request Information
            'reference_number' => $documentRequest->reference_number,
            'request_date' => $documentRequest->created_at->format('F d, Y'),
            'total_fee' => number_format($documentRequest->total_fee, 2),
            
            // System Information
            'current_date' => $currentDate,
            'school_year' => $schoolYear,
            'registrar_name' => Auth::user()->name,
            'registrar_signature' => 'Registrar\'s Signature',
            
            // Grades (placeholder)
            'grades' => $grades,
            'general_average' => $grades['average'] ?? 'N/A',
            
            // Footer
            'footer_text' => 'This document is issued by the CCST Registrar Office for official purposes only.',
        ];
    }
    
    /**
     * Load the appropriate PDF template
     */
    private function loadTemplate($documentCode, $data)
    {
        switch ($documentCode) {
            case 'COE':
                return Pdf::loadView('pdf.coe-template', $data);
            case 'GMC':
                return Pdf::loadView('pdf.cgmc-template', $data);
            case 'REG':
                return Pdf::loadView('pdf.reg-template', $data);
            case 'COG':
                return Pdf::loadView('pdf.cog-template', $data);
            case 'TOR':
                return Pdf::loadView('pdf.tor-template', $data);
            default:
                return Pdf::loadView('pdf.generic-template', $data);
        }
    }
    
    /**
     * Get student grades (placeholder)
     */
    private function getStudentGrades($user)
    {
        // TODO: Implement grade management system
        return [
            'subjects' => [
                ['name' => 'English', 'grade' => '90', 'remarks' => 'Passed'],
                ['name' => 'Mathematics', 'grade' => '85', 'remarks' => 'Passed'],
                ['name' => 'Science', 'grade' => '88', 'remarks' => 'Passed'],
                ['name' => 'Filipino', 'grade' => '92', 'remarks' => 'Passed'],
                ['name' => 'Social Studies', 'grade' => '87', 'remarks' => 'Passed'],
            ],
            'average' => '88.4',
        ];
    }
    
    /**
     * Get current school year
     */
    private function getCurrentSchoolYear()
    {
        $year = date('Y');
        $nextYear = $year + 1;
        return "SY {$year}-{$nextYear}";
    }
}