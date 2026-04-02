<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * NotificationController (Registrar)
 *
 * Handles AJAX requests from the bell dropdown.
 * Registrar receives notifications about:
 * - New document requests submitted
 * - Payment verified (so they can process)
 * - Request status changes
 */
class NotificationController extends Controller
{
    /**
     * Return the current user's notifications as JSON.
     * Shows ALL notifications, newest first.
     *
     * GET /registrar/notifications
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get ALL notifications (not just unread) — ordered newest first
        // Show max 50 per page to avoid huge lists
        $notifications = $user
            ->notifications()
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($notification) {
                return [
                    'id'      => $notification->id,
                    'message' => $notification->data['message'] ?? 'You have a new notification.',
                    'url'     => $notification->data['url'] ?? '#',
                    'time'    => $notification->created_at->diffForHumans(),
                    'read'    => $notification->read_at !== null, // true if already read
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
     * PATCH /registrar/notifications/{id}/read
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
     * POST /registrar/notifications/mark-all-read
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true]);
    }
}