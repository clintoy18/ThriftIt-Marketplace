<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            
            // Use DB transaction to ensure data consistency
            DB::beginTransaction();
            
            try {
                // Mark all unread notifications as read with timestamp
                // Use raw update to ensure boolean is stored correctly (1 for true)
                // Update notifications that are either unread OR have null read_at (for consistency)
                $updatedCount = DB::table('notifications')
                    ->where('user_id', $user->id)
                    ->where(function($query) {
                        $query->where('is_read', 0) // Use 0 instead of false for raw query
                              ->orWhereNull('read_at'); // Also update any that have null read_at
                    })
                    ->update([
                        'is_read' => 1, // Use 1 instead of true for raw query
                        'read_at' => now(),
                        'updated_at' => now()
                    ]);
                
                DB::commit();
                
                // Refresh cache if using any
                Cache::forget("notifications_unread_count_{$user->id}");
                
                // Get updated unread count (should be 0)
                $unreadCount = Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();
                
                return response()->json([
                    'success' => true,
                    'unread_count' => $unreadCount,
                    'marked_count' => $updatedCount
                ]);
                
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
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
        $page = (int) $request->get('page', 1); // Page 1 = first 50 items
        $perPage = 50;
        
        // Calculate offset: page 1 = skip 0, page 2 = skip 50, etc.
        $offset = ($page - 1) * $perPage;
        
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->skip($offset)
            ->take($perPage)
            ->get()
            ->map(function($notification) {
                $data = $notification->data;
                $data['profile_pic_url'] = $notification->from_user_profile_pic;
                
                // Use the accessor which ensures read_at takes precedence
                $isRead = $notification->is_read;
                
                // Double-check: if read_at exists, it's definitely read
                if ($notification->read_at !== null) {
                    $isRead = true;
                }
                
                return [
                    'id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'type' => $notification->type,
                    'data' => $data,
                    'is_read' => (bool) $isRead,
                    'read_at' => $notification->read_at ? $notification->read_at->toIso8601String() : null,
                    'created_at' => $notification->created_at->toIso8601String(),
                    'updated_at' => $notification->updated_at->toIso8601String(),
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