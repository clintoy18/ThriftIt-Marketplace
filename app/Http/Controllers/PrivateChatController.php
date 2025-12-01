<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\MessageService;

class PrivateChatController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index()
    {
        $conversations = $this->messageService->getUserConversations(Auth::id());

        return view('messages.index', [
            'conversations' => $conversations
        ]);
    }

    public function show(User $user)
    {
        $chatData = $this->messageService->getChatData(Auth::id(), $user->id);

        if (isset($chatData['error'])) {
            abort(403, $chatData['error']);
        }

        $conversations = $this->messageService->getUserConversations(Auth::id());

        return view('private-chat', [
            'recipient' => $chatData['receiver'],
            'privateMessages' => $chatData['messages'],
            'conversations' => $conversations
        ]);
    }

    public function send(Request $request, User $user)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240' // 10MB max
        ]);

        $messageContent = trim($request->input('message', ''));
        $hasMessage = !empty($messageContent);
        $hasImage = $request->hasFile('image');

        if (!$hasMessage && !$hasImage) {
            return response()->json(['error' => 'Message or image is required.'], 400);
        }

        $result = $this->messageService->sendPrivateMessage(
            Auth::id(),
            $user->id,
            $hasMessage ? $messageContent : null,
            $hasImage ? $request->file('image') : null
        );

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        $message = $result['message'];
        
        // Ensure user relationship is loaded
        if (!$message->relationLoaded('user')) {
            $message->load('user');
        }
        
        // Add image_url for easy access using Storage facade for proper URL generation
        // Chat images are stored on the 'public' disk, so we need to specify it explicitly
        $message->setAttribute('image_url', $message->image_path
            ? Storage::disk('public')->url($message->image_path)
            : null);

        // Return message as resource/array for JSON serialization
        return response()->json([
            'message' => [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'receiver_id' => $message->receiver_id,
                'message' => $message->message,
                'image_path' => $message->image_path,
                'image_url' => $message->image_url,
                'product_preview' => $message->getAttribute('product_preview'),
                'created_at' => $message->created_at,
                'user' => [
                    'id' => $message->user->id,
                    'fname' => $message->user->fname,
                    'lname' => $message->user->lname,
                    'profile_pic_url' => $message->user->profileImageUrl(),
                ]
            ]
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id'
        ]);

        $messageIds = $request->input('message_ids');
        $userId = Auth::id();

        // Only mark messages as read if the current user is the receiver
        \App\Models\Message::whereIn('id', $messageIds)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function block(User $user)
    {
        $currentUser = Auth::user();

        // Prevent blocking yourself
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot block yourself.'
            ], 400);
        }

        // Check if already blocked
        if ($currentUser->hasBlocked($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'User is already blocked.'
            ], 400);
        }

        // Block the user
        \App\Models\BlockedUser::create([
            'user_id' => $currentUser->id,
            'blocked_user_id' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User blocked successfully.'
        ]);
    }

    public function getBlockedUsers()
    {
        $currentUser = Auth::user();
        
        $blockedUsers = \App\Models\BlockedUser::where('user_id', $currentUser->id)
            ->with('blockedUser:id,fname,lname,profile_pic')
            ->get()
            ->filter(function ($blocked) {
                // Filter out deleted users
                return $blocked->blockedUser !== null;
            })
            ->map(function ($blocked) {
                return [
                    'id' => $blocked->blocked_user_id,
                    'fname' => $blocked->blockedUser->fname,
                    'lname' => $blocked->blockedUser->lname,
                    'profile_pic' => $blocked->blockedUser->profile_pic,
                    'profile_pic_url' => $blocked->blockedUser->profileImageUrl(),
                    'blocked_at' => $blocked->created_at
                ];
            })
            ->values(); // Re-index array after filtering

        return response()->json([
            'success' => true,
            'blocked_users' => $blockedUsers
        ]);
    }

    public function unblock(User $user)
    {
        $currentUser = Auth::user();

        // Check if user is actually blocked
        if (!$currentUser->hasBlocked($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'User is not blocked.'
            ], 400);
        }

        // Unblock the user
        \App\Models\BlockedUser::where('user_id', $currentUser->id)
            ->where('blocked_user_id', $user->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'User unblocked successfully.'
        ]);
    }

    /**
     * Proxy endpoint to fetch images from S3 (avoids CORS issues)
     */
    public function proxyImage(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $imageUrl = $request->input('url');
        
        try {
            // Fetch the image from the URL
            $imageContent = file_get_contents($imageUrl);
            
            if ($imageContent === false) {
                return response()->json(['error' => 'Failed to fetch image'], 400);
            }

            // Determine content type from URL or default to jpeg
            $contentType = 'image/jpeg';
            if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $imageUrl, $matches)) {
                $ext = strtolower($matches[1]);
                $contentType = 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);
            }

            return response($imageContent, 200)
                ->header('Content-Type', $contentType)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch image: ' . $e->getMessage()], 500);
        }
    }
}
