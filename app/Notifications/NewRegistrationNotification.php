<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

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

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/registrar/students/pending');

        return (new MailMessage)
            ->subject('New Student Registration – Pending Verification')
            ->greeting('Hello Registrar,')
            ->line('A new student has registered and is awaiting verification.')
            ->line('**Student Name:** ' . ($this->student->full_name ?? $this->student->first_name . ' ' . $this->student->last_name))
            ->line('**Student Number:** ' . ($this->student->student_number ?? 'N/A'))
            ->line('**Email:** ' . $this->student->email)
            ->action('Verify Student', $url)
            ->line('Please review the student\'s information and uploaded ID before approving.')
            ->line('Thank you for your prompt action.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New student registration: ' . ($this->student->full_name ?? $this->student->first_name) . ' (' . ($this->student->student_number ?? '') . ') needs verification.',
            'url' => '/registrar/students/pending',
            'type' => 'verification',
        ];
    }
}