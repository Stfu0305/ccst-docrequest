<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\Appointment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('registrar.reports.index');
    }

    public function export(Request $request)
    {
        $reportType = $request->input('report_type');
        $isPreview = $request->has('preview');
        
        switch ($reportType) {
            case 'requests':
                return $this->exportRequestsReport($request, $isPreview);
            case 'payments':
                return $this->exportPaymentsReport($request, $isPreview);
            case 'appointments':
                return $this->exportAppointmentsReport($request, $isPreview);
            case 'students':
                return $this->exportStudentsReport($request, $isPreview);
            default:
                return back()->with('error', 'Invalid report type.');
        }
    }

    private function exportRequestsReport($request, $isPreview = false)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $status = $request->input('status', 'all');
        $includeSummary = $request->has('include_summary');

        $query = DocumentRequest::with(['user', 'items.documentType'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        // Summary data
        $summary = [];
        if ($includeSummary) {
            $summary['total'] = $requests->count();
            $summary['total_amount'] = $requests->sum('total_fee');
            $summary['by_status'] = $requests->groupBy('status')->map->count();
        }

        $data = [
            'title' => 'Document Requests Report',
            'subtitle' => $dateFrom . ' to ' . $dateTo,
            'requests' => $requests,
            'summary' => $summary,
            'includeSummary' => $includeSummary,
            'generated_at' => now()->format('F d, Y h:i A'),
            'generated_by' => auth()->user()->name,
        ];

        $pdf = Pdf::loadView('pdf.request-report', $data);
        
        if ($isPreview) {
            return $pdf->stream();
        }
        
        return $pdf->download('document-requests-report-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportPaymentsReport($request, $isPreview = false)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $paymentMethod = $request->input('payment_method', 'all');
        $status = $request->input('status', 'all');
        $includeSummary = $request->has('include_summary');

        $query = DocumentRequest::with(['user', 'paymentProof', 'officialReceipt'])
            ->whereNotNull('payment_method')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        // Summary data
        $summary = [];
        if ($includeSummary) {
            $summary['total_count'] = $payments->count();
            $summary['total_collected'] = $payments->where('status', 'payment_verified')->sum('total_fee');
            $summary['by_method'] = $payments->groupBy('payment_method')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->where('status', 'payment_verified')->sum('total_fee')
                ];
            });
        }

        $data = [
            'title' => 'Payments Report',
            'subtitle' => $dateFrom . ' to ' . $dateTo,
            'payments' => $payments,
            'summary' => $summary,
            'includeSummary' => $includeSummary,
            'generated_at' => now()->format('F d, Y h:i A'),
            'generated_by' => auth()->user()->name,
        ];

        $pdf = Pdf::loadView('pdf.payment-report', $data);
        
        if ($isPreview) {
            return $pdf->stream();
        }
        
        return $pdf->download('payments-report-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportAppointmentsReport($request, $isPreview = false)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $status = $request->input('status', 'all');
        $includeSummary = $request->has('include_summary');

        $query = Appointment::with(['student', 'documentRequest', 'timeSlot'])
            ->whereBetween('appointment_date', [$dateFrom, $dateTo]);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')->get();

        // Summary data
        $summary = [];
        if ($includeSummary) {
            $summary['total'] = $appointments->count();
            $summary['by_status'] = $appointments->groupBy('status')->map->count();
            $summary['attendance_rate'] = $appointments->count() > 0 
                ? round(($appointments->where('status', 'completed')->count() / $appointments->count()) * 100, 2)
                : 0;
        }

        $data = [
            'title' => 'Appointments Report',
            'subtitle' => $dateFrom . ' to ' . $dateTo,
            'appointments' => $appointments,
            'summary' => $summary,
            'includeSummary' => $includeSummary,
            'generated_at' => now()->format('F d, Y h:i A'),
            'generated_by' => auth()->user()->name,
        ];

        $pdf = Pdf::loadView('pdf.appointment-report', $data);
        
        if ($isPreview) {
            return $pdf->stream();
        }
        
        return $pdf->download('appointments-report-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportStudentsReport($request, $isPreview = false)
    {
        $gradeLevel = $request->input('grade_level', 'all');
        $strand = $request->input('strand', 'all');
        $includeSummary = $request->has('include_summary');

        $query = User::where('role', 'student');

        if ($gradeLevel !== 'all') {
            $query->where('grade_level', $gradeLevel);
        }

        if ($strand !== 'all') {
            $query->where('strand', $strand);
        }

        $students = $query->orderBy('last_name')->orderBy('first_name')->get();

        // Summary data
        $summary = [];
        if ($includeSummary) {
            $summary['total'] = $students->count();
            $summary['by_grade'] = $students->groupBy('grade_level')->map->count();
            $summary['by_strand'] = $students->groupBy('strand')->map->count();
        }

        $data = [
            'title' => 'Students Report',
            'subtitle' => 'As of ' . now()->format('F d, Y'),
            'students' => $students,
            'summary' => $summary,
            'includeSummary' => $includeSummary,
            'generated_at' => now()->format('F d, Y h:i A'),
            'generated_by' => auth()->user()->name,
        ];

        $pdf = Pdf::loadView('pdf.student-report', $data);
        
        if ($isPreview) {
            return $pdf->stream();
        }
        
        return $pdf->download('students-report-' . now()->format('Y-m-d') . '.pdf');
    }
}