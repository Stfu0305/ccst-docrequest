<?php

namespace App\Traits;

use App\Notifications\SystemNotification;

trait SendsDatabaseNotifications
{
    /**
     * Send a database notification to a user
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @param string $message
     * @param string|null $url
     * @return void
     */
    protected function sendNotification($user, string $message, ?string $url = null)
    {
        // Only send if user exists
        if ($user && $message) {
            $notification = new SystemNotification($message, $url);
            $user->notify($notification);
            
            // Set a session flag to trigger immediate notification check on next page load
            session()->flash('check_notifications', true);
        }
    }

    /**
     * Send a notification to the currently authenticated user
     *
     * @param string $message
     * @param string|null $url
     * @return void
     */
    protected function sendNotificationToCurrentUser(string $message, ?string $url = null)
    {
        $this->sendNotification(auth()->user(), $message, $url);
    }
}