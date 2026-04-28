<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;

    public function __construct(User $student)
    {
        $this->student = $student;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): array
    {
        $url = url('/registrar/students/pending');

        return [
            'subject' => 'New Student Registration - Pending Verification',
            'greeting' => 'Hello Registrar,',
            'line' => 'A new student has registered and is awaiting verification.',
            'student_name' => $this->student->full_name,
            'student_number' => $this->student->student_number,
            'email' => $this->student->email,
            'action_text' => 'Verify Student',
            'action_url' => $url,
            'line2' => 'Please review the student\'s information and uploaded ID before approving.',
            'thank_you' => 'Thank you for your prompt action.',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New student registration: ' . $this->student->full_name . ' (' . $this->student->student_number . ') needs verification.',
            'url' => '/registrar/students/pending',
            'type' => 'verification',
        ];
    }
}