<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\DocumentRequest;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use SendsDatabaseNotifications;

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

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        
        $request->validate([
            'content' => 'required|string',
        ]);
        
        $announcement->update([
            'content' => $request->content,
            'published_by' => auth()->id(),
        ]);
        
        // Send notification
        $message = '✅ Announcement has been updated successfully.';
        $this->sendNotificationToCurrentUser($message, route('registrar.dashboard'));
        session()->flash('check_notifications', true);
        
        return redirect()
            ->route('registrar.dashboard')
            ->with('success', 'Announcement updated successfully.');
    }

    public function publish($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        $announcement->update([
            'is_published' => !$announcement->is_published,
            'published_by' => auth()->id(),
            'published_at' => now(),
        ]);
        
        $status = $announcement->is_published ? 'published' : 'unpublished';
        
        // Send notification
        $message = "✅ Announcement has been {$status}.";
        $this->sendNotificationToCurrentUser($message, route('registrar.dashboard'));
        session()->flash('check_notifications', true);
        
        return redirect()
            ->route('registrar.dashboard')
            ->with('success', "Announcement {$status} successfully.");
    }
}