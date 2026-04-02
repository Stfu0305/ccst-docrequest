<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SystemNotification extends Notification  // Remove ShouldQueue temporarily

{
    use Queueable;

    /**
     * The notification message
     *
     * @var string
     */
    protected $message;

    /**
     * The URL to redirect to when clicked (optional)
     *
     * @var string|null
     */
    protected $url;

    /**
     * Create a new notification instance.
     *
     * @param string $message The notification text
     * @param string|null $url Optional URL to redirect to when clicked
     */
    public function __construct(string $message, ?string $url = null)
    {
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Only store in database, don't send email
        // Email notifications are handled separately by the 7 dedicated notification classes
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'url' => $this->url ?? '#',
            'type' => 'system',
            'time' => now()->toDateTimeString(),
        ];
    }
}