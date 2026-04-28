<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountVerifiedNotification;
use App\Traits\SendsDatabaseNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentVerificationController extends Controller
{
    use SendsDatabaseNotifications;

    public function pending()
    {
        $pendingStudents = User::where('role', 'student')
            ->where('is_verified', false)
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('registrar.students.pending', compact('pendingStudents'));
    }

    public function verify($id)
    {
        $student = User::findOrFail($id);

        if ($student->is_verified) {
            return back()->with('error', 'Student is already verified.');
        }

        $student->markAsVerified(auth()->id());

        // Send notification to student
        $student->notify(new AccountVerifiedNotification());

        // Send notification to registrar
        $message = '✅ Student ' . $student->full_name . ' has been verified.';
        $this->sendNotificationToCurrentUser($message, route('registrar.students.pending'));
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.students.pending')
            ->with('success', 'Student verified successfully.');
    }

    public function verifyBulk(Request $request)
    {
        $ids = $request->input('student_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No students selected.');
        }

        $students = User::whereIn('id', $ids)
            ->where('role', 'student')
            ->where('is_verified', false)
            ->get();

        $count = 0;
        foreach ($students as $student) {
            $student->markAsVerified(auth()->id());
            $student->notify(new AccountVerifiedNotification());
            $count++;
        }

        $message = '✅ ' . $count . ' student(s) have been verified.';
        $this->sendNotificationToCurrentUser($message, route('registrar.students.pending'));
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.students.pending')
            ->with('success', $message);
    }

    public function showId($id)
    {
        $student = User::findOrFail($id);

        if (!$student->student_id_photo || !Storage::disk('local')->exists($student->student_id_photo)) {
            abort(404);
        }

        $path = storage_path('app/private/' . $student->student_id_photo);
        $mime = mime_content_type($path);

        return response()->file($path, ['Content-Type' => $mime]);
    }

    public function reject($id)
    {
        $student = User::findOrFail($id);

        // Delete the student account (they can re-register)
        if ($student->student_id_photo) {
            Storage::disk('local')->delete($student->student_id_photo);
        }
        $student->delete();

        $message = '❌ Student registration has been rejected and removed.';
        $this->sendNotificationToCurrentUser($message, route('registrar.students.pending'));
        session()->flash('check_notifications', true);

        return redirect()->route('registrar.students.pending')
            ->with('success', 'Student registration rejected and removed.');
    }
}