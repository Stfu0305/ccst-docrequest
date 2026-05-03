<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/student/requests/' . $this->appointment->document_request_id);

        $date = \Carbon\Carbon::parse($this->appointment->appointment_date)->format('F d, Y');
        $time = \Carbon\Carbon::parse($this->appointment->timeSlot->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($this->appointment->timeSlot->end_time)->format('h:i A');

        return (new MailMessage)
            ->subject('Reminder: Your Appointment is Tomorrow – CCST DocRequest')
            ->greeting('Hello ' . ($notifiable->first_name ?? 'Student') . ',')
            ->line('This is a friendly reminder that you have an appointment tomorrow.')
            ->line('**Date:** ' . $date)
            ->line('**Time:** ' . $time)
            ->line('Please remember to bring your school ID.')
            ->action('View Details', $url)
            ->line('Thank you for using CCST DocRequest!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Reminder: You have an appointment tomorrow.',
            'url' => '/student/requests/' . $this->appointment->document_request_id,
            'type' => 'appointment_reminder',
        ];
    }
}
