<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'conversations' => $conversations,
        ]);
    }

    public function show(User $user)
    {
        $this->authorize('message', [Message::class, $user]);

        $chatData = $this->messageService->getChatData(Auth::id(), $user->id);

        if (isset($chatData['error'])) {
            abort(403, $chatData['error']);
        }

        $conversations = $this->messageService->getUserConversations(Auth::id());

        return view('private-chat', [
            'recipient' => $chatData['receiver'],
            'privateMessages' => $chatData['messages'],
            'conversations' => $conversations,
        ]);
    }

    public function send(Request $request, User $user)
    {
        $this->authorize('message', [Message::class, $user]);

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
        ]);

        $messageContent = trim($request->input('message', ''));
        $hasMessage = ! empty($messageContent);
        $hasImage = $request->hasFile('image');

        if (! $hasMessage && ! $hasImage) {
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
        if (! $message->relationLoaded('user')) {
            $message->load('user');
        }

        // Add image_url for easy access using Storage facade for proper URL generation on S3
        $message->setAttribute('image_url', $message->image_path
            ? Storage::disk('s3')->url($message->image_path)
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
                ],
            ],
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id',
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

    public function markAllMessagesAsRead()
    {
        $userId = Auth::id();

        Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    public function unreadCount()
    {
        return response()->json([
            'unread_count' => Message::where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count(),
        ]);
    }

    public function conversationUnreadCount(int $userId)
    {
        $currentUserId = Auth::id();

        return response()->json([
            'unread_count' => Message::where('user_id', $userId)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->count(),
        ]);
    }

    public function block(User $user)
    {
        $currentUser = Auth::user();

        // Prevent blocking yourself
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot block yourself.',
            ], 400);
        }

        // Check if already blocked
        if ($currentUser->hasBlocked($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'User is already blocked.',
            ], 400);
        }

        // Block the user
        \App\Models\BlockedUser::create([
            'user_id' => $currentUser->id,
            'blocked_user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User blocked successfully.',
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
                    'blocked_at' => $blocked->created_at,
                ];
            })
            ->values(); // Re-index array after filtering

        return response()->json([
            'success' => true,
            'blocked_users' => $blockedUsers,
        ]);
    }

    public function unblock(User $user)
    {
        $currentUser = Auth::user();

        // Check if user is actually blocked
        if (! $currentUser->hasBlocked($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'User is not blocked.',
            ], 400);
        }

        // Unblock the user
        \App\Models\BlockedUser::where('user_id', $currentUser->id)
            ->where('blocked_user_id', $user->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'User unblocked successfully.',
        ]);
    }

    /**
     * Proxy only known public S3 image objects used by chat image prefill.
     */
    public function proxyImage(Request $request)
    {
        $request->validate([
            'url' => 'nullable|url',
            'path' => 'nullable|string',
        ]);

        $path = $this->resolveAllowedS3ImagePath($request);

        if (! $path) {
            return response()->json(['error' => 'Image is not available.'], 403);
        }

        try {
            $disk = Storage::disk('s3');

            if (! $disk->exists($path)) {
                return response()->json(['error' => 'Image not found.'], 404);
            }

            $contentType = $disk->mimeType($path) ?: $this->guessImageContentType($path);

            return response($disk->get($path), 200)
                ->header('Content-Type', $contentType)
                ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            report($e);

            return response()->json(['error' => 'Failed to fetch image.'], 500);
        }
    }

    private function resolveAllowedS3ImagePath(Request $request): ?string
    {
        $path = $request->input('path');

        if (! $path && $request->filled('url')) {
            $path = $this->pathFromConfiguredS3Url($request->input('url'));
        }

        if (! $path) {
            return null;
        }

        $path = ltrim($path, '/');
        $allowedPrefixes = [
            'products/',
            'products_images/',
            'donation_images/',
            'donations_images/',
            'chat_images/',
            'featured/items/',
            'featured/avatars/',
        ];

        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $path;
            }
        }

        return null;
    }

    private function pathFromConfiguredS3Url(string $url): ?string
    {
        $urlHost = parse_url($url, PHP_URL_HOST);
        $configuredUrl = config('filesystems.disks.s3.url');
        $bucket = config('filesystems.disks.s3.bucket');
        $region = config('filesystems.disks.s3.region');
        $allowedHosts = [];

        if ($configuredUrl) {
            $allowedHosts[] = parse_url($configuredUrl, PHP_URL_HOST);
        }

        if ($bucket) {
            $allowedHosts[] = "{$bucket}.s3.amazonaws.com";

            if ($region) {
                $allowedHosts[] = "{$bucket}.s3.{$region}.amazonaws.com";
            }
        }

        $allowedHosts = array_filter(array_map(fn ($host) => $host ? strtolower($host) : null, $allowedHosts));

        if (! $urlHost || ! in_array(strtolower($urlHost), $allowedHosts, true)) {
            return null;
        }

        $urlPath = ltrim((string) parse_url($url, PHP_URL_PATH), '/');
        $configuredPrefix = $configuredUrl ? ltrim((string) parse_url($configuredUrl, PHP_URL_PATH), '/') : '';
        $prefix = $configuredPrefix ? trim($configuredPrefix, '/').'/' : '';

        if ($prefix && str_starts_with($urlPath, $prefix)) {
            $urlPath = substr($urlPath, strlen($prefix));
        }

        return $urlPath ?: null;
    }

    private function guessImageContentType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }
}
