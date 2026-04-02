<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\DocumentRequest;

class DashboardController extends Controller
{
    public function index()
    {
        return view('registrar.dashboard', [
            'announcement'   => Announcement::general(),
            'transactionDay' => Announcement::transactionDays(),
            'totalRequests'  => DocumentRequest::count(),
            'processing'     => DocumentRequest::where('status', 'processing')->count(),
            'readyForPickup' => DocumentRequest::where('status', 'ready_for_pickup')->count(),
            'receivedToday'  => DocumentRequest::where('status', 'received')
                                    ->whereDate('updated_at', today())->count(),
            'cancelled'      => DocumentRequest::where('status', 'cancelled')->count(),
        ]);
    }

    public function account()
    {
        return view('registrar.account.index');
    }

    public function update($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'content'      => request('content'),
            'published_by' => auth()->id(),
        ]);

        return back()->with('success', 'Announcement updated successfully.');
    }

    public function publish($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'is_published' => ! $announcement->is_published,
            'published_by' => auth()->id(),
            'published_at' => now(),
        ]);

        $status = $announcement->is_published ? 'published' : 'unpublished';
        return back()->with('success', "Announcement {$status} successfully.");
    }
}