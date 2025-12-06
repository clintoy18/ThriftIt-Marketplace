<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark all notifications as read for authenticated user
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Mark all unread notifications as read with timestamp
            $updatedCount = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
            
            // Get updated unread count
            $unreadCount = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
            
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'unread_count' => $unreadCount,
                'marked_count' => $updatedCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error marking notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Load more notifications
     */
    public function loadMore(Request $request)
    {
        $page = (int) $request->get('page', 2); // Page 1 is initial load (50 items), page 2 starts from item 51
        $perPage = 50;
        
        // Calculate offset: page 2 = skip 50 (already loaded), page 3 = skip 100, etc.
        $offset = ($page - 1) * $perPage;
        
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->skip($offset)
            ->take($perPage)
            ->get()
            ->map(function($notification) {
                $data = $notification->data;
                $data['profile_pic_url'] = $notification->from_user_profile_pic;
                // Ensure is_read is explicitly included
                return [
                    'id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'type' => $notification->type,
                    'data' => $data,
                    'is_read' => (bool) $notification->is_read,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'updated_at' => $notification->updated_at,
                ];
            });
        
        $totalCount = Notification::where('user_id', Auth::id())->count();
        $loadedCount = $offset + $notifications->count();
        $hasMore = $loadedCount < $totalCount;
        
        return response()->json([
            'notifications' => $notifications,
            'has_more' => $hasMore
        ]);
    }
}