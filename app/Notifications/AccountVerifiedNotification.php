<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): array
    {
        $url = url('/login');

        return [
            'subject' => 'Your Account Has Been Verified',
            'greeting' => 'Hello ' . $notifiable->first_name . ',',
            'line' => 'Your account has been verified by the registrar. You can now log in to the CCST DocRequest System.',
            'action_text' => 'Login Now',
            'action_url' => $url,
            'line2' => 'You can now submit document requests and book appointments.',
            'thank_you' => 'Thank you for using CCST DocRequest!',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your account has been verified! You can now log in and submit document requests.',
            'url' => '/login',
            'type' => 'verification',
        ];
    }
}