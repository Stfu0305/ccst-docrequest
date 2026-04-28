<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditController extends Controller
{
    /**
     * Display audit logs
     */
    public function index(Request $request)
    {
        $query = StatusLog::with(['documentRequest', 'changedBy']);

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('changed_by', $request->user_id);
        }

        // Filter by request
        if ($request->has('request_id') && $request->request_id) {
            $query->where('document_request_id', $request->request_id);
        }

        // Filter by action type
        if ($request->has('action') && $request->action) {
            if ($request->action === 'status_change') {
                $query->whereNotNull('old_status');
            } elseif ($request->action === 'creation') {
                $query->whereNull('old_status');
            }
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(50)
            ->appends($request->all());

        $users = User::whereIn('role', ['student', 'registrar'])->get();
        $requests = DocumentRequest::select('id', 'reference_number')->get();

        // Summary statistics
        $totalActions = StatusLog::count();
        $todayActions = StatusLog::whereDate('created_at', today())->count();
        $topUsers = StatusLog::select('changed_by', \DB::raw('count(*) as count'))
            ->groupBy('changed_by')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->with('changedBy')
            ->get();

        return view('registrar.audit.index', compact(
            'logs', 'users', 'requests', 'totalActions', 'todayActions', 'topUsers'
        ));
    }

    /**
     * Export audit log to PDF
     */
    public function export(Request $request)
    {
        $query = StatusLog::with(['documentRequest', 'changedBy']);

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $data = [
            'title' => 'Audit Log Report',
            'subtitle' => 'System Activity Log',
            'logs' => $logs,
            'generated_at' => now()->format('F d, Y h:i A'),
            'generated_by' => auth()->user()->name,
            'total_records' => $logs->count(),
        ];

        $pdf = Pdf::loadView('pdf.audit-report', $data);
        return $pdf->download('audit-log-' . now()->format('Y-m-d') . '.pdf');
    }
}