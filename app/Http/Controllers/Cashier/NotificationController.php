<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get ALL notifications (not just unread) — ordered newest first
        $notifications = $user
            ->notifications()
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($notification) {
                // Get message from notification data (handle different formats)
                $message = $notification->data['message'] ?? 
                          $notification->data['data']['message'] ?? 
                          'You have a new notification.';
                
                $url = $notification->data['url'] ?? 
                       $notification->data['data']['url'] ?? 
                       '#';
                
                return [
                    'id'      => $notification->id,
                    'message' => $message,
                    'url'     => $url,
                    'time'    => $notification->created_at->diffForHumans(),
                    'read'    => $notification->read_at !== null,
                ];
            });

        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'count'         => $notifications->count(),
            'unread'        => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markOneRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true]);
    }
}