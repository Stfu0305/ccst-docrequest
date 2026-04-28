<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    use SendsDatabaseNotifications;

    public function index()
    {
        // Pending GCash payments
        $gcashPending = DocumentRequest::where('payment_method', 'gcash')
            ->where('status', 'payment_uploaded')
            ->count();

        // Pending bank transfer payments
        $bankPending = DocumentRequest::where('payment_method', 'bank_transfer')
            ->where('status', 'payment_uploaded')
            ->count();

        // Pending cash payments
        $cashPending = DocumentRequest::where('payment_method', 'cash')
            ->where('status', 'payment_method_set')
            ->count();

        // Total pending
        $totalPending = $gcashPending + $bankPending + $cashPending;

        // Verified today
        $verifiedToday = DocumentRequest::where('status', 'payment_verified')
            ->whereHas('officialReceipt', fn($q) =>
                $q->whereDate('issued_at', today())
            )->count();

        // Rejected today
        $rejectedToday = DocumentRequest::where('status', 'payment_rejected')
            ->whereHas('paymentProof', fn($q) =>
                $q->whereDate('verified_at', today())
            )->count();

        // Recent pending payments (limit to 7)
        $recentPending = DocumentRequest::whereIn('status', [
                'payment_uploaded',
                'payment_method_set',
            ])
            ->whereNotNull('payment_method')
            ->latest()
            ->limit(7)
            ->get();

        return view('cashier.dashboard', compact(
            'totalPending',
            'gcashPending',
            'bankPending',
            'cashPending',
            'verifiedToday',
            'rejectedToday',
            'recentPending'
        ));
    }

    public function servePhoto()
    {
        $user = Auth::user();

        if (! $user->profile_photo) {
            abort(404);
        }

        $path = storage_path('app/private/' . $user->profile_photo);

        if (! file_exists($path)) {
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

        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('local')->delete($user->profile_photo);
        }

        $file = $request->file('profile_photo');
        $filename = 'cashier_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('photos', $filename, 'local');

        $user->update(['profile_photo' => $path]);

        $message = 'Your profile photo has been updated successfully.';
        $url = route('cashier.account');
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('check_notifications', true);

        return redirect()->route('cashier.account');
    }

    public function account()
    {
        return view('cashier.account.index');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'contact_number' => ['required', 'string', 'max:20'],
            'address'        => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $user->update($validated);

        // Send notification
        $message = 'Your profile information has been updated successfully.';
        $url = route('cashier.account');
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('check_notifications', true);

        return redirect()->route('cashier.account');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password'          => ['required', 'string'],
            'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Your current password is incorrect.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        // Send notification
        $message = 'Your password has been changed successfully. Please use your new password next time you log in.';
        $url = route('cashier.account');
        $this->sendNotificationToCurrentUser($message, $url);
        session()->flash('check_notifications', true);

        return redirect()->route('cashier.account');
    }
}