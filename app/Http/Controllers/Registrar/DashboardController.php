<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\DocumentRequest;
use App\Models\Appointment;
use App\Models\User;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    use SendsDatabaseNotifications;

    public function index()
    {
        // Stats
        $pendingVerifications = User::where('role', 'student')->where('is_verified', false)->count();
        $pendingRequests = DocumentRequest::whereIn('status', ['pending', 'ready_for_pickup'])->count();
        $todayAppointments = Appointment::whereDate('appointment_date', today())->where('status', 'scheduled')->count();
        
        // Announcements
        $announcement = Announcement::general();
        $transactionDay = Announcement::transactionDays();

        // Weekly chart data
        $weeklyLabels = [];
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyLabels[] = $date->format('D');
            $weeklyData[] = Appointment::whereDate('appointment_date', $date)->count();
        }

        // Top requested documents
        $topDocuments = \DB::table('document_request_items')
            ->join('document_types', 'document_request_items.document_type_id', '=', 'document_types.id')
            ->select('document_types.name', \DB::raw('count(*) as count'))
            ->groupBy('document_types.id', 'document_types.name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return view('registrar.dashboard', compact(
            'pendingVerifications',
            'pendingRequests',
            'todayAppointments',
            'announcement',
            'transactionDay',
            'weeklyLabels',
            'weeklyData',
            'topDocuments'
        ));
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
        
        $message = '✅ Announcement has been updated successfully.';
        $this->sendNotificationToCurrentUser($message, route('registrar.dashboard'));
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.dashboard');
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
        
        $message = "✅ Announcement has been {$status}.";
        $this->sendNotificationToCurrentUser($message, route('registrar.dashboard'));
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.dashboard');
    }

    public function servePhoto()
    {
        $user = auth()->user();

        if (!$user->profile_photo) {
            abort(404);
        }

        $path = storage_path('app/private/' . $user->profile_photo);

        if (!file_exists($path)) {
            abort(404);
        }

        $mime = mime_content_type($path);

        return response()->file($path, ['Content-Type' => $mime]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

        $user = auth()->user();

        if ($user->profile_photo) {
            Storage::disk('local')->delete($user->profile_photo);
        }

        $file = $request->file('profile_photo');
        $filename = 'registrar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('photos', $filename, 'local');

        $user->update(['profile_photo' => $path]);

        $message = 'Your profile photo has been updated successfully.';
        $this->sendNotificationToCurrentUser($message, route('registrar.account'));
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.account');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'contact_number' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        auth()->user()->update($validated);

        $message = 'Your profile information has been updated successfully.';
        $this->sendNotificationToCurrentUser($message, route('registrar.account'));
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.account');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string'],
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Your current password is incorrect.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        $message = 'Your password has been changed successfully. Please use your new password next time you log in.';
        $this->sendNotificationToCurrentUser($message, route('registrar.account'));
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.account');
    }
}