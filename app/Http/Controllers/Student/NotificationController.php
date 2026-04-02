<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return the current user's notifications as JSON.
     * Shows ALL notifications, newest first.
     *
     * GET /student/notifications
     */
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
                // Handle different data structures
                $data = $notification->data;
                
                // Try to extract message from various possible formats
                $message = null;
                
                if (isset($data['message'])) {
                    $message = $data['message'];
                } elseif (isset($data['data']['message'])) {
                    $message = $data['data']['message'];
                } elseif (isset($data['title'])) {
                    $message = $data['title'];
                } elseif (isset($data['content'])) {
                    $message = $data['content'];
                } else {
                    $message = 'You have a new notification.';
                }
                
                // Extract URL
                $url = '#';
                if (isset($data['url'])) {
                    $url = $data['url'];
                } elseif (isset($data['data']['url'])) {
                    $url = $data['data']['url'];
                }
                
                // Extract type
                $type = $data['type'] ?? $data['data']['type'] ?? 'system';
                
                return [
                    'id'      => $notification->id,
                    'message' => $message,
                    'url'     => $url,
                    'time'    => $notification->created_at->diffForHumans(),
                    'read'    => $notification->read_at !== null,
                    'type'    => $type,
                ];
            });

        // Count unread notifications
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'count'         => $notifications->count(),
            'unread'        => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a single notification as read.
     *
     * PATCH /student/notifications/{id}/read
     */
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

    /**
     * Mark ALL notifications as read.
     *
     * POST /student/notifications/mark-all-read
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true]);
    }
}