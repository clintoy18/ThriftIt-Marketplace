<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    

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
            ->get();
        
        $totalCount = Notification::where('user_id', Auth::id())->count();
        $loadedCount = $offset + $notifications->count();
        $hasMore = $loadedCount < $totalCount;
        
        return response()->json([
            'notifications' => $notifications,
            'has_more' => $hasMore
        ]);
    }
}

