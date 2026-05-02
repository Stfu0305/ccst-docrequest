<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DocumentType;
use App\Models\DocumentRequest;
use App\Models\DocumentRequestItem;
use App\Models\StatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalkInController extends Controller
{
    public function index()
    {
        return view('registrar.walkin.index');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return redirect()->back()->with('error', 'Please enter a student number or name.');
        }

        $students = User::where('role', 'student')
            ->where(function($q) use ($query) {
                $q->where('student_number', 'like', "%{$query}%")
                  ->orWhere('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->get();

        return view('registrar.walkin.index', compact('students', 'query'));
    }

    public function create(Request $request)
    {
        $studentId = $request->query('student_id');
        $student = null;

        if ($studentId) {
            $student = User::findOrFail($studentId);
        }

        $documentTypes = DocumentType::where('is_active', true)->get();

        return view('registrar.walkin.create', compact('student', 'documentTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'student_number' => 'nullable|string|max:50',
            'contact_number' => 'required|string|max:20',
            'course_program' => 'required|string|max:100',
            'year_level' => 'required|string|max:20',
            'section' => 'nullable|string|max:50',
        ]);

        $password = Str::random(10);
        $email = $request->student_number ? $request->student_number . '@walkin.local' : strtolower($request->first_name . $request->last_name) . '@walkin.local';

        // Make email unique just in case
        $baseEmail = $email;
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = str_replace('@walkin.local', $counter . '@walkin.local', $baseEmail);
            $counter++;
        }

        $student = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'student',
            'student_number' => $request->student_number ?? 'WALK-IN-' . strtoupper(Str::random(5)),
            'contact_number' => $request->contact_number,
            'strand' => $request->course_program,
            'grade_level' => $request->year_level,
            'section' => $request->section ?? 'N/A',
            'is_walk_in' => true,
            'walk_in_registered_by' => Auth::id(),
            'walk_in_registered_at' => now(),
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return redirect()->route('registrar.walkin.create', ['student_id' => $student->id]);
    }

    public function createRequest(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'documents' => 'required|array|min:1',
            'documents.*' => 'exists:document_types,id',
            'copies' => 'required|array',
        ]);

        $student = User::findOrFail($request->student_id);

        $totalFee = 0;
        foreach ($request->documents as $docId) {
            $docType = DocumentType::find($docId);
            $copies = $request->copies[$docId] ?? 1;
            $totalFee += $docType->fee * $copies;
        }

        // Generate Reference Number
        $year = date('Y');
        $lastRequest = DocumentRequest::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $lastRequest ? intval(substr($lastRequest->reference_number, -5)) + 1 : 1;
        $referenceNumber = 'DQST-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

        $docRequest = DocumentRequest::create([
            'reference_number' => $referenceNumber,
            'user_id' => $student->id,
            'student_number' => $student->student_number,
            'full_name' => $student->full_name,
            'contact_number' => $student->contact_number ?? 'N/A',
            'course_program' => $student->strand ?? 'N/A',
            'year_level' => $student->grade_level ?? 'N/A',
            'section' => $student->section ?? 'N/A',
            'total_fee' => $totalFee,
            'status' => 'payment_method_set', // Skip pending for walk-in
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'is_walk_in' => true,
            'walk_in_handled_by' => Auth::id(),
        ]);

        foreach ($request->documents as $docId) {
            $docType = DocumentType::find($docId);
            $copies = $request->copies[$docId] ?? 1;

            DocumentRequestItem::create([
                'document_request_id' => $docRequest->id,
                'document_type_id' => $docType->id,
                'copies' => $copies,
                'fee' => $docType->fee,
            ]);
        }

        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by' => Auth::id(),
            'old_status' => 'pending',
            'new_status' => 'payment_method_set',
            'notes' => 'Walk-in request created by registrar.',
        ]);

        return redirect()->route('registrar.walkin.payment', $docRequest->id);
    }

    public function printPayment($id)
    {
        $docRequest = DocumentRequest::with(['items.documentType', 'user'])->findOrFail($id);

        if ($docRequest->payment_status === 'paid') {
            return redirect()->route('registrar.requests.show', $docRequest->id)->with('success', 'Request is already paid.');
        }

        return view('registrar.walkin.payment', compact('docRequest'));
    }

    public function completePayment(Request $request, $id)
    {
        $request->validate([
            'receipt_number' => 'required|string|max:50',
            'cashier_name' => 'nullable|string|max:100',
        ]);

        $docRequest = DocumentRequest::findOrFail($id);

        $docRequest->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'receipt_number' => $request->receipt_number,
            'cashier_name' => $request->cashier_name,
            'status' => 'processing',
        ]);

        StatusLog::create([
            'document_request_id' => $docRequest->id,
            'changed_by' => Auth::id(),
            'old_status' => 'payment_method_set',
            'new_status' => 'processing',
            'notes' => 'Walk-in payment recorded by registrar. Receipt: ' . $request->receipt_number,
        ]);

        return redirect()->route('registrar.requests.show', $docRequest->id)->with('success', 'Walk-in request created and payment recorded successfully. It is now processing.');
    }
}
