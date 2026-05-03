<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject('Appointment Confirmed – CCST DocRequest')
            ->greeting('Hello ' . ($notifiable->first_name ?? 'Student') . ',')
            ->line('Your appointment for document pickup has been confirmed!')
            ->line('**Date:** ' . $date)
            ->line('**Time:** ' . $time)
            ->line('Please proceed to the cashier for payment (if you haven\'t yet) and then to the registrar\'s office to claim your documents.')
            ->action('View Details', $url)
            ->line('Thank you for using CCST DocRequest!');
    }

    public function toArray(object $notifiable): array
    {
        $date = \Carbon\Carbon::parse($this->appointment->appointment_date)->format('M d, Y');
        return [
            'message' => 'Appointment confirmed for ' . $date . '.',
            'url' => '/student/requests/' . $this->appointment->document_request_id,
            'type' => 'appointment_confirmed',
        ];
    }
}
