<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $notifications = $user->notifications()->paginate(20);

            return success('Notifications fetched successfully', $notifications, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Failed to fetch notifications', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(): JsonResponse
    {
        try {
            $user = Auth::user();
            $count = $user->unreadNotifications()->count();

            return success('Unread count fetched successfully', ['count' => $count], Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Failed to fetch unread count', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get unread notifications
     */
    public function unread(): JsonResponse
    {
        try {
            $user = Auth::user();
            $notifications = $user->unreadNotifications()->limit(10)->get();

            return success('Unread notifications fetched successfully', $notifications, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Failed to fetch unread notifications', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $id)->first();

            if (!$notification) {
                return error('Notification not found', null, Response::HTTP_NOT_FOUND);
            }

            $notification->markAsRead();

            return success('Notification marked as read', $notification, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Failed to mark notification as read', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();

            return success('All notifications marked as read', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Failed to mark all notifications as read', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $id)->first();

            if (!$notification) {
                return error('Notification not found', null, Response::HTTP_NOT_FOUND);
            }

            $notification->delete();

            return success('Notification deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Failed to delete notification', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            $user->readNotifications()->delete();

            return success('All read notifications deleted', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Failed to delete notifications', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
