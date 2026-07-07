<?php

namespace App\Http\Controllers;

use App\Models\NotificationUser;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function unread(Request $request)
    {
        $user = $request->user();

        $notifications = NotificationUser::leftJoin('notifications', 'notification_user.notification_id', '=', 'notifications.id')
            ->where('notification_user.user_id', $user->id)
            ->where('notification_user.read', false)
            ->orderBy('notification_user.created_at', 'desc')
            ->select(
                'notifications.id',
                'notifications.title',
                'notifications.message',
                'notifications.type',
                'notifications.model_id',
                'notifications.created_at'
            )
            ->take(10)
            ->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    /**
     * Get all notifications (both read and unread) for the authenticated user.
     */
    public function all(Request $request)
    {
        $user = $request->user();

        $notifications = NotificationUser::leftJoin('notifications', 'notification_user.notification_id', '=', 'notifications.id')
            ->where('notification_user.user_id', $user->id)
            ->orderBy('notification_user.created_at', 'desc')
            ->select(
                'notifications.id',
                'notifications.title',
                'notifications.message',
                'notifications.type',
                'notifications.model_id',
                'notifications.created_at',
                'notification_user.read'
            )
            ->take(20) // Increase limit for "see all" functionality
            ->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllRead(Request $request)
    {
        $user = $request->user();

        NotificationUser::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json([
            'message' => 'All notifications marked as read.'
        ]);
    }

    public function markAsRead(Request $request, $notificationId)
    {
        $user = $request->user();

        // Update the notification_user pivot table
        $affected = NotificationUser::where('user_id', $user->id)
            ->where('notification_id', $notificationId)
            ->where('read', false)
            ->update(['read' => true]);

        if ($affected) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification already read or not found'
        ], 404);
    }
}