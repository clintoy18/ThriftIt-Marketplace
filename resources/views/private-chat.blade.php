
<x-app-layout>
   

    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="recipient-id" content="{{ $recipient->id }}">
    <meta name="recipient-profile-pic-url" content="{{ $recipient->profileImageUrl() }}">
    <meta name="recipient-name" content="{{ substr($recipient->fname, 0, 1) }}{{ substr($recipient->lname, 0, 1) }}">

    <div class="py-0 sm:py-6">
        <div class="max-w-7xl mx-auto px-0 sm:px-4 lg:px-8">
            <!-- Mobile Header with Conversations Toggle -->
            <div class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-20">
                
               
            </div>

            <div class="bg-white lg:rounded-2xl lg:shadow-xl overflow-hidden h-[calc(100vh-100px)] lg:h-[calc(100vh-120px)] flex flex-col lg:flex-row">
                <!-- Conversations Sidebar - Hidden on mobile by default -->
                <div id="conversations-sidebar" class="hidden lg:flex lg:w-80 bg-white lg:bg-[#F4F2ED] border-r border-gray-200 lg:border-[#B59F84] flex-col transition-all duration-300 absolute lg:relative z-30 w-full h-full lg:h-auto">
                    <!-- Mobile Header -->
                    <div class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-[#634600]">Messages</h2>
                        <button id="close-conversations" class="p-2 text-[#634600] hover:bg-gray-100 rounded-full transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                   <!-- In the Conversations Sidebar section, update the search inputs and add the search functionality -->

<!-- Desktop Search -->
<div class="hidden lg:block p-4 border-b border-[#B59F84]">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-[#634600]">Messages</h2>
        <button class="p-2 text-[#786126] hover:text-[#634600] hover:bg-[#B59F84] hover:bg-opacity-20 rounded-full transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </button>
    </div>
    <!-- Search -->
    <div class="mt-3 relative">
        <input type="text" 
               id="desktop-search-input"
               placeholder="Search conversations..." 
               class="w-full pl-10 pr-4 py-2 border border-[#B59F84] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#634600] focus:border-transparent bg-white text-sm">
        <svg class="absolute left-3 top-2.5 w-4 h-4 text-[#786126]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <!-- Clear search button -->
        <button id="clear-desktop-search" class="absolute right-3 top-2.5 hidden text-[#786126] hover:text-[#634600]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<!-- Mobile Search -->
<div class="lg:hidden p-4 border-b border-gray-200">
    <div class="relative">
        <input type="text" 
               id="mobile-search-input"
               placeholder="Search conversations..." 
               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-[#634600] focus:border-transparent bg-gray-50 text-sm">
        <svg class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <!-- Clear search button -->
        <button id="clear-mobile-search" class="absolute right-3 top-3.5 hidden text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<!-- Update the Conversations List to include search functionality -->
<div class="flex-1 overflow-y-auto" id="conversations-list">
    @if($conversations->count() > 0)
        @foreach($conversations as $conversation)
            <a href="{{ route('private.chat', $conversation['user']->id) }}" 
               class="conversation-item flex items-center p-4 hover:bg-gray-50 lg:hover:bg-[#B59F84] lg:hover:bg-opacity-20 transition-colors border-b border-gray-100 lg:border-[#B59F84] lg:border-opacity-20 {{ $conversation['user']->id == $recipient->id ? 'bg-gray-50 lg:bg-[#B59F84] lg:bg-opacity-30 lg:border-r-2 lg:border-[#634600]' : '' }}"
               data-user-name="{{ strtolower($conversation['user']->fname . ' ' . $conversation['user']->lname) }}"
               data-latest-message="{{ strtolower($conversation['latest_message']->message) }}"
               x-data="{ 
                   unreadCount: {{ $conversation['unread_count'] }},
                   conversationUserId: {{ $conversation['user']->id }},
                   init() {
                       // Listen for when messages are marked as read for this conversation
                       window.addEventListener('conversation-read', (e) => {
                           if (e.detail.user_id === this.conversationUserId) {
                               this.unreadCount = e.detail.unread_count || 0;
                           }
                       });
                       
                       // Listen for new messages in this conversation
                       @if(Auth::check())
                       if (typeof Echo !== 'undefined') {
                           Echo.private('chat.user.{{ Auth::id() }}')
                               .listen('.private-message', (e) => {
                                   // Check if message is from this conversation user
                                   if (e.message && e.message.user_id === this.conversationUserId) {
                                       // Only increment if not currently viewing this conversation
                                       const currentRecipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
                                       if (currentRecipientId != this.conversationUserId) {
                                           this.unreadCount++;
                                       }
                                   }
                               });
                       }
                       @endif
                   }
               }">
                <!-- Avatar -->
                <div class="relative">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center overflow-hidden bg-gradient-to-r from-[#634600] to-[#B59F84]">
                        @if($conversation['user']->profile_pic)
                            <img src="{{ $conversation['user']->profileImageUrl() }}"
                                 alt="{{ $conversation['user']->fname }} {{ $conversation['user']->lname }}'s Profile Picture"
                                 class="w-full h-full object-cover rounded-full">
                        @else
                            <span class="text-white font-semibold text-sm">
                                {{ substr($conversation['user']->fname, 0, 1) }}{{ substr($conversation['user']->lname, 0, 1) }}
                            </span>
                        @endif
                    </div>

                    <!-- Unread Count Badge -->
                    <div x-show="unreadCount > 0"
                         class="absolute -top-1 -right-1 w-5 h-5 bg-[#634600] text-white text-xs rounded-full flex items-center justify-center transition-all duration-300">
                        <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                    </div>
                </div>
                
                <!-- Conversation Info -->
                <div class="ml-3 flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900 lg:text-[#634600] truncate conversation-name">
                            {{ $conversation['user']->fname }} {{ $conversation['user']->lname }}
                        </p>
                        <p class="text-xs text-gray-500 lg:text-[#786126] whitespace-nowrap ml-2 conversation-time">
                            {{ $conversation['latest_message']->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <p class="text-sm text-gray-600 lg:text-[#786126] truncate mt-1 conversation-message">
                        {{ $conversation['latest_message']->message }}
                    </p>
                </div>
            </a>
        @endforeach
    @else
        <div class="flex items-center justify-center h-full p-4">
            <div class="text-center">
                <div class="w-16 h-16 bg-[#B59F84] bg-opacity-30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-[#786126]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.325 0-2.58-.26-3.68-.725L3 20l1.32-3.96C3.474 15.003 3 13.55 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <p class="text-[#634600] text-sm">No conversations yet</p>
                <p class="text-[#786126] text-xs mt-1">Start chatting with someone!</p>
            </div>
        </div>
    @endif
    
    <!-- No search results message -->
    <div id="no-search-results" class="hidden flex items-center justify-center h-full p-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-[#B59F84] bg-opacity-30 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#786126]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <p class="text-[#634600] text-sm">No conversations found</p>
            <p class="text-[#786126] text-xs mt-1">Try different search terms</p>
        </div>
    </div>
</div>
</div>
                <!-- Chat Area -->
                <div class="flex-1 flex flex-col w-full">
                    <!-- Chat Header -->
                    <div class="bg-gradient-to-r from-[#634600] to-[#B59F84] border-b border-gray-200 lg:border-none px-4 sm:px-6 py-3 sm:py-4 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <!-- Mobile Back Button -->
                                <button id="mobile-back-button" class="lg:hidden text-white hover:text-gray-200 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center overflow-hidden">
                                @if($recipient->profile_pic)
                                    <img src="{{ $recipient->profileImageUrl() }}"
                                        alt="{{ $recipient->fname }} {{ $recipient->lname }}'s Profile Picture"
                                        class="w-full h-full object-cover">
                                @else
                                    <span class="text-white font-semibold text-sm">
                                        {{ substr($recipient->fname, 0, 1) }}{{ substr($recipient->lname, 0, 1) }}
                                    </span>
                                @endif
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base sm:text-lg">{{ $recipient->fname }} {{ $recipient->lname }}</h3>
                                    <p class="text-white text-xs sm:text-sm opacity-80">Active now</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                            <!-- 
                                <button onclick="startAudioCall()" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-full transition-colors" title="Start Audio Call">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </button>
                                <button onclick="startVideoCall()" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-full transition-colors" title="Start Video Call">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            -->
                                <!-- Settings Dropdown - Desktop Only -->
                                <div id="chat-settings-dropdown" class=" lg:block relative">
                                    <button id="settings-toggle-btn" 
                                            class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-full transition-colors relative">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </button>
                                    <!-- Dropdown Menu -->
                                    <div id="settings-dropdown-menu"
                                         class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[100] py-1">
                                        <!-- View Media and Links -->
                                        <button type="button" 
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center space-x-2"
                                                onclick="showMediaAndLinks()">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>View Media & Links</span>
                                        </button>
                                        
                                        <!-- Blocked Users -->
                                        <button type="button" 
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center space-x-2"
                                                onclick="showBlockedUsers()">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            <span>Blocked Users</span>
                                        </button>
                                        
                                        <!-- Block User -->
                                        <button type="button" 
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center space-x-2"
                                                onclick="blockUser({{ $recipient->id }})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                            </svg>
                                            <span>Block User</span>
                                        </button>
                                        
                                        <!-- Delete Conversation -->
                                        <button type="button" 
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center space-x-2"
                                                onclick="deleteConversation({{ $recipient->id }})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            <span>Delete Conversation</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Container - FIXED HEIGHT -->
                    <div class="flex-1 overflow-y-auto p-3 sm:p-6 space-y-4 bg-gray-50 lg:bg-[#F4F2ED] relative" id="private-messages-container">
                        <!-- Scroll to Bottom Button -->
                        <button id="scroll-to-bottom" class="fixed bottom-20 right-4 lg:right-8 bg-[#634600] text-white p-3 rounded-full shadow-lg hover:bg-[#56432C] transition-all duration-200 z-20 hidden">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </button>
                        
                        @if($privateMessages->count() > 0)
                            @foreach($privateMessages as $msg)
                                @php
                                    $isUnread = !$msg->is_read && $msg->receiver_id === auth()->id();
                                @endphp
                                <div class="flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }} message-item {{ $isUnread ? 'unread-message' : '' }}" data-message-id="{{ $msg->id }}">
                                    <div class="flex max-w-[85%] sm:max-w-xs lg:max-w-md {{ $msg->user_id === auth()->id() ? 'flex-row-reverse' : 'flex-row' }} items-end space-x-2">
                                        <!-- Avatar -->
                                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0 {{ $msg->user_id === auth()->id() ? 'ml-1 sm:ml-2' : 'mr-1 sm:mr-2' }}">
                                            @php
                                                $user = $msg->user_id === auth()->id() ? auth()->user() : $msg->user;
                                                $profilePicUrl = $user->profile_pic_url ?? $user->profileImageUrl();
                                            @endphp
                                            @if($profilePicUrl)
                                                <img src="{{ $profilePicUrl }}" 
                                                    alt="{{ $user->fname }} {{ $user->lname }}'s Profile Picture"
                                                    class="w-full h-full object-cover rounded-full">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-r {{ $msg->user_id === auth()->id() ? 'from-[#634600] to-[#B59F84]' : 'from-[#B59F84] to-[#786126]' }} rounded-full flex items-center justify-center">
                                                    <span class="text-white text-xs font-semibold">
                                                        {{ substr($user->fname, 0, 1) }}{{ substr($user->lname, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <!-- Message Bubble -->
                                        <div class="flex flex-col {{ $msg->user_id === auth()->id() ? 'items-end' : 'items-start' }}">
                                            @php
                                                if ($msg->user_id === auth()->id()) {
                                                    $bubbleClasses = 'bg-gradient-to-r from-[#634600] to-[#B59F84] text-white rounded-br-md';
                                                } else {
                                                    if ($isUnread) {
                                                        $bubbleClasses = 'bg-blue-50 border-2 border-blue-300 text-[#634600] font-semibold rounded-bl-md shadow-md';
                                                    } else {
                                                        $bubbleClasses = 'bg-white text-[#634600] rounded-bl-md shadow-sm border border-[#B59F84]';
                                                    }
                                                }
                                            @endphp
                                            <div class="px-4 py-3 rounded-2xl text-sm {{ $bubbleClasses }}">
                                                @if($msg->image_path)
                                                    <div class="mb-2">
                                                        @php
                                                            $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($msg->image_path);
                                                        @endphp
                                                        <img src="{{ $imageUrl }}" 
                                                             alt="Shared image" 
                                                             class="max-w-full h-auto rounded-lg shadow-sm"
                                                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EImage%3C/text%3E%3C/svg%3E'; this.alt='Image failed to load';">
                                                    </div>
                                                @endif
                                                @if($msg->message && trim($msg->message))
                                                @php
                                                    $escapedMessage = e($msg->message);
                                                    $processedMessage = nl2br($escapedMessage);
                                                    // Remove both product and donation URLs from the message text
                                                    $messageText = preg_replace('/https?:\/\/[^\s]+\/(products|donations)\/\d+/', '', $processedMessage);
                                                    $textClasses = 'text-sm whitespace-pre-line break-words';
                                                    if ($isUnread) {
                                                        $textClasses .= ' font-bold';
                                                    }
                                                @endphp
                                             
                                                    <div class="{{ $textClasses }}">{!! $messageText !!}</div>
                                                @endif
                                                
           <!-- Product/Donation Preview Card (if message contains product or donation link) -->
@php
    $productUrlPattern = '/\/products\/(\d+)/';
    $donationUrlPattern = '/\/donations\/(\d+)/';
    
    preg_match($productUrlPattern, $msg->message, $productMatches);
    preg_match($donationUrlPattern, $msg->message, $donationMatches);
    
    $productId = $productMatches[1] ?? null;
    $donationId = $donationMatches[1] ?? null;
@endphp

@if($productId)
    @php
        $product = \App\Models\Product::find($productId);
    @endphp
    @if($product)
        <div class="mt-3 p-3 bg-white bg-opacity-95 rounded-xl border border-white border-opacity-40 shadow-lg group hover:bg-opacity-100 transition-all duration-200">
            <div class="flex gap-3">
                <!-- Product Image -->
                <div class="relative flex-shrink-0">
                    @if($product->first_image)
                       <img src="{{ $product->first_image }}"
                             alt="{{ $product->name }}" 
                             class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-sm"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EProduct%3C/text%3E%3C/svg%3E'; this.alt='Image failed to load';">
                    @else
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                    <!-- Status Badge -->
                    @if($product->listingtype === 'for donation')
                        <div class="absolute -top-1 -right-1 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                            FREE
                        </div>
                    @endif
                </div>
                
                <!-- Product Details -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between mb-1">
                        <h4 class="font-semibold text-sm sm:text-base text-gray-900 truncate pr-2">{{ $product->name }}</h4>
                        <div class="flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-600 mb-2 flex items-center">
                        <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        {{ $product->category->name ?? 'No Category' }}
                    </p>
                    
                    <div class="flex items-center justify-between">
                        @if($product->listingtype !== 'for donation')
                            <p class="text-sm font-bold text-[#634600] flex items-center">
                                <svg class="w-3 h-3 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                ₱{{ number_format($product->price, 2) }}
                            </p>
                        @else
                            <p class="text-sm font-bold text-green-600 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                Free
                            </p>
                        @endif
                        
                        <!-- View Product Link -->
                        <a href="{{ route('products.show', $product->id) }}" 
                           target="_blank" 
                           class="text-xs text-[#634600] hover:text-[#56432C] font-medium flex items-center">
                            <span>View</span>
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

<!-- Donation Preview Card -->
<!-- Donation Preview Card -->
@if($donationId)
    @php
        $donation = \App\Models\Donation::find($donationId);
    @endphp
    @if($donation)
        <div class="mt-3 p-3 bg-white bg-opacity-95 rounded-xl border border-white border-opacity-40 shadow-lg group hover:bg-opacity-100 transition-all duration-200">
            <div class="flex gap-3">
                <!-- Donation Image -->
                <div class="relative flex-shrink-0">
                    @if($donation->first_image)
                        <img src="{{ $donation->first_image }}"
                             alt="{{ $donation->name }}" 
                             class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-sm"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EDonation%3C/text%3E%3C/svg%3E'; this.alt='Image failed to load';">
                    @else
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    @endif
                    <!-- Donation Badge -->
                    <div class="absolute -top-1 -right-1 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                        FREE
                    </div>
                </div>
                
                <!-- Donation Details -->
                <div class="flex-1 min-w-0">
                    <div class="mb-1">
                        <h4 class="font-semibold text-sm sm:text-base text-gray-900 truncate">{{ $donation->name }}</h4>
                    </div>
                    
                    <p class="text-xs text-gray-600 mb-2 flex items-center">
                        <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        For Donation
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-green-600 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            Free
                        </p>
                        
                        <!-- View Donation Link -->
                        <a href="{{ route('donations.show', $donation->id) }}" 
                           target="_blank" 
                           class="text-xs text-[#634600] hover:text-[#56432C] font-medium flex items-center">
                            <span>View</span>
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
        </div>


                                            <!-- Timestamp - Moved outside the bubble -->
                                            <div class="mt-1 {{ $msg->user_id === auth()->id() ? 'text-right' : 'text-left' }}">
                                                <span class="text-xs text-gray-500 lg:text-[#786126] px-2">{{ $msg->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-center justify-center h-full p-4">
                                <div class="text-center">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-[#B59F84] bg-opacity-30 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-[#786126]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.325 0-2.58-.26-3.68-.725L3 20l1.32-3.96C3.474 15.003 3 13.55 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-[#634600] text-base sm:text-lg font-medium">Start a conversation</p>
                                    <p class="text-[#786126] text-sm mt-1">Send your first message to {{ $recipient->fname }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Message Input - ENHANCED DESIGN -->
                    <div class="p-3 sm:p-4 bg-white border-t border-gray-200 lg:border-[#B59F84] shadow-sm sticky bottom-0 z-10 w-full">
                        <!-- Image Preview Area -->
                        <div id="image-preview-container" class="mb-3 hidden">
                            <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded-lg">
                                <img id="image-preview" src="" alt="Preview" class="w-12 h-12 object-cover rounded-lg cursor-pointer">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600" id="image-filename"></p>
                                    <p class="text-xs text-gray-500">Click to remove</p>
                                </div>
                                <button type="button" id="remove-image" class="text-red-500 hover:text-red-700 p-1" title="Remove image">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Emoji Picker (Hidden by default) -->
                        <div id="emoji-picker" class="mb-3 hidden bg-white border border-gray-200 rounded-lg p-3 shadow-lg">
                            <div class="grid grid-cols-8 gap-1 max-h-32 overflow-y-auto">
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😀">😀</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😃">😃</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😄">😄</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😁">😁</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😆">😆</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😅">😅</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😂">😂</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤣">🤣</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😊">😊</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😇">😇</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🙂">🙂</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🙃">🙃</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😉">😉</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😌">😌</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😍">😍</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🥰">🥰</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😘">😘</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😗">😗</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😙">😙</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😚">😚</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😋">😋</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😛">😛</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😝">😝</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😜">😜</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤪">🤪</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤨">🤨</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🧐">🧐</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤓">🤓</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😎">😎</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤩">🤩</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🥳">🥳</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😏">😏</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😒">😒</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😞">😞</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😔">😔</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😟">😟</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😕">😕</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🙁">🙁</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="☹️">☹️</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😣">😣</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😖">😖</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😫">😫</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😩">😩</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🥺">🥺</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😢">😢</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😭">😭</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😤">😤</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😠">😠</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😡">😡</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤬">🤬</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤯">🤯</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😳">😳</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🥵">🥵</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🥶">🥶</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😱">😱</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😨">😨</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😰">😰</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😥">😥</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😓">😓</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤗">🤗</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤔">🤔</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤭">🤭</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤫">🤫</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤥">🤥</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😶">😶</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😐">😐</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😑">😑</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😬">😬</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🙄">🙄</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😯">😯</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😦">😦</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😧">😧</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😮">😮</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😲">😲</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🥱">🥱</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😴">😴</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤤">🤤</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😪">😪</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😵">😵</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤐">🤐</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🥴">🥴</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤢">🤢</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤮">🤮</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤧">🤧</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤒">🤒</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤕">🤕</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤑">🤑</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤠">🤠</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😈">😈</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="👿">👿</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="👹">👹</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="👺">👺</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤡">🤡</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="💩">💩</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="👻">👻</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="💀">💀</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="☠️">☠️</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="👽">👽</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="👾">👾</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🤖">🤖</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🎃">🎃</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😺">😺</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😸">😸</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😹">😹</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😻">😻</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😼">😼</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😽">😽</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="🙀">🙀</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😿">😿</button>
                                <button type="button" class="emoji-btn p-2 hover:bg-gray-100 rounded text-lg" data-emoji="😾">😾</button>
                            </div>
                        </div>

                        <form id="private-chat-form" method="POST" action="{{ route('private.chat.send', $recipient->id) }}" data-ajax="true" enctype="multipart/form-data">
                            @csrf
                            <div class="flex items-center space-x-2 w-full">
                                <!-- Image Upload Button -->
                                <label for="image-upload" class="p-2 text-gray-500 lg:text-[#786126] hover:text-gray-700 lg:hover:text-[#634600] hover:bg-gray-100 lg:hover:bg-[#B59F84] lg:hover:bg-opacity-20 rounded-full transition-colors flex-shrink-0 cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <input type="file" id="image-upload" name="image" accept="image/*" class="hidden">
                                </label>
                                
                                <div class="flex-1 relative min-w-0">
                                    <input
                                        type="text"
                                        name="message"
                                        id="message-input"
                                        class="w-full border border-gray-300 lg:border-[#B59F84] rounded-full px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-[#634600] focus:border-[#634600] resize-none text-sm bg-white text-gray-900 placeholder-gray-500 min-h-[44px]"
                                        placeholder="Type a message or select an image..."
                                        autocomplete="off"
                                    >
                                    <!-- Emoji Button -->
                                    <button type="button" id="emoji-toggle" class="absolute right-2 top-1/2 transform -translate-y-1/2 p-1 text-gray-400 lg:text-[#786126] hover:text-gray-600 lg:hover:text-[#634600]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <button
                                    type="submit"
                                    class="bg-gradient-to-r from-[#634600] to-[#B59F84] hover:from-[#56432C] hover:to-[#a08e77] text-white p-3 rounded-full transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#634600] focus:ring-offset-2 flex-shrink-0 min-h-[44px]"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('message')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Search functionality for conversations
function initConversationSearch() {
    const desktopSearchInput = document.getElementById('desktop-search-input');
    const mobileSearchInput = document.getElementById('mobile-search-input');
    const clearDesktopSearch = document.getElementById('clear-desktop-search');
    const clearMobileSearch = document.getElementById('clear-mobile-search');
    const conversationsList = document.getElementById('conversations-list');
    const noSearchResults = document.getElementById('no-search-results');
    const conversationItems = document.querySelectorAll('.conversation-item');
    const emptyState = conversationsList.querySelector('.flex.items-center.justify-center.h-full');

    // Function to perform search
    function performSearch(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        let hasVisibleResults = false;

        if (term === '') {
            // Show all conversations
            conversationItems.forEach(item => {
                item.classList.remove('hidden');
            });
            if (emptyState) {
                emptyState.classList.remove('hidden');
            }
            noSearchResults.classList.add('hidden');
            return;
        }

        // Hide empty state during search
        if (emptyState) {
            emptyState.classList.add('hidden');
        }

        // Filter conversations
        conversationItems.forEach(item => {
            const userName = item.getAttribute('data-user-name') || '';
            const latestMessage = item.getAttribute('data-latest-message') || '';
            
            if (userName.includes(term) || latestMessage.includes(term)) {
                item.classList.remove('hidden');
                hasVisibleResults = true;
                
                // Highlight matching text
                highlightText(item, term);
            } else {
                item.classList.add('hidden');
            }
        });

        // Show/hide no results message
        if (hasVisibleResults) {
            noSearchResults.classList.add('hidden');
        } else {
            noSearchResults.classList.remove('hidden');
        }
    }

    // Function to highlight matching text
    function highlightText(item, term) {
        const nameElement = item.querySelector('.conversation-name');
        const messageElement = item.querySelector('.conversation-message');
        
        if (nameElement) {
            const originalName = nameElement.getAttribute('data-original-text') || nameElement.textContent;
            nameElement.setAttribute('data-original-text', originalName);
            const highlightedName = originalName.replace(
                new RegExp(term, 'gi'),
                match => `<span class="bg-yellow-200 text-[#634600] px-1 rounded">${match}</span>`
            );
            nameElement.innerHTML = highlightedName;
        }
        
        if (messageElement) {
            const originalMessage = messageElement.getAttribute('data-original-text') || messageElement.textContent;
            messageElement.setAttribute('data-original-text', originalMessage);
            const highlightedMessage = originalMessage.replace(
                new RegExp(term, 'gi'),
                match => `<span class="bg-yellow-200 text-[#634600] px-1 rounded">${match}</span>`
            );
            messageElement.innerHTML = highlightedMessage;
        }
    }

    // Function to remove highlighting
    function removeHighlighting() {
        conversationItems.forEach(item => {
            const nameElement = item.querySelector('.conversation-name');
            const messageElement = item.querySelector('.conversation-message');
            
            if (nameElement && nameElement.getAttribute('data-original-text')) {
                nameElement.textContent = nameElement.getAttribute('data-original-text');
                nameElement.removeAttribute('data-original-text');
            }
            
            if (messageElement && messageElement.getAttribute('data-original-text')) {
                messageElement.textContent = messageElement.getAttribute('data-original-text');
                messageElement.removeAttribute('data-original-text');
            }
        });
    }

    // Function to clear search
    function clearSearch(inputElement, clearButton) {
        inputElement.value = '';
        clearButton.classList.add('hidden');
        removeHighlighting();
        performSearch('');
    }

    // Desktop search event listeners
    if (desktopSearchInput && clearDesktopSearch) {
        desktopSearchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value;
            performSearch(searchTerm);
            
            // Show/hide clear button
            if (searchTerm.length > 0) {
                clearDesktopSearch.classList.remove('hidden');
            } else {
                clearDesktopSearch.classList.add('hidden');
                removeHighlighting();
            }
        });

        clearDesktopSearch.addEventListener('click', function() {
            clearSearch(desktopSearchInput, clearDesktopSearch);
        });

        // Clear search on escape key
        desktopSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearSearch(desktopSearchInput, clearDesktopSearch);
                desktopSearchInput.blur();
            }
        });
    }

    // Mobile search event listeners
    if (mobileSearchInput && clearMobileSearch) {
        mobileSearchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value;
            performSearch(searchTerm);
            
            // Show/hide clear button
            if (searchTerm.length > 0) {
                clearMobileSearch.classList.remove('hidden');
            } else {
                clearMobileSearch.classList.add('hidden');
                removeHighlighting();
            }
        });

        clearMobileSearch.addEventListener('click', function() {
            clearSearch(mobileSearchInput, clearMobileSearch);
        });

        // Clear search on escape key
        mobileSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearSearch(mobileSearchInput, clearMobileSearch);
                mobileSearchInput.blur();
            }
        });
    }

    // Sync search between desktop and mobile
    function syncSearch() {
        if (desktopSearchInput && mobileSearchInput) {
            desktopSearchInput.addEventListener('input', function() {
                mobileSearchInput.value = desktopSearchInput.value;
                if (mobileSearchInput.value.length > 0) {
                    clearMobileSearch.classList.remove('hidden');
                } else {
                    clearMobileSearch.classList.add('hidden');
                }
            });

            mobileSearchInput.addEventListener('input', function() {
                desktopSearchInput.value = mobileSearchInput.value;
                if (desktopSearchInput.value.length > 0) {
                    clearDesktopSearch.classList.remove('hidden');
                } else {
                    clearDesktopSearch.classList.add('hidden');
                }
            });
        }
    }

    syncSearch();

    // Debounce search to improve performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Apply debouncing to search inputs
    const debouncedSearch = debounce((searchTerm) => {
        performSearch(searchTerm);
    }, 300);

    if (desktopSearchInput) {
        desktopSearchInput.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    }

    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    }
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        initConversationSearch();
    }, 100);
});
    // Debug function to check WebRTC support
    function checkWebRTCSupport() {
        const support = {
            getUserMedia: !!navigator.mediaDevices?.getUserMedia,
            RTCPeerConnection: !!window.RTCPeerConnection,
            RTCSessionDescription: !!window.RTCSessionDescription,
            RTCIceCandidate: !!window.RTCIceCandidate
        };
        
        console.log('WebRTC Support:', support);
        return support;
    }
    
    // Test Pusher authentication
    async function testPusherAuth() {
        const currentUserId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        
        if (!currentUserId) {
            console.log('❌ User ID not found');
            return false;
        }
        
        if (!window.Echo) {
            console.log('❌ Echo not available');
            return false;
        }
        
        try {
            // Try to subscribe to the private channel
            const channel = window.Echo.private(`chat.user.${currentUserId}`);
            console.log('✅ Pusher channel subscribed successfully');
            
            // Test a whisper
            channel.whisper('test', { message: 'test', timestamp: Date.now() });
            console.log('✅ Pusher whisper test sent');
            
            return true;
        } catch (error) {
            console.error('❌ Pusher error:', error);
            return false;
        }
    }

    // Improved WebRTC Configuration with better STUN servers
    function getRTCConfiguration() {
        return {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' },
                { urls: 'stun:stun2.l.google.com:19302' },
                { urls: 'stun:stun3.l.google.com:19302' },
                { urls: 'stun:stun4.l.google.com:19302' },
                { 
                    urls: 'turn:openrelay.metered.ca:80',
                    username: 'openrelayproject',
                    credential: 'openrelayproject'
                },
                { 
                    urls: 'turn:openrelay.metered.ca:443',
                    username: 'openrelayproject',
                    credential: 'openrelayproject'
                }
            ],
            iceCandidatePoolSize: 10
        };
    }

    // Utility function for retrying operations
    function sendWithRetry(operation, maxRetries) {
        return new Promise((resolve, reject) => {
            let attempts = 0;
            
            function attempt() {
                attempts++;
                try {
                    const result = operation();
                    if (result) {
                        resolve(result);
                    } else {
                        resolve();
                    }
                } catch (error) {
                    if (attempts <= maxRetries) {
                        console.log(`Retry ${attempts}/${maxRetries} after error:`, error);
                        setTimeout(attempt, 1000 * attempts);
                    } else {
                        reject(error);
                    }
                }
            }
            
            attempt();
        });
    }

    // Update call status display
    function updateCallStatus(status) {
        const statusElement = document.getElementById('call-status');
        if (!statusElement) return;
        
        const statusMap = {
            'new': 'Starting...',
            'connecting': 'Connecting...',
            'connected': 'Connected',
            'disconnected': 'Disconnected',
            'failed': 'Connection Failed',
            'closed': 'Call Ended'
        };
        
        statusElement.textContent = statusMap[status] || status;
    }

    // ICE restart function for connection recovery
    function restartIce(recipientId, currentUserId) {
        if (!window.peerConnection) return;
        
        console.log('Restarting ICE...');
        window.peerConnection.restartIce();
        
        // Create a new offer
        setTimeout(() => {
            createAndSendOffer(recipientId, currentUserId);
        }, 1000);
    }

    // Emoji picker functionality
    function initEmojiPicker() {
        const emojiToggle = document.getElementById('emoji-toggle');
        const emojiPicker = document.getElementById('emoji-picker');
        const messageInput = document.getElementById('message-input');

        if (!emojiToggle || !emojiPicker || !messageInput) {
            console.log('Emoji elements not found, retrying...');
            setTimeout(initEmojiPicker, 100);
            return;
        }

        console.log('Initializing emoji picker...');

        emojiToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Emoji toggle clicked');
            emojiPicker.classList.toggle('hidden');
        });

        // Close emoji picker when clicking outside
        document.addEventListener('click', function(e) {
            if (emojiToggle && emojiPicker && !emojiToggle.contains(e.target) && !emojiPicker.contains(e.target)) {
                emojiPicker.classList.add('hidden');
            }
        });

        // Emoji selection
        document.querySelectorAll('.emoji-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const emoji = this.getAttribute('data-emoji');
                const currentValue = messageInput.value;
                const cursorPos = messageInput.selectionStart;
                const newValue = currentValue.slice(0, cursorPos) + emoji + currentValue.slice(cursorPos);
                messageInput.value = newValue;
                messageInput.focus();
                messageInput.setSelectionRange(cursorPos + emoji.length, cursorPos + emoji.length);
                emojiPicker.classList.add('hidden');
                console.log('Emoji selected:', emoji);
            });
        });
    }

    // Enhanced Mobile navigation functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Check WebRTC support on page load
        checkWebRTCSupport();
        
        // Test Pusher authentication
        setTimeout(() => {
            testPusherAuth();
        }, 2000);
        
        // Initialize emoji picker when DOM is ready
        initEmojiPicker();
        const conversationsToggle = document.getElementById('conversations-toggle');
        const conversationsSidebar = document.getElementById('conversations-sidebar');
        const closeConversations = document.getElementById('close-conversations');
        const mobileBackButton = document.getElementById('mobile-back-button');
        const chatArea = document.querySelector('.flex-1.flex-col');

        // Add smooth slide animations
        function showConversations() {
            conversationsSidebar.classList.remove('hidden');
            conversationsSidebar.style.transform = 'translateX(0)';
            chatArea.style.transform = 'translateX(100%)';
            setTimeout(() => {
                chatArea.classList.add('hidden');
            }, 300);
        }

        function showChat() {
            conversationsSidebar.style.transform = 'translateX(-100%)';
            chatArea.classList.remove('hidden');
            chatArea.style.transform = 'translateX(0)';
            setTimeout(() => {
                conversationsSidebar.classList.add('hidden');
            }, 300);
        }

        // Initialize transforms
        conversationsSidebar.style.transition = 'transform 0.3s ease-in-out';
        chatArea.style.transition = 'transform 0.3s ease-in-out';

        // Toggle conversations sidebar on mobile
        if (conversationsToggle) {
            conversationsToggle.addEventListener('click', function() {
                showConversations();
            });
        }

        // Close conversations sidebar on mobile
        if (closeConversations) {
            closeConversations.addEventListener('click', function() {
                showChat();
            });
        }

        // Mobile back button functionality
        if (mobileBackButton) {
            mobileBackButton.addEventListener('click', function() {
                showConversations();
            });
        }

        // Close sidebar when clicking on a conversation (mobile)
        const conversationLinks = document.querySelectorAll('a[href*="private.chat"]');
        conversationLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    showChat();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                conversationsSidebar.classList.remove('hidden');
                chatArea.classList.remove('hidden');
                conversationsSidebar.style.transform = '';
                chatArea.style.transform = '';
            }
        });

        // Settings dropdown toggle
        const settingsToggleBtn = document.getElementById('settings-toggle-btn');
        const settingsDropdownMenu = document.getElementById('settings-dropdown-menu');
        
        if (settingsToggleBtn && settingsDropdownMenu) {
            settingsToggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                settingsDropdownMenu.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!settingsToggleBtn.contains(e.target) && !settingsDropdownMenu.contains(e.target)) {
                    settingsDropdownMenu.classList.add('hidden');
                }
            });
        }
    });

    // Image upload functionality
    const imageUpload = document.getElementById('image-upload');
    const imagePreviewContainer = document.getElementById('image-preview-container');
    const imagePreview = document.getElementById('image-preview');
    const imageFilename = document.getElementById('image-filename');
    const removeImageBtn = document.getElementById('remove-image');

    console.log('Image upload elements:', {
        imageUpload: !!imageUpload,
        imagePreviewContainer: !!imagePreviewContainer,
        imagePreview: !!imagePreview,
        imageFilename: !!imageFilename,
        removeImageBtn: !!removeImageBtn
    });

    imageUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        console.log('Image file selected:', file);
        if (file) {
            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                showNotification('Image size must be less than 10MB', 'error');
                imageUpload.value = '';
                return;
            }
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showNotification('Please select a valid image file', 'error');
                imageUpload.value = '';
                return;
            }
            
            console.log('File details:', {
                name: file.name,
                size: file.size,
                type: file.type
            });
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imageFilename.textContent = file.name;
                imagePreviewContainer.classList.remove('hidden');
                console.log('Image preview loaded');
            };
            reader.readAsDataURL(file);
        }
    });

    // Function to remove image
    function removeImage() {
        imageUpload.value = '';
        imagePreviewContainer.classList.add('hidden');
        imagePreview.src = '';
        imageFilename.textContent = '';
    }
    
    // Remove image button click
    removeImageBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        removeImage();
    });
    
    // Remove image when clicking on preview
    imagePreview.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        removeImage();
    });
    
    // Remove image when clicking on preview container
    imagePreviewContainer.addEventListener('click', function(e) {
        if (e.target === imagePreviewContainer || e.target.closest('#remove-image')) {
            return; // Let the remove button handle it
        }
        if (e.target === imagePreview || imagePreview.contains(e.target)) {
            removeImage();
        }
    });


    // Chat functionality
    document.getElementById('private-chat-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const url = form.action;
        const messageInput = form.querySelector('input[name="message"]');
        const message = messageInput.value.trim();
        const imageFile = form.querySelector('input[name="image"]').files[0];
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const messagesContainer = document.getElementById('private-messages-container');

        console.log('Form submission:', { 
            message, 
            imageFile: imageFile ? {
                name: imageFile.name,
                size: imageFile.size,
                type: imageFile.type
            } : null,
            hasImage: !!imageFile 
        });

        if (!message && !imageFile) {
            console.log('No message or image provided');
            showNotification('Please enter a message or select an image', 'error');
            return; // Do not send empty messages
        }

        // Show typing indicator
        showTypingIndicator();

        // Create FormData for file upload
        const formData = new FormData();
        formData.append('message', message);
        if (imageFile) {
            formData.append('image', imageFile);
        }
        formData.append('_token', token);

        console.log('Sending request to:', url);
        console.log('FormData contents:');
        for (let pair of formData.entries()) {
            if (pair[1] instanceof File) {
                console.log(pair[0] + ': [File] ' + pair[1].name + ' (' + pair[1].size + ' bytes, ' + pair[1].type + ')');
            } else {
                console.log(pair[0] + ': ' + pair[1]);
            }
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
                // Don't set Content-Type for FormData - browser sets it automatically with boundary
            },
            body: formData
        })
        .then(async response => {
            console.log('Response status:', response.status);
            
            // Try to parse JSON, but handle errors gracefully
            let data;
            try {
                data = await response.json();
            } catch (e) {
                console.error('Failed to parse JSON response:', e);
                throw new Error('Invalid response from server');
            }
            
            if (!response.ok) {
                console.error('Response not ok:', response.status, response.statusText, data);
                const errorMessage = data.error || data.message || 'Failed to send message. Please try again.';
                throw new Error(errorMessage);
            }
            
            // Check if response has error field
            if (data.error) {
                console.error('Error in response:', data.error);
                throw new Error(data.error);
            }
            
            // Verify message structure
            if (!data.message) {
                console.error('No message in response:', data);
                throw new Error('Invalid response: no message data');
            }
            
            console.log('Message sent successfully:', data.message);
            return data;
        })
        .then(data => {
            // Clear input and image
            messageInput.value = '';
            imageUpload.value = '';
            imagePreviewContainer.classList.add('hidden');
            imagePreview.src = '';
            imageFilename.textContent = '';
            
            // Remove typing indicator
            removeTypingIndicator();

            // Construct full user name safely
            if (!data.message.user) {
                console.error('No user in message:', data.message);
                showNotification('Error: User data missing', 'error');
                return;
            }
            
            const user = data.message.user;
            const userFullName = `${user.fname ?? ''} ${user.lname ?? ''}`.trim();

            // Helper: strip raw product URLs from message text (we render a card instead)
            function stripProductLinks(text) {
                if (!text) return '';
                return text.replace(/https?:\/\/[^\s]+\/products\/\d+/g, '').trim();
            }

            // Create new message HTML
            let messageContent = '';

            // Handle image (check both image_path and image_url)
            if (data.message.image_path || data.message.image_url) {
                const imageSrc = data.message.image_url || `/storage/${data.message.image_path}`;
                // Use inline SVG as fallback instead of external image
                const fallbackSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Crect fill='%23ddd' width='200' height='200'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='14' x='50%25' y='50%25' text-anchor='middle' dominant-baseline='middle'%3EImage%3C/text%3E%3C/svg%3E";
                messageContent += `
                    <div class="mb-2">
                        <img src="${imageSrc}" 
                             alt="Shared image" 
                             class="max-w-full h-auto rounded-lg shadow-sm"
                             onerror="this.onerror=null; this.src='${fallbackSvg}'; this.alt='Image failed to load'; console.error('Failed to load image:', '${imageSrc}');">
                    </div>
                `;
            }

            // Message text (with product links stripped and HTML-escaped)
            if (data.message.message && data.message.message.trim()) {
                const cleanedText = stripProductLinks(data.message.message);
                if (cleanedText) {
                    messageContent += `
                        <div class="text-sm whitespace-pre-line break-words">
                            ${escapeHtml(cleanedText).replace(/\n/g, '<br>')}
                        </div>
                    `;
                }
            }

            // Product preview card (if backend attached product_preview metadata)
            let productPreviewHtml = '';
            const preview = data.message.product_preview || null;
            if (preview && preview.id) {
                const previewImage = preview.image_url || 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EProduct%3C/text%3E%3C/svg%3E';
                const isDonation = preview.listingtype === 'for donation';
                const priceLabel = isDonation
                    ? 'For Donation'
                    : `₱${Number(preview.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                productPreviewHtml = `
                    <div class="mt-3 p-3 bg-white bg-opacity-95 rounded-xl border border-white border-opacity-40 shadow-lg group hover:bg-opacity-100 transition-all duration-200">
                        <div class="flex gap-3">
                            <!-- Product Image -->
                            <div class="relative flex-shrink-0">
                                <img src="${previewImage}" 
                                     alt="${escapeHtml(preview.name || 'Product')}"
                                     class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-sm"
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EProduct%3C/text%3E%3C/svg%3E'; this.alt='Image failed to load';">
                                ${isDonation ? `
                                    <div class="absolute -top-1 -right-1 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                        FREE
                                    </div>` : ''}
                            </div>
                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-1">
                                    <h4 class="font-semibold text-sm sm:text-base text-gray-900 truncate pr-2">
                                        ${escapeHtml(preview.name || 'Product')}
                                    </h4>
                                    <div class="flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 mb-2 flex items-center">
                                    <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    ${preview.category || 'Product'}
                                </p>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-bold ${isDonation ? 'text-green-600' : 'text-[#634600]'} flex items-center">
                                        ${isDonation ? `
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                            Free` : `
                                            <svg class="w-3 h-3 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                            ${priceLabel}`}
                                    </p>
                                    ${preview.url ? `
                                        <a href="${preview.url}" target="_blank"
                                           class="text-xs text-[#634600] hover:text-[#56432C] font-medium flex items-center">
                                            <span>View</span>
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            const newMessageHTML = `
                <div class="flex justify-end message-item">
                    <div class="flex max-w-[85%] sm:max-w-xs lg:max-w-md flex-row-reverse items-end space-x-2">
                        <!-- Avatar -->
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0 ml-1 sm:ml-2">
                           ${user.profile_pic_url 
                                ? `<img src="${user.profile_pic_url}" alt="${user.fname} ${user.lname}" class="w-full h-full rounded-full object-cover">`
                                : `<div class="w-full h-full bg-gradient-to-r from-[#634600] to-[#B59F84] flex items-center justify-center">
                                    <span class="text-white text-xs font-semibold">${user.fname.charAt(0)}${user.lname.charAt(0)}</span>
                                </div>`
                            }
                        </div>
                        <!-- Message Bubble -->
                        <div class="flex flex-col items-end">
                            <div class="px-4 py-3 rounded-2xl bg-gradient-to-r from-[#634600] to-[#B59F84] text-white rounded-br-md">
                                ${messageContent}
                                ${productPreviewHtml}
                            </div>
                            <div class="mt-1 text-right">
                                <span class="text-xs text-gray-500 lg:text-[#786126] px-2">just now</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove empty state if it exists
            const emptyState = messagesContainer.querySelector('.flex.items-center.justify-center.h-full');
            if (emptyState) {
                emptyState.remove();
            }

            // Add the new message
            messagesContainer.insertAdjacentHTML('beforeend', newMessageHTML);
            
            // Scroll to bottom after sending message
            setTimeout(() => {
                scrollToBottom(true);
            }, 100);
            
            // Note: We don't mark messages as read when sending a message
            // Messages are only marked as read when viewing them (via Intersection Observer)
        })
        .catch(error => {
            console.error('Error sending message:', error);
            removeTypingIndicator();
            const errorMessage = error.message || 'Failed to send message. Please try again.';
            showNotification(errorMessage, 'error');
            
            // Don't clear the image preview on error so user can retry
            // Keep the form state so user can fix and resubmit
        });
    });

    // Utility to escape HTML
    function escapeHtml(string) {
        if (!string) return '';
        return string
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

 // Override global createMessageBubble from Echo bundle so avatars persist without rebuild
window.createMessageBubble = function(message, sender, isOwnMessage) {
    const containerJustify = isOwnMessage ? 'justify-end' : 'justify-start';
    const rowDirection = isOwnMessage ? 'flex-row-reverse' : 'flex-row';
    const avatarSpacing = isOwnMessage ? 'ml-1 sm:ml-2' : 'mr-1 sm:mr-2';
    const bubbleAlignment = isOwnMessage ? 'items-end' : 'items-start';
    const fallbackGradient = isOwnMessage ? 'from-[#634600] to-[#B59F84]' : 'from-[#B59F84] to-[#786126]';
    const bubbleClasses = isOwnMessage
        ? 'bg-gradient-to-r from-[#634600] to-[#B59F84] text-white rounded-br-md'
        : 'bg-white text-[#634600] rounded-bl-md shadow-sm border border-[#B59F84]';

    const avatarUrl = sender?.profile_pic_url || sender?.profile_pic || sender?.profileImageUrl || '';
    const initials = `${(sender?.fname || '?').charAt(0)}${(sender?.lname || '?').charAt(0)}`.toUpperCase();
    const avatarInner = avatarUrl
        ? `<img src="${avatarUrl}" alt="${sender?.fname || ''} ${sender?.lname || ''}" class="w-full h-full object-cover rounded-full">`
        : `<div class="w-full h-full bg-gradient-to-r ${fallbackGradient} rounded-full flex items-center justify-center">
                <span class="text-white text-xs font-semibold">${initials}</span>
           </div>`;

    const imageSrc = message?.image_url
        || (message?.image_path ? `/storage/${message.image_path}` : '');
    // Use inline SVG as fallback instead of external image
    const fallbackSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Crect fill='%23ddd' width='200' height='200'/%3E%3Ctext fill='%23999' font-family='sans-serif' font-size='14' x='50%25' y='50%25' text-anchor='middle' dominant-baseline='middle'%3EImage%3C/text%3E%3C/svg%3E";
    const imageHTML = imageSrc
        ? `<div class="mb-2">
            <img src="${imageSrc}" 
                 alt="Shared image" 
                 class="max-w-full h-auto rounded-lg shadow-sm"
                 onerror="this.onerror=null; this.src='${fallbackSvg}'; this.alt='Image failed to load'; console.error('Failed to load image:', '${imageSrc}');">
           </div>`
        : '';

    // Strip raw product and donation links from message text (we render cards instead) and escape HTML
    function stripLinks(text) {
        if (!text) return '';
        return text.replace(/https?:\/\/[^\s]+\/(products|donations)\/\d+/g, '').trim();
    }

    const rawMessageText = message?.message || '';
    const cleanedMessageText = stripLinks(rawMessageText);
    const safeMessage = cleanedMessageText && cleanedMessageText.trim() !== ''
        ? escapeHtml(cleanedMessageText).replace(/\n/g, '<br>')
        : '';

    // Product preview card (for realtime Echo messages)
    let productPreviewHtml = '';
    const preview = message?.product_preview || null;
    if (preview && preview.id) {
        const previewImage = preview.image_url || 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EProduct%3C/text%3E%3C/svg%3E';
        const isDonation = preview.listingtype === 'for donation';
        const priceLabel = isDonation
            ? 'For Donation'
            : `₱${Number(preview.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        productPreviewHtml = `
            <div class="mt-3 p-3 bg-white bg-opacity-95 rounded-xl border border-white border-opacity-40 shadow-lg group hover:bg-opacity-100 transition-all duration-200">
                <div class="flex gap-3">
                    <!-- Product Image -->
                    <div class="relative flex-shrink-0">
                        <img src="${previewImage}" 
                             alt="${escapeHtml(preview.name || 'Product')}"
                             class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-sm"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EProduct%3C/text%3E%3C/svg%3E'; this.alt='Image failed to load';">
                        ${isDonation ? `
                            <div class="absolute -top-1 -right-1 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                FREE
                            </div>` : ''}
                    </div>
                    <!-- Product Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-1">
                            <h4 class="font-semibold text-sm sm:text-base text-gray-900 truncate pr-2">
                                ${escapeHtml(preview.name || 'Product')}
                            </h4>
                            <div class="flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mb-2 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            ${preview.category || 'Product'}
                        </p>
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold ${isDonation ? 'text-green-600' : 'text-[#634600]'} flex items-center">
                                ${isDonation ? `
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    Free` : `
                                    <svg class="w-3 h-3 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    ${priceLabel}`}
                            </p>
                            ${preview.url ? `
                                <a href="${preview.url}" target="_blank"
                                   class="text-xs text-[#634600] hover:text-[#56432C] font-medium flex items-center">
                                    <span>View</span>
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Donation preview card (for realtime Echo messages with donation links)
   // Add this to your createMessageBubble function or update the existing one
// Donation preview card for realtime Echo messages
// Donation preview card (for realtime Echo messages with donation links)
let donationPreviewHtml = '';
const donationUrlPattern = /\/donations\/(\d+)/;
const donationMatch = rawMessageText.match(donationUrlPattern);
const donationId = donationMatch ? donationMatch[1] : null;

if (donationId) {
    const donationPlaceholderSvg = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dominant-baseline=\'middle\'%3EDonation%3C/text%3E%3C/svg%3E';
    donationPreviewHtml = `
        <div class="mt-3 p-3 bg-white bg-opacity-95 rounded-xl border border-white border-opacity-40 shadow-lg group hover:bg-opacity-100 transition-all duration-200">
            <div class="flex gap-3">
                <div class="relative flex-shrink-0">
                    <img src="${donationPlaceholderSvg}" 
                         alt="Donation Item"
                         class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-sm"
                         onerror="this.onerror=null; this.src='${donationPlaceholderSvg}'; this.alt='Image failed to load';">
                    <div class="absolute -top-1 -right-1 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                        FREE
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="mb-1">
                        <h4 class="font-semibold text-sm sm:text-base text-gray-900 truncate">Donation Item</h4>
                    </div>
                    <p class="text-xs text-gray-600 mb-2 flex items-center">
                        <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        For Donation
                    </p>
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-green-600 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            Free
                        </p>
                        <a href="/donations/${donationId}" target="_blank" 
                           class="text-xs text-[#634600] hover:text-[#56432C] font-medium flex items-center">
                            <span>View</span>
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
}

    return `
        <div class="flex ${containerJustify} message-item">
            <div class="flex max-w-[85%] sm:max-w-xs lg:max-w-md ${rowDirection} items-end space-x-2">
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0 ${avatarSpacing}">
                    ${avatarInner}
                </div>
                <div class="flex flex-col ${bubbleAlignment}">
                    <div class="px-4 py-3 rounded-2xl text-sm ${bubbleClasses}">
                        ${imageHTML}
                        ${safeMessage ? `<div class="text-sm whitespace-pre-line break-words">${safeMessage}</div>` : ''}
                        ${productPreviewHtml}
                        ${donationPreviewHtml}
                    </div>
                    <div class="mt-1 ${isOwnMessage ? 'text-right' : 'text-left'}">
                        <span class="text-xs text-gray-500 lg:text-[#786126] px-2">just now</span>
                    </div>
                </div>
            </div>
        </div>
    `;
};

    function showTypingIndicator() {
    const messagesContainer = document.getElementById('private-messages-container');
    
    // Remove existing typing indicator if any
    removeTypingIndicator();
    
    // Get recipient info with fallbacks
    const recipientProfilePicUrl = document.querySelector('meta[name="recipient-profile-pic-url"]')?.content || '';
    const recipientInitials = document.querySelector('meta[name="recipient-name"]')?.content || 'U';
    const recipientName = '{{ $recipient->fname }} {{ $recipient->lname }}' || 'User';
    
    // Create avatar HTML with better error handling
    let avatarHTML = '';
    if (recipientProfilePicUrl && recipientProfilePicUrl !== '') {
        avatarHTML = `
            <img src="${recipientProfilePicUrl}" 
                 alt="${recipientName}" 
                 class="w-full h-full object-cover rounded-full"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="w-full h-full bg-gradient-to-r from-[#B59F84] to-[#786126] rounded-full flex items-center justify-center hidden">
                <span class="text-white text-xs font-semibold">${recipientInitials}</span>
            </div>
        `;
    } else {
        avatarHTML = `
            <div class="w-full h-full bg-gradient-to-r from-[#B59F84] to-[#786126] rounded-full flex items-center justify-center">
                <span class="text-white text-xs font-semibold">${recipientInitials}</span>
            </div>
        `;
    }
    
    const typingHTML = `
        <div class="flex justify-start" id="typing-indicator">
            <div class="flex max-w-[85%] sm:max-w-xs lg:max-w-md flex-row items-end space-x-2">
                <!-- Avatar with profile picture -->
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0 mr-1 sm:mr-2 overflow-hidden relative">
                    ${avatarHTML}
                </div>
                <!-- Typing bubble -->
                <div class="flex flex-col items-start">
                    <div class="px-4 py-3 rounded-2xl bg-white text-[#634600] rounded-bl-md shadow-sm border border-[#B59F84]">
                        <div class="flex space-x-1 items-center">
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-[#786126] rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-[#786126] rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-[#786126] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    messagesContainer.insertAdjacentHTML('beforeend', typingHTML);
    scrollToBottom(true);
}

    function removeTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-3 sm:p-4 rounded-lg shadow-lg z-50 text-sm sm:text-base ${
            type === 'error' ? 'bg-red-500 text-white' : 'bg-green-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Enter key to send message
    const messageInput = document.getElementById('message-input');
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('private-chat-form').dispatchEvent(new Event('submit'));
        }
    });

    // Enhanced scroll functionality
    function scrollToBottom(smooth = true) {
        const messagesContainer = document.getElementById('private-messages-container');
        if (messagesContainer) {
            if (smooth) {
                messagesContainer.scrollTo({
                    top: messagesContainer.scrollHeight,
                    behavior: 'smooth'
                });
            } else {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }
    }

    function checkScrollPosition() {
        const messagesContainer = document.getElementById('private-messages-container');
        const scrollButton = document.getElementById('scroll-to-bottom');
        
        if (messagesContainer && scrollButton) {
            const isAtBottom = messagesContainer.scrollTop + messagesContainer.clientHeight >= messagesContainer.scrollHeight - 100;
            
            if (isAtBottom) {
                scrollButton.classList.add('hidden');
            } else {
                scrollButton.classList.remove('hidden');
            }
        }
    }

    // Add scroll event listener
    const messagesContainer = document.getElementById('private-messages-container');
    if (messagesContainer) {
        messagesContainer.addEventListener('scroll', checkScrollPosition);
    }

    // Scroll to bottom button functionality
    const scrollButton = document.getElementById('scroll-to-bottom');
    if (scrollButton) {
        scrollButton.addEventListener('click', function() {
            scrollToBottom(true);
        });
    }

    // Mark messages as read when they come into view
    function markMessagesAsRead() {
        const unreadMessages = document.querySelectorAll('.unread-message');
        if (unreadMessages.length === 0) return;
        
        const messageIds = Array.from(unreadMessages).map(msg => msg.getAttribute('data-message-id'));
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const recipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
        
        fetch('/messages/mark-as-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message_ids: messageIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove unread styling
                unreadMessages.forEach(msg => {
                    msg.classList.remove('unread-message');
                    // Find the message bubble (the div with rounded-2xl class)
                    const bubble = msg.querySelector('[class*="rounded-2xl"]');
                    if (bubble && bubble.classList.contains('bg-blue-50')) {
                        bubble.classList.remove('bg-blue-50', 'border-2', 'border-blue-300', 'font-semibold', 'shadow-md');
                        bubble.classList.add('bg-white', 'border', 'border-[#B59F84]', 'shadow-sm');
                    }
                    // Remove bold from text
                    const text = msg.querySelector('.text-sm.whitespace-pre-line');
                    if (text && text.classList.contains('font-bold')) {
                        text.classList.remove('font-bold');
                    }
                });
                
                // Get updated unread count for this conversation and total unread count
                if (recipientId) {
                    // Get conversation-specific unread count
                    fetch(`/messages/conversation-unread-count/${recipientId}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(countData => {
                        // Dispatch event to update conversation badge
                        window.dispatchEvent(new CustomEvent('conversation-read', {
                            detail: {
                                user_id: parseInt(recipientId),
                                unread_count: countData.unread_count || 0
                            }
                        }));
                    })
                    .catch(error => {
                        console.warn('Error fetching conversation unread count:', error);
                    });
                    
                    // Get total unread count across all conversations
                    fetch('/messages/unread-count', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(totalCountData => {
                        // Dispatch event to update navigation badge
                        window.dispatchEvent(new CustomEvent('messages-marked-read', {
                            detail: {
                                unread_count: totalCountData.unread_count || 0
                            }
                        }));
                    })
                    .catch(error => {
                        console.warn('Error fetching total unread count:', error);
                    });
                }
            }
        })
        .catch(error => {
            console.warn('Error marking messages as read:', error);
        });
    }

    // Use Intersection Observer to mark messages as read when they're visible
    function setupReadObserver() {
        const messagesContainer = document.getElementById('private-messages-container');
        if (!messagesContainer) return;
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && entry.target.classList.contains('unread-message')) {
                    // Mark as read after a short delay to ensure user sees it
                    setTimeout(() => {
                        markMessagesAsRead();
                    }, 1000);
                }
            });
        }, {
            root: messagesContainer,
            threshold: 0.5 // Mark as read when 50% visible
        });
        
        // Observe all unread messages
        document.querySelectorAll('.unread-message').forEach(msg => {
            observer.observe(msg);
        });
    }

    // Auto-scroll to bottom on page load
    window.addEventListener('load', function() {
        scrollToBottom(false); // Instant scroll on load
        
        // Mark all visible messages as read after page loads
        setTimeout(() => {
            markMessagesAsRead();
            setupReadObserver();
        }, 500);
    
        // Check for auto-message parameter and send message automatically
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto_message') === '1') {
            setTimeout(async () => {
                const messageInput = document.getElementById('message-input');
                const form = document.getElementById('private-chat-form');
                const imageUpload = document.getElementById('image-upload');
                
                if (messageInput && form) {
                    const productId = urlParams.get('product_id');
                    const productName = urlParams.get('product_name');
                    const productImage = urlParams.get('product_image');
                    
                    const donationId = urlParams.get('donation_id');
                    const donationName = urlParams.get('donation_name');
                    const donationImage = urlParams.get('donation_image');
                    
                    // Handle product auto-message
                    if (productId && productName) {
                        const productUrl = `${window.location.origin}/products/${productId}`;
                        let message = `Is this available?\n\n📦 ${decodeURIComponent(productName)}\n🔗 ${productUrl}`;
                        
                        messageInput.value = message;
                        
                        // Upload product image if provided
                        if (productImage && imageUpload) {
                            try {
                                // Decode the URL properly
                                const decodedImageUrl = decodeURIComponent(productImage);
                                console.log('Loading product image from:', decodedImageUrl);
                                
                                // Use proxy endpoint to avoid CORS issues
                                const proxyUrl = '{{ route("proxy.image") }}?url=' + encodeURIComponent(decodedImageUrl);
                                console.log('Fetching via proxy:', proxyUrl);
                                
                                const response = await fetch(proxyUrl);
                                if (!response.ok) {
                                    throw new Error(`Failed to fetch image: ${response.status} ${response.statusText}`);
                                }
                                const blob = await response.blob();
                                
                                if (!blob || blob.size === 0) {
                                    throw new Error('Received empty blob');
                                }
                                
                                console.log('Blob received:', { size: blob.size, type: blob.type });
                                
                                const file = new File([blob], 'product-image.jpg', { type: blob.type || 'image/jpeg' });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                imageUpload.files = dataTransfer.files;
                                
                                console.log('File set in input:', {
                                    fileCount: imageUpload.files.length,
                                    fileName: imageUpload.files[0]?.name,
                                    fileSize: imageUpload.files[0]?.size,
                                    fileType: imageUpload.files[0]?.type
                                });
                                
                                // Wait for the change event to process
                                await new Promise(resolve => {
                                    const timeout = setTimeout(resolve, 200);
                                    imageUpload.addEventListener('change', () => {
                                        clearTimeout(timeout);
                                        resolve();
                                    }, { once: true });
                                    imageUpload.dispatchEvent(new Event('change', { bubbles: true }));
                                });
                                
                                console.log('Product image loaded successfully');
                            } catch (error) {
                                console.error('Error loading product image:', error);
                                console.error('Error details:', {
                                    message: error.message,
                                    stack: error.stack,
                                    productImage: productImage
                                });
                                // Continue without image if fetch fails
                            }
                        }
                    }
                    // Handle donation auto-message
                    else if (donationId && donationName) {
                        const donationUrl = `${window.location.origin}/donations/${donationId}`;
                        let message = `Is this available?\n\n🎁 ${decodeURIComponent(donationName)}`;
                                            
                        messageInput.value = message;
                        
                        // Upload donation image if provided
                        if (donationImage && imageUpload) {
                            try {
                                // Decode the URL properly
                                const decodedImageUrl = decodeURIComponent(donationImage);
                                console.log('Loading donation image from:', decodedImageUrl);
                                
                                // Use proxy endpoint to avoid CORS issues
                                const proxyUrl = '{{ route("proxy.image") }}?url=' + encodeURIComponent(decodedImageUrl);
                                console.log('Fetching via proxy:', proxyUrl);
                                
                                const response = await fetch(proxyUrl);
                                
                                if (!response.ok) {
                                    throw new Error(`Failed to fetch image: ${response.status} ${response.statusText}`);
                                }
                                
                                const blob = await response.blob();
                                
                                if (!blob || blob.size === 0) {
                                    throw new Error('Received empty blob');
                                }
                                
                                console.log('Blob received:', { size: blob.size, type: blob.type });
                                
                                const file = new File([blob], 'donation-image.jpg', { 
                                    type: blob.type || 'image/jpeg' 
                                });
                                
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                imageUpload.files = dataTransfer.files;
                                
                                console.log('File set in input:', {
                                    fileCount: imageUpload.files.length,
                                    fileName: imageUpload.files[0]?.name,
                                    fileSize: imageUpload.files[0]?.size,
                                    fileType: imageUpload.files[0]?.type
                                });
                                
                                // Wait for the change event to process
                                await new Promise(resolve => {
                                    const timeout = setTimeout(resolve, 200);
                                    imageUpload.addEventListener('change', () => {
                                        clearTimeout(timeout);
                                        resolve();
                                    }, { once: true });
                                    imageUpload.dispatchEvent(new Event('change', { bubbles: true }));
                                });
                                
                                console.log('Donation image loaded successfully');
                            } catch (error) {
                                console.error('Error loading donation image:', error);
                                console.error('Error details:', {
                                    message: error.message,
                                    stack: error.stack,
                                    donationImage: donationImage
                                });
                                // Continue without image if fetch fails
                            }
                        }
                    } else {
                        messageInput.value = 'Is this available?';
                    }
                    
                    // Wait a bit for image to load, then submit
                    setTimeout(() => {
                        // Verify image is loaded if one was expected
                        if ((donationImage || productImage) && imageUpload) {
                            const hasImage = imageUpload.files && imageUpload.files.length > 0;
                            console.log('Image loaded check:', { 
                                expected: !!(donationImage || productImage), 
                                actual: hasImage,
                                fileCount: imageUpload.files ? imageUpload.files.length : 0
                            });
                        }
                        form.dispatchEvent(new Event('submit'));
                    }, (donationImage || productImage) ? 800 : 0);
                    
                    // Remove all auto-message parameters from URL
                    const newUrl = new URL(window.location);
                    newUrl.searchParams.delete('auto_message');
                    newUrl.searchParams.delete('product_id');
                    newUrl.searchParams.delete('product_name');
                    newUrl.searchParams.delete('product_image');
                    newUrl.searchParams.delete('donation_id');
                    newUrl.searchParams.delete('donation_name');
                    newUrl.searchParams.delete('donation_image');
                    window.history.replaceState({}, '', newUrl);
                }
            }, 1000);
        }
    });

    // Settings Functions
    function showMediaAndLinks() {
        // Close dropdown
        const dropdownMenu = document.getElementById('settings-dropdown-menu');
        if (dropdownMenu) {
            dropdownMenu.classList.add('hidden');
        }
        
        const messagesContainer = document.getElementById('private-messages-container');
        const messages = messagesContainer.querySelectorAll('.message-item');
        
        let mediaCount = 0;
        let linksCount = 0;
        const mediaItems = [];
        const linkItems = [];
        
        messages.forEach(msg => {
            // Check for images
            const images = msg.querySelectorAll('img[src*="storage"], img[src*="image_path"]');
            images.forEach(img => {
                if (img.src && (img.src.includes('storage') || img.src.includes('image_path'))) {
                    mediaCount++;
                    const messageDiv = msg.closest('.message-item');
                    if (messageDiv) {
                        mediaItems.push({
                            src: img.src,
                            alt: img.alt || 'Shared image',
                            message: messageDiv
                        });
                    }
                }
            });
            
            // Check for links in message text and product previews
            const links = msg.querySelectorAll('a[href*="products"]');
            links.forEach(link => {
                if (link.href && link.href.includes('/products/')) {
                    linksCount++;
                    linkItems.push({
                        href: link.href,
                        text: link.textContent.trim() || 'Product link',
                        message: msg.closest('.message-item')
                    });
                }
            });
            
            // Also check message text for product URLs
            const messageText = msg.textContent || '';
            const urlRegex = /https?:\/\/[^\s]+\/products\/(\d+)/g;
            let match;
            while ((match = urlRegex.exec(messageText)) !== null) {
                if (!linkItems.find(item => item.href === match[0])) {
                    linksCount++;
                    linkItems.push({
                        href: match[0],
                        text: 'Product link',
                        message: msg.closest('.message-item')
                    });
                }
            }
        });
        
        // Show modal with media and links
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[200]';
        modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Media & Links</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    ${mediaCount > 0 ? `
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Media (${mediaCount})</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                ${mediaItems.map(item => `
                                    <div class="relative group">
                                        <img src="${item.src}" alt="${item.alt}" class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity">
                                        <a href="${item.src}" target="_blank" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded-lg">
                                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    ${linksCount > 0 ? `
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Links (${linksCount})</h4>
                            <div class="space-y-2">
                                ${linkItems.map(item => `
                                    <a href="${item.href}" target="_blank" class="flex items-center p-2 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2 text-[#634600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">${item.text}</span>
                                        <svg class="w-4 h-4 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    ${mediaCount === 0 && linksCount === 0 ? `
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No media or links found in this conversation</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    function showBlockedUsers() {
        // Close dropdown
        const dropdownMenu = document.getElementById('settings-dropdown-menu');
        if (dropdownMenu) {
            dropdownMenu.classList.add('hidden');
        }
        
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Show loading state
        const modal = document.createElement('div');
        modal.id = 'blocked-users-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[200]';
        modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Blocked Users</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#634600] mx-auto"></div>
                        <p class="text-gray-500 dark:text-gray-400 mt-4">Loading blocked users...</p>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Fetch blocked users
        fetch('/users/blocked', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            if (!response.ok) {
                throw new Error('Failed to fetch blocked users');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateBlockedUsersModal(data.blocked_users);
            } else {
                throw new Error('Failed to load blocked users');
            }
        })
        .catch(error => {
            console.error('Error fetching blocked users:', error);
            modal.querySelector('.flex-1').innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Failed to load blocked users. Please try again.</p>
                </div>
            `;
        });
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
    
    function updateBlockedUsersModal(blockedUsers) {
        const modal = document.getElementById('blocked-users-modal');
        if (!modal) return;
        
        const contentDiv = modal.querySelector('.flex-1');
        
        if (blockedUsers.length === 0) {
            contentDiv.innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No blocked users</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">You haven't blocked any users yet.</p>
                </div>
            `;
            return;
        }
        
        contentDiv.innerHTML = `
            <div class="space-y-3">
                ${blockedUsers.map(user => `
                    <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden bg-gradient-to-r from-[#634600] to-[#B59F84] flex-shrink-0">
                                ${user.profile_pic_url 
                                    ? `<img src="${user.profile_pic_url}" alt="${user.fname} ${user.lname}" class="w-full h-full object-cover rounded-full">`
                                    : `<span class="text-white font-semibold text-sm">${user.fname.charAt(0)}${user.lname.charAt(0)}</span>`
                                }
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">${user.fname} ${user.lname}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Blocked ${new Date(user.blocked_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        <button onclick="unblockUser(${user.id}, '${user.fname} ${user.lname}')" 
                                class="ml-3 px-4 py-2 text-sm font-medium text-white bg-[#634600] hover:bg-[#56432C] rounded-lg transition-colors flex-shrink-0">
                            Unblock
                        </button>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
    function unblockUser(userId, userName) {
        if (!confirm(`Are you sure you want to unblock ${userName}? You will be able to receive messages from them again.`)) {
            return;
        }
        
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/users/${userId}/unblock`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Failed to unblock user');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(`${userName} has been unblocked`, 'info');
                // Refresh the blocked users list
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('/users/blocked', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            updateBlockedUsersModal(data.blocked_users);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error refreshing blocked users:', error);
                });
            } else {
                showNotification(data.message || 'Failed to unblock user', 'error');
            }
        })
        .catch(error => {
            console.error('Error unblocking user:', error);
            showNotification(error.message || 'Failed to unblock user. Please try again.', 'error');
        });
    }

    function blockUser(userId) {
        // Close dropdown
        const dropdownMenu = document.getElementById('settings-dropdown-menu');
        if (dropdownMenu) {
            dropdownMenu.classList.add('hidden');
        }
        
        if (confirm('Are you sure you want to block this user? You will no longer receive messages from them.')) {
            // TODO: Implement block user functionality
            // This would typically call an API endpoint
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/users/${userId}/block`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (response.status === 404) {
                    throw new Error('Block user feature is not yet implemented');
                }
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Failed to block user');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification('User blocked successfully', 'info');
                    setTimeout(() => {
                        window.location.href = '{{ route("messages.index") }}';
                    }, 1500);
                } else {
                    showNotification(data.message || 'Failed to block user', 'error');
                }
            })
            .catch(error => {
                console.error('Error blocking user:', error);
                showNotification(error.message || 'Failed to block user. Please try again.', 'error');
            });
        }
    }

    function deleteConversation(userId) {
        // Close dropdown
        const dropdownMenu = document.getElementById('settings-dropdown-menu');
        if (dropdownMenu) {
            dropdownMenu.classList.add('hidden');
        }
        
        if (confirm('Are you sure you want to delete this conversation? This action cannot be undone.')) {
            // TODO: Implement delete conversation functionality
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/messages/${userId}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (response.status === 404) {
                    throw new Error('Delete conversation feature is not yet implemented');
                }
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Failed to delete conversation');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification('Conversation deleted successfully', 'info');
                    setTimeout(() => {
                        window.location.href = '{{ route("messages.index") }}';
                    }, 1500);
                } else {
                    showNotification(data.message || 'Failed to delete conversation', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting conversation:', error);
                showNotification(error.message || 'Failed to delete conversation. Please try again.', 'error');
            });
        }
    }

    // Audio Call Functionality
    function startAudioCall() {
        // Check if WebRTC is supported
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showNotification('Audio calls are not supported in your browser. Please use a modern browser.', 'error');
            return;
        }
        
        const recipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
        const recipientName = document.querySelector('meta[name="recipient-name"]')?.getAttribute('content');
        
        if (!recipientId) {
            showNotification('Unable to start audio call. Please refresh the page.', 'error');
            return;
        }
        
        // Create audio call modal
        const modal = document.createElement('div');
        modal.id = 'audio-call-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[200]';
        modal.innerHTML = `
            <div class="w-full h-full flex flex-col relative">
                <!-- Audio Call Header -->
                <div class="absolute top-0 left-0 right-0 bg-gradient-to-r from-[#634600] to-[#B59F84] px-4 py-3 flex items-center justify-between z-10">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">${recipientName}</span>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">{{ $recipient->fname }} {{ $recipient->lname }}</h3>
                            <p class="text-white text-xs opacity-80" id="audio-call-status">Calling...</p>
                        </div>
                    </div>
                    <button onclick="endAudioCall()" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Audio Call Display -->
                <div class="flex-1 flex flex-col items-center justify-center text-white">
                    <div class="w-32 h-32 bg-gradient-to-r from-[#634600] to-[#B59F84] rounded-full flex items-center justify-center mb-6 animate-pulse">
                        <span class="text-white text-3xl font-semibold">${recipientName}</span>
                    </div>
                    <h3 class="text-2xl font-semibold mb-2">{{ $recipient->fname }} {{ $recipient->lname }}</h3>
                    <p class="text-lg opacity-80" id="audio-call-time">00:00</p>
                </div>
                
                <!-- Hidden audio element for remote audio stream -->
                <audio id="remote-audio" autoplay playsinline></audio>
                
                <!-- Call Controls -->
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 px-4 py-6 flex items-center justify-center space-x-4 z-10">
                    <button onclick="toggleCallAudio()" id="mute-call-btn" class="p-4 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white transition-colors" title="Mute">
                        <svg id="mute-icon-on" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                        <svg id="mute-icon-off" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" clip-rule="evenodd"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path>
                        </svg>
                    </button>
                    <button onclick="endAudioCall()" id="end-audio-call-btn" class="p-4 bg-red-600 hover:bg-red-700 rounded-full text-white transition-colors transform hover:scale-110" title="End Call">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <button onclick="toggleSpeaker()" id="speaker-btn" class="p-4 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white transition-colors" title="Speaker">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 14.142M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        
        // Initialize audio call
        initializeAudioCall(recipientId);
    }
    
    function initializeAudioCall(recipientId) {
        // Get current user ID from meta tag
        const currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content');
        
        // Request user media (microphone only)
        navigator.mediaDevices.getUserMedia({ 
            video: false, 
            audio: true 
        })
        .then(stream => {
            // Store stream globally for controls
            window.audioCallStream = stream;
            
            // Start call timer
            startCallTimer();
            
            // Initialize audio call connection using WebRTC
            initializeAudioCallConnection(recipientId, currentUserId);
            
            // Send notification to recipient
            sendAudioCallNotification(recipientId);
        })
        .catch(error => {
            console.error('Error accessing microphone:', error);
            showNotification('Unable to access microphone. Please check permissions.', 'error');
            endAudioCall();
        });
    }
    
    function initializeAudioCallConnection(recipientId, currentUserId) {
        // WebRTC Configuration
        const configuration = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' }
            ]
        };
        
        // Create peer connection
        window.audioPeerConnection = new RTCPeerConnection(configuration);
        
        // Add audio tracks to peer connection
        window.audioCallStream.getTracks().forEach(track => {
            window.audioPeerConnection.addTrack(track, window.audioCallStream);
        });
        
        // Handle remote audio stream
        window.audioPeerConnection.ontrack = (event) => {
            // Remote audio stream received
            if (event.streams[0]) {
                const statusElement = document.getElementById('audio-call-status');
                if (statusElement) {
                    statusElement.textContent = 'Connected';
                }
                
                // Attach remote audio stream to audio element
                const remoteAudio = document.getElementById('remote-audio');
                if (remoteAudio) {
                    remoteAudio.srcObject = event.streams[0];
                    remoteAudio.play().catch(error => {
                        console.error('Error playing remote audio:', error);
                    });
                    console.log('Remote audio stream attached and playing');
                }
            }
        };
        
        // Handle ICE candidates
        window.audioPeerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                // Send ICE candidate via Pusher
                if (window.Echo) {
                    window.Echo.private(`chat.user.${recipientId}`)
                        .whisper('ice-candidate-audio', {
                            candidate: event.candidate,
                            from: currentUserId
                        });
                }
            }
        };
        
        // Create offer
        window.audioPeerConnection.createOffer()
            .then(offer => {
                return window.audioPeerConnection.setLocalDescription(offer);
            })
            .then(() => {
                // Send offer via Pusher
                if (window.Echo) {
                    window.Echo.private(`chat.user.${recipientId}`)
                        .whisper('audio-call-offer', {
                            offer: window.audioPeerConnection.localDescription,
                            from: currentUserId,
                            type: 'audio'
                        });
                }
            })
            .catch(error => {
                console.error('Error creating audio offer:', error);
                showNotification('Failed to initiate audio call', 'error');
                endAudioCall();
            });
        
        // Listen for answer
        if (window.Echo) {
            window.Echo.private(`chat.user.${currentUserId}`)
                .listenForWhisper('audio-call-answer', (data) => {
                    if (data.from === recipientId) {
                        window.audioPeerConnection.setRemoteDescription(new RTCSessionDescription(data.answer))
                            .catch(error => console.error('Error setting remote description:', error));
                    }
                });
            
            // Listen for ICE candidates from remote
            window.Echo.private(`chat.user.${currentUserId}`)
                .listenForWhisper('ice-candidate-audio', (data) => {
                    if (data.from === recipientId && data.candidate) {
                        window.audioPeerConnection.addIceCandidate(new RTCIceCandidate(data.candidate))
                            .catch(error => console.error('Error adding ICE candidate:', error));
                    }
                });
        }
    }
    
    function sendAudioCallNotification(recipientId) {
        const currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        console.log('Sending audio call invitation to:', recipientId);
        
        // Send via API (which will broadcast via Laravel Event - more reliable)
        fetch('/api/call/invite', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                recipient_id: recipientId,
                call_type: 'audio',
                action: 'invite'
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to send call invitation');
            }
            return response.json();
        })
        .then(data => {
            console.log('Audio call invitation sent successfully:', data);
        })
        .catch(error => {
            console.error('Error sending audio call invitation:', error);
            showNotification('Failed to send call invitation. Please try again.', 'error');
            // Fallback to whisper if API fails
            if (window.Echo) {
                const callerName = '{{ auth()->user()->fname }} {{ auth()->user()->lname }}';
                window.Echo.private(`chat.user.${recipientId}`)
                    .whisper('audio-call-invitation', {
                        from: currentUserId,
                        fromName: callerName,
                        type: 'audio',
                        action: 'invite'
                    });
            }
        });
    }
    
    let callTimerInterval = null;
    let callStartTime = null;
    
    function startCallTimer() {
        callStartTime = Date.now();
        callTimerInterval = setInterval(() => {
            const elapsed = Date.now() - callStartTime;
            const minutes = Math.floor(elapsed / 60000);
            const seconds = Math.floor((elapsed % 60000) / 1000);
            const timeDisplay = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            const timeElement = document.getElementById('audio-call-time');
            if (timeElement) {
                timeElement.textContent = timeDisplay;
            }
        }, 1000);
    }
    
    function toggleCallAudio() {
        if (window.audioCallStream) {
            const audioTracks = window.audioCallStream.getAudioTracks();
            audioTracks.forEach(track => {
                track.enabled = !track.enabled;
            });
            
            const muteIconOn = document.getElementById('mute-icon-on');
            const muteIconOff = document.getElementById('mute-icon-off');
            
            if (audioTracks[0] && audioTracks[0].enabled) {
                muteIconOn.classList.remove('hidden');
                muteIconOff.classList.add('hidden');
            } else {
                muteIconOn.classList.add('hidden');
                muteIconOff.classList.remove('hidden');
            }
        }
    }
    
    function toggleSpeaker() {
        // TODO: Implement speaker toggle functionality
        // This would typically involve routing audio to different output devices
        const speakerBtn = document.getElementById('speaker-btn');
        if (speakerBtn) {
            speakerBtn.classList.toggle('bg-[#634600]');
        }
    }
    
    function endAudioCall() {
        // Stop timer
        if (callTimerInterval) {
            clearInterval(callTimerInterval);
            callTimerInterval = null;
        }
        callStartTime = null;
        
        const recipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
        const currentUserId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        
        // Close peer connection
        if (window.audioPeerConnection) {
            window.audioPeerConnection.close();
            window.audioPeerConnection = null;
        }
        
        // Stop all tracks
        if (window.audioCallStream) {
            window.audioCallStream.getTracks().forEach(track => track.stop());
            window.audioCallStream = null;
        }
        
        // Remove modal
        const modal = document.getElementById('audio-call-modal');
        if (modal) {
            modal.remove();
        }
        
        document.body.style.overflow = '';
        
        // Send end call notification via Pusher
        if (window.Echo && recipientId) {
            window.Echo.private(`chat.user.${recipientId}`)
                .whisper('audio-call-ended', {
                    from: currentUserId,
                    type: 'audio',
                    action: 'end'
                });
        }
        
        // Send end call notification via API
        if (recipientId) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch(`/messages/${recipientId}/audio-call`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    type: 'audio_call_invitation',
                    action: 'end'
                })
            }).catch(error => {
                console.warn('Audio call API endpoint not available:', error);
                // Continue without API notification
            });
        }
    }

    // Video Call Functionality
    function startVideoCall() {
        // Check if WebRTC is supported
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showNotification('Video calls are not supported in your browser. Please use a modern browser.', 'error');
            return;
        }
        
        const recipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
        const recipientName = document.querySelector('meta[name="recipient-name"]')?.getAttribute('content');
        const currentUser = '{{ auth()->user()->fname }} {{ auth()->user()->lname }}';
        
        if (!recipientId) {
            showNotification('Unable to start video call. Please refresh the page.', 'error');
            return;
        }
        
        // Create video call modal
        const modal = document.createElement('div');
        modal.id = 'video-call-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[200]';
        modal.innerHTML = `
            <div class="w-full h-full flex flex-col relative">
                <!-- Video Call Header -->
                <div class="absolute top-0 left-0 right-0 bg-gradient-to-r from-[#634600] to-[#B59F84] px-4 py-3 flex items-center justify-between z-10">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">${recipientName}</span>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">{{ $recipient->fname }} {{ $recipient->lname }}</h3>
                            <p class="text-white text-xs opacity-80" id="call-status">Calling...</p>
                        </div>
                    </div>
                    <button onclick="endVideoCall()" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Video Container -->
                <div class="flex-1 flex items-center justify-center relative p-4">
                    <!-- Remote Video (Recipient) -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div id="remote-video-container" class="w-full h-full flex items-center justify-center bg-gray-900 rounded-lg overflow-hidden relative">
                            <video id="remote-video" autoplay playsinline class="w-full h-full object-contain hidden"></video>
                            <!-- Waiting for recipient -->
                            <div id="waiting-indicator" class="text-center text-white">
                                <div class="w-24 h-24 bg-gradient-to-r from-[#634600] to-[#B59F84] rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                                    <span class="text-white text-2xl font-semibold">${recipientName}</span>
                                </div>
                                <p class="text-lg">Waiting for {{ $recipient->fname }} to join...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Local Video (Self) -->
                    <div class="absolute bottom-4 right-4 w-48 h-36 bg-gray-800 rounded-lg overflow-hidden shadow-xl z-20">
                        <video id="local-video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <!-- Video controls overlay -->
                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 p-2 flex items-center justify-center space-x-2">
                            <button id="toggle-video" onclick="toggleVideo()" class="p-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white transition-colors" title="Toggle Video">
                                <svg id="video-on-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <svg id="video-off-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                            </button>
                            <button id="toggle-audio" onclick="toggleAudio()" class="p-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white transition-colors" title="Toggle Audio">
                                <svg id="audio-on-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                </svg>
                                <svg id="audio-off-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" clip-rule="evenodd"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Call Controls -->
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 px-4 py-6 flex items-center justify-center space-x-4 z-10">
                    <button id="end-call-btn" onclick="endVideoCall()" class="p-4 bg-red-600 hover:bg-red-700 rounded-full text-white transition-colors transform hover:scale-110" title="End Call">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        
        // Initialize video call
        initializeVideoCall(recipientId);
    }
    
    function initializeVideoCall(recipientId) {
        const currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content');
        
        console.log('Initializing video call to:', recipientId);
        
        // Create peer connection with enhanced configuration
        window.peerConnection = new RTCPeerConnection(getRTCConfiguration());
        
        // Enhanced connection state monitoring
        window.peerConnection.onconnectionstatechange = () => {
            console.log('Connection state:', window.peerConnection.connectionState);
            updateCallStatus(window.peerConnection.connectionState);
            
            if (window.peerConnection.connectionState === 'connected') {
                console.log('WebRTC connection established successfully!');
                showNotification('Call connected!', 'info');
            } else if (window.peerConnection.connectionState === 'failed') {
                console.error('WebRTC connection failed');
                showNotification('Connection failed. Please try again.', 'error');
                // Auto-retry after 3 seconds
                setTimeout(() => {
                    if (window.peerConnection && window.peerConnection.connectionState === 'failed') {
                        console.log('Attempting to reconnect...');
                        restartIce(recipientId, currentUserId);
                    }
                }, 3000);
            }
        };
        
        window.peerConnection.oniceconnectionstatechange = () => {
            console.log('ICE connection state:', window.peerConnection.iceConnectionState);
            if (window.peerConnection.iceConnectionState === 'failed') {
                console.log('ICE connection failed, restarting ICE...');
                restartIce(recipientId, currentUserId);
            }
        };
        
        window.peerConnection.onsignalingstatechange = () => {
            console.log('Signaling state:', window.peerConnection.signalingState);
        };
        
        window.peerConnection.onicegatheringstatechange = () => {
            console.log('ICE gathering state:', window.peerConnection.iceGatheringState);
        };
        
        // Request user media (camera and microphone)
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 1280 },
                height: { ideal: 720 },
                frameRate: { ideal: 30 }
            }, 
            audio: {
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true
            }
        })
        .then(stream => {
            console.log('Local media stream obtained');
            
            const localVideo = document.getElementById('local-video');
            if (localVideo) {
                localVideo.srcObject = stream;
            }
            
            // Store stream globally for controls
            window.localStream = stream;
            
            // Add tracks to peer connection with error handling
            try {
                stream.getTracks().forEach(track => {
                    console.log('Adding track:', track.kind, track.id);
                    window.peerConnection.addTrack(track, stream);
                });
            } catch (error) {
                console.error('Error adding tracks:', error);
            }
            
            // Handle remote stream
            window.peerConnection.ontrack = (event) => {
                console.log('Remote track received:', event.track.kind);
                const remoteVideo = document.getElementById('remote-video');
                const waitingIndicator = document.getElementById('waiting-indicator');
                
                if (remoteVideo && event.streams[0]) {
                    remoteVideo.srcObject = event.streams[0];
                    remoteVideo.classList.remove('hidden');
                    if (waitingIndicator) {
                        waitingIndicator.classList.add('hidden');
                    }
                    
                    updateCallStatus('connected');
                    console.log('Remote video stream attached');
                }
            };
            
            // Handle ICE candidates with retry mechanism
            window.peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    console.log('New ICE candidate:', event.candidate);
                    // Send ICE candidate via API (primary) and Pusher (fallback)
                    sendIceCandidate(recipientId, event.candidate, currentUserId);
                } else {
                    console.log('All ICE candidates gathered');
                }
            };
            
            // Create and send offer with enhanced error handling
            createAndSendOffer(recipientId, currentUserId);
            
            // Set up answer listener (both API and Pusher)
            setupAnswerListener(recipientId, currentUserId);
            
            // Set up ICE candidate listener (both API and Pusher)
            setupICECandidateListener(recipientId);
            
            // Send notification to recipient
            sendVideoCallNotification(recipientId);
        })
        .catch(error => {
            console.error('Error accessing media devices:', error);
            let errorMessage = 'Unable to access camera or microphone. ';
            
            if (error.name === 'NotAllowedError') {
                errorMessage += 'Please check permissions and allow access to camera and microphone.';
            } else if (error.name === 'NotFoundError') {
                errorMessage += 'No camera or microphone found.';
            } else if (error.name === 'NotSupportedError') {
                errorMessage += 'Your browser does not support video calling.';
            } else {
                errorMessage += 'Please check your device settings.';
            }
            
            showNotification(errorMessage, 'error');
            endVideoCall();
        });
    }
    
    // Enhanced offer creation with retry
    function createAndSendOffer(recipientId, currentUserId, retryCount = 0) {
        const maxRetries = 3;
        
        console.log(`Creating offer (attempt ${retryCount + 1}/${maxRetries + 1})`);
        
        window.peerConnection.createOffer({
            offerToReceiveAudio: true,
            offerToReceiveVideo: true,
            voiceActivityDetection: false
        })
        .then(offer => {
            console.log('Offer created:', offer.type);
            return window.peerConnection.setLocalDescription(offer);
        })
        .then(() => {
            console.log('Local description set, sending offer to:', recipientId);
            // Send offer via API (primary) and Pusher (fallback)
            return sendOfferViaAPI(recipientId, window.peerConnection.localDescription, currentUserId);
        })
        .then(() => {
            console.log('Offer sent successfully via API');
            // Also send via Pusher as fallback
            sendOfferViaPusher(recipientId, window.peerConnection.localDescription, currentUserId);
            // Set up offer retry listener in case recipient didn't receive it
            setupOfferRetryListener(recipientId, currentUserId);
        })
        .catch(error => {
            console.error('Error creating/sending offer:', error);
            if (retryCount < maxRetries) {
                console.log(`Retrying offer creation (${retryCount + 1}/${maxRetries})`);
                setTimeout(() => {
                    createAndSendOffer(recipientId, currentUserId, retryCount + 1);
                }, 1000 * (retryCount + 1));
            } else {
                showNotification('Failed to initiate call. Please try again.', 'error');
                endVideoCall();
            }
        });
    }
    
    // Send offer via API (more reliable)
    async function sendOfferViaAPI(recipientId, offer, currentUserId) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            // Convert RTCSessionDescription to plain object for JSON serialization
            const offerData = {
                type: offer.type,
                sdp: offer.sdp
            };
            
            const response = await fetch('/api/webrtc/offer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    recipient_id: recipientId,
                    offer: offerData,  // Use serialized offer
                    caller_id: currentUserId,
                    type: 'video'
                })
            });
            
            if (response.ok) {
                console.log('✅ Offer sent via API');
                return await response.json();
            } else {
                const errorData = await response.json().catch(() => ({}));
                console.warn('⚠️ API offer sending failed:', response.status, errorData);
                throw new Error('API offer failed');
            }
        } catch (error) {
            console.warn('⚠️ API offer sending failed:', error);
            throw error;
        }
    }
    
    // Send offer via Pusher (fallback)
    function sendOfferViaPusher(recipientId, offer, currentUserId) {
        if (!window.Echo) {
            console.warn('⚠️ Echo not available for Pusher fallback');
            return;
        }
        
        try {
            window.Echo.private(`chat.user.${recipientId}`)
                .whisper('video-call-offer', {
                    offer: offer,
                    from: currentUserId,
                    type: 'video',
                    timestamp: Date.now()
                });
            console.log('✅ Offer sent via Pusher (fallback)');
        } catch (error) {
            console.error('❌ Pusher offer sending failed:', error);
        }
    }
    
    // Send ICE candidate (API primary, Pusher fallback)
    function sendIceCandidate(recipientId, candidate, currentUserId) {
        // Try API first
        sendIceCandidateViaAPI(recipientId, candidate, currentUserId);
        
        // Pusher fallback
        if (window.Echo) {
            try {
                window.Echo.private(`chat.user.${recipientId}`)
                    .whisper('ice-candidate', {
                        candidate: candidate,
                        from: currentUserId
                    });
            } catch (error) {
                console.warn('Pusher ICE candidate failed:', error);
            }
        }
    }
    
    async function sendIceCandidateViaAPI(recipientId, candidate, currentUserId) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            // Convert RTCIceCandidate to plain object for JSON serialization
            const candidateData = {
                candidate: candidate.candidate,
                sdpMid: candidate.sdpMid,
                sdpMLineIndex: candidate.sdpMLineIndex,
                usernameFragment: candidate.usernameFragment
            };
            
            await fetch('/api/webrtc/ice-candidate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({
                    recipient_id: recipientId,
                    candidate: candidateData,  // Use serialized candidate
                    caller_id: currentUserId
                })
            });
        } catch (error) {
            // Silent fail - ICE candidates are not critical, Pusher will handle it
        }
    }
    
    // Setup answer listener (both API and Pusher)
    function setupAnswerListener(recipientId, currentUserId) {
        if (!window.Echo) return;
        
        // Listen for answer via API (Laravel Event)
        window.Echo.private(`chat.user.${currentUserId}`)
            .listen('.webrtc.answer', (data) => {
                console.log('📥 Received WebRTC answer via API:', data);
                if (data.answer && data.callerId === recipientId && window.peerConnection) {
                    console.log('Setting remote description from API answer');
                    window.peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer))
                        .then(() => {
                            console.log('✅ Remote description set from API answer');
                        })
                        .catch(error => {
                            console.error('❌ Error setting remote description:', error);
                            // Try to recover by creating a new offer
                            setTimeout(() => {
                                createAndSendOffer(recipientId, currentUserId);
                            }, 1000);
                        });
                }
            });
        
        // Also listen for Pusher whisper (fallback)
        window.Echo.private(`chat.user.${currentUserId}`)
            .listenForWhisper('video-call-answer', (data) => {
                console.log('📥 Received answer via Pusher whisper:', data);
                if (data.from === recipientId && data.answer) {
                    console.log('Setting remote description from Pusher answer');
                    window.peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer))
                        .then(() => {
                            console.log('✅ Remote description set from Pusher answer');
                        })
                        .catch(error => {
                            console.error('❌ Error setting remote description:', error);
                        });
                }
            });
    }
    
    // Setup ICE candidate listener (both API and Pusher)
    function setupICECandidateListener(recipientId) {
        if (!window.Echo) return;
        
        const currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content');
        
        // Listen for ICE candidates via API (Laravel Event)
        window.Echo.private(`chat.user.${currentUserId}`)
            .listen('.webrtc.ice-candidate', (data) => {
                if (data.candidate && data.callerId === recipientId && window.peerConnection) {
                    console.log('📥 Received ICE candidate via API');
                    window.peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate))
                        .then(() => console.log('✅ ICE candidate added from API'))
                        .catch(error => console.error('❌ Error adding ICE candidate:', error));
                }
            });
        
        // Also listen for Pusher whisper (fallback)
        window.Echo.private(`chat.user.${currentUserId}`)
            .listenForWhisper('ice-candidate', (data) => {
                if (data.from === recipientId && data.candidate && window.peerConnection) {
                    console.log('📥 Received ICE candidate via Pusher');
                    window.peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate))
                        .then(() => console.log('✅ ICE candidate added from Pusher'))
                        .catch(error => console.error('❌ Error adding ICE candidate:', error));
                }
            });
    }
    
    // Setup offer retry listener
    function setupOfferRetryListener(recipientId, currentUserId) {
        if (!window.Echo) return;
        
        window.Echo.private(`chat.user.${document.querySelector('meta[name="user-id"]').getAttribute('content')}`)
            .listenForWhisper('request-offer', (data) => {
                if (data.from === recipientId && window.peerConnection && window.peerConnection.localDescription) {
                    console.log('Resending offer to recipient');
                    window.Echo.private(`chat.user.${recipientId}`)
                        .whisper('video-call-offer', {
                            offer: window.peerConnection.localDescription,
                            from: currentUserId,
                            type: 'video',
                            timestamp: Date.now()
                        });
                }
            });
    }
    
    function sendVideoCallNotification(recipientId) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content');
        
        console.log('Sending video call invitation to:', recipientId);
        
        // Send via API (which will broadcast via Laravel Event - more reliable)
        fetch('/api/call/invite', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                recipient_id: recipientId,
                call_type: 'video',
                action: 'invite'
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to send call invitation');
            }
            return response.json();
        })
        .then(data => {
            console.log('Video call invitation sent successfully:', data);
        })
        .catch(error => {
            console.error('Error sending video call invitation:', error);
            showNotification('Failed to send call invitation. Please try again.', 'error');
            // Fallback to whisper if API fails
            if (window.Echo) {
                const callerName = '{{ auth()->user()->fname }} {{ auth()->user()->lname }}';
                window.Echo.private(`chat.user.${recipientId}`)
                    .whisper('video-call-invitation', {
                        from: currentUserId,
                        fromName: callerName,
                        type: 'video',
                        action: 'invite'
                    });
            }
        });
    }
    
    function toggleVideo() {
        if (window.localStream) {
            const videoTracks = window.localStream.getVideoTracks();
            videoTracks.forEach(track => {
                track.enabled = !track.enabled;
            });
            
            const videoOnIcon = document.getElementById('video-on-icon');
            const videoOffIcon = document.getElementById('video-off-icon');
            const localVideo = document.getElementById('local-video');
            
            if (videoTracks[0] && videoTracks[0].enabled) {
                videoOnIcon.classList.remove('hidden');
                videoOffIcon.classList.add('hidden');
                if (localVideo) localVideo.style.opacity = '1';
            } else {
                videoOnIcon.classList.add('hidden');
                videoOffIcon.classList.remove('hidden');
                if (localVideo) localVideo.style.opacity = '0.5';
            }
        }
    }
    
    function toggleAudio() {
        if (window.localStream) {
            const audioTracks = window.localStream.getAudioTracks();
            audioTracks.forEach(track => {
                track.enabled = !track.enabled;
            });
            
            const audioOnIcon = document.getElementById('audio-on-icon');
            const audioOffIcon = document.getElementById('audio-off-icon');
            
            if (audioTracks[0] && audioTracks[0].enabled) {
                audioOnIcon.classList.remove('hidden');
                audioOffIcon.classList.add('hidden');
            } else {
                audioOnIcon.classList.add('hidden');
                audioOffIcon.classList.remove('hidden');
            }
        }
    }
    
    function endVideoCall() {
        console.log('Ending video call');
        
        const recipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
        const currentUserId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        
        // Close peer connection
        if (window.peerConnection) {
            window.peerConnection.close();
            window.peerConnection = null;
        }
        
        // Stop all tracks
        if (window.localStream) {
            window.localStream.getTracks().forEach(track => {
                track.stop();
                track.enabled = false;
            });
            window.localStream = null;
        }
        
        // Send end call notification
        if (window.Echo && recipientId) {
            window.Echo.private(`chat.user.${recipientId}`)
                .whisper('video-call-ended', {
                    from: currentUserId,
                    type: 'video',
                    action: 'end'
                });
        }
        
        // Remove modal
        const modal = document.getElementById('video-call-modal');
        if (modal) {
            modal.remove();
        }
        
        // Also remove incoming call modal if it exists
        const incomingModal = document.getElementById('incoming-video-call-modal');
        if (incomingModal) {
            incomingModal.remove();
        }
        
        document.body.style.overflow = '';
        
        console.log('Video call ended');
    }

    // ============================================
    // INCOMING CALL HANDLERS (Recipient Side)
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const currentUserId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        if (!currentUserId || !window.Echo) {
            console.log('Echo not available or user ID missing');
            return;
        }

        console.log('Setting up incoming call listeners for user:', currentUserId);

        // Listen for call invitations via Laravel Event (primary method)
        window.Echo.private(`chat.user.${currentUserId}`)
            .listen('.call-invitation', (data) => {
                console.log('Received call invitation (chat page handler):', data);
                if (data.action === 'invite' && data.caller_id) {
                    // Only handle if we're on the chat page with this user
                    const recipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
                    if (recipientId && parseInt(recipientId) === parseInt(data.caller_id)) {
                        const callerInfo = data.caller || {};
                        const callerName = callerInfo.name || `${callerInfo.fname || ''} ${callerInfo.lname || ''}`.trim() || '{{ $recipient->fname ?? "Someone" }}';
                        
                        if (data.call_type === 'video') {
                            handleIncomingVideoCall(data.caller_id, callerName);
                        } else if (data.call_type === 'audio') {
                            handleIncomingAudioCall(data.caller_id, callerName);
                        }
                    }
                } else if (data.action === 'end' || data.action === 'rejected') {
                    if (document.getElementById('incoming-audio-call-modal')) {
                        endIncomingAudioCall();
                    }
                    if (document.getElementById('incoming-video-call-modal')) {
                        endIncomingVideoCall();
                    }
                }
            });

        // Also listen for whispers as fallback (backward compatibility)
        window.Echo.private(`chat.user.${currentUserId}`)
            .listenForWhisper('audio-call-invitation', (data) => {
                console.log('Received audio call invitation (whisper fallback):', data);
                if (data.action === 'invite' && data.from) {
                    const recipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
                    if (recipientId && parseInt(recipientId) === parseInt(data.from)) {
                        const callerName = data.fromName || '{{ $recipient->fname ?? "Someone" }}';
                        handleIncomingAudioCall(data.from, callerName);
                    }
                }
            });

        window.Echo.private(`chat.user.${currentUserId}`)
            .listenForWhisper('video-call-invitation', (data) => {
                console.log('Received video call invitation (whisper fallback):', data);
                if (data.action === 'invite' && data.from) {
                    const recipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
                    if (recipientId && parseInt(recipientId) === parseInt(data.from)) {
                        const callerName = data.fromName || '{{ $recipient->fname ?? "Someone" }}';
                        handleIncomingVideoCall(data.from, callerName);
                    }
                }
            });

        // Listen for incoming audio call offers
        window.Echo.private(`chat.user.${currentUserId}`)
            .listenForWhisper('audio-call-offer', (data) => {
                console.log('Received audio call offer:', data);
                if (data.from && data.offer) {
                    handleAudioCallOffer(data.from, data.offer);
                }
            });

        // Listen for incoming video call offers
        window.Echo.private(`chat.user.${currentUserId}`)
            .listenForWhisper('video-call-offer', (data) => {
                console.log('Received video call offer:', data);
                if (data.from && data.offer) {
                    handleVideoCallOffer(data.from, data.offer);
                }
            });

        // Listen for ICE candidates (both audio and video)
        window.Echo.private(`chat.user.${currentUserId}`)
            .listenForWhisper('ice-candidate-audio', (data) => {
                if (data.from && data.candidate && window.audioPeerConnection) {
                    window.audioPeerConnection.addIceCandidate(new RTCIceCandidate(data.candidate))
                        .catch(error => console.error('Error adding ICE candidate:', error));
                }
            });

        window.Echo.private(`chat.user.${currentUserId}`)
            .listenForWhisper('ice-candidate', (data) => {
                if (data.from && data.candidate && window.peerConnection) {
                    window.peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate))
                        .catch(error => console.error('Error adding ICE candidate:', error));
                }
            });
    });

    // Handle incoming audio call
    function handleIncomingAudioCall(callerId, callerName = null) {
        // Prevent duplicate modals
        if (document.getElementById('incoming-audio-call-modal')) return;
        
        const displayName = callerName || '{{ $recipient->fname ?? "Someone" }}';
        
        const modal = document.createElement('div');
        modal.id = 'incoming-audio-call-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[200]';
        modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 text-center max-w-md w-full mx-4">
                <div class="w-24 h-24 bg-gradient-to-r from-[#634600] to-[#B59F84] rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                    <span class="text-white text-2xl font-semibold">${displayName.charAt(0).toUpperCase()}</span>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Incoming Audio Call</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-8">from ${displayName}</p>
                <div class="flex items-center justify-center gap-4">
                    <button onclick="rejectIncomingAudioCall()" class="p-4 bg-red-600 hover:bg-red-700 rounded-full text-white transition-colors" title="Decline">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <button onclick="acceptIncomingAudioCall(${callerId})" class="p-4 bg-green-600 hover:bg-green-700 rounded-full text-white transition-colors" title="Accept">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        
        window.incomingAudioCallTimeout = setTimeout(() => {
            rejectIncomingAudioCall();
        }, 30000);
    }

    // Handle incoming video call
    function handleIncomingVideoCall(callerId, callerName = null) {
        if (document.getElementById('incoming-video-call-modal')) return;
        
        const displayName = callerName || '{{ $recipient->fname ?? "Someone" }}';
        
        const modal = document.createElement('div');
        modal.id = 'incoming-video-call-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[200]';
        modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 text-center max-w-md w-full mx-4">
                <div class="w-24 h-24 bg-gradient-to-r from-[#634600] to-[#B59F84] rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                    <span class="text-white text-2xl font-semibold">${displayName.charAt(0).toUpperCase()}</span>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Incoming Video Call</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-8">from ${displayName}</p>
                <div class="flex items-center justify-center gap-4">
                    <button onclick="rejectIncomingVideoCall()" class="p-4 bg-red-600 hover:bg-red-700 rounded-full text-white transition-colors" title="Decline">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <button onclick="acceptIncomingVideoCall(${callerId})" class="p-4 bg-green-600 hover:bg-green-700 rounded-full text-white transition-colors" title="Accept">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        
        window.incomingVideoCallTimeout = setTimeout(() => {
            rejectIncomingVideoCall();
        }, 30000);
    }

    // Handle audio call offer (when recipient accepts)
    function handleAudioCallOffer(callerId, offer) {
        if (window.audioPeerConnection) {
            // Already handling a call
            return;
        }

        const configuration = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' }
            ]
        };
        window.audioPeerConnection = new RTCPeerConnection(configuration);
        
        navigator.mediaDevices.getUserMedia({ video: false, audio: true })
            .then(stream => {
                window.audioCallStream = stream;
                stream.getTracks().forEach(track => {
                    window.audioPeerConnection.addTrack(track, stream);
                });
                
                window.audioPeerConnection.ontrack = (event) => {
                    if (event.streams[0]) {
                        const statusElement = document.getElementById('audio-call-status');
                        if (statusElement) {
                            statusElement.textContent = 'Connected';
                        }
                        
                        // Attach remote audio stream to audio element
                        const remoteAudio = document.getElementById('remote-audio');
                        if (remoteAudio) {
                            remoteAudio.srcObject = event.streams[0];
                            remoteAudio.play().catch(error => {
                                console.error('Error playing remote audio:', error);
                            });
                            console.log('Remote audio stream attached and playing');
                        }
                    }
                };
                
                window.audioPeerConnection.onicecandidate = (event) => {
                    if (event.candidate && window.Echo) {
                        window.Echo.private(`chat.user.${callerId}`)
                            .whisper('ice-candidate-audio', {
                                candidate: event.candidate,
                                from: document.querySelector('meta[name="user-id"]').getAttribute('content')
                            });
                    }
                };
                
                return window.audioPeerConnection.setRemoteDescription(new RTCSessionDescription(offer));
            })
            .then(() => {
                return window.audioPeerConnection.createAnswer();
            })
            .then(answer => {
                return window.audioPeerConnection.setLocalDescription(answer);
            })
            .then(() => {
                if (window.Echo) {
                    window.Echo.private(`chat.user.${callerId}`)
                        .whisper('audio-call-answer', {
                            answer: window.audioPeerConnection.localDescription,
                            from: document.querySelector('meta[name="user-id"]').getAttribute('content')
                        });
                }
            })
            .catch(error => {
                console.error('Error handling audio offer:', error);
                showNotification('Error accepting call', 'error');
            });
    }

    // Send answer via API (more reliable)
    async function sendAnswerViaAPI(recipientId, answer, currentUserId) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            // Convert RTCSessionDescription to plain object for JSON serialization
            const answerData = {
                type: answer.type,
                sdp: answer.sdp
            };
            
            const response = await fetch('/api/webrtc/answer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    recipient_id: recipientId,
                    answer: answerData,  // Use serialized answer
                    caller_id: currentUserId
                })
            });
            
            if (response.ok) {
                console.log('✅ Answer sent via API');
                return await response.json();
            } else {
                const errorData = await response.json().catch(() => ({}));
                console.warn('⚠️ API answer sending failed:', response.status, errorData);
                throw new Error('API answer failed');
            }
        } catch (error) {
            console.warn('⚠️ API answer sending failed:', error);
            throw error;
        }
    }
    
    // Send answer via Pusher (fallback)
    function sendAnswerViaPusher(recipientId, answer, currentUserId) {
        if (!window.Echo) {
            console.warn('⚠️ Echo not available for Pusher fallback');
            return;
        }
        
        try {
            window.Echo.private(`chat.user.${recipientId}`)
                .whisper('video-call-answer', {
                    answer: answer,
                    from: currentUserId
                });
            console.log('✅ Answer sent via Pusher (fallback)');
        } catch (error) {
            console.error('❌ Pusher answer sending failed:', error);
        }
    }
    
    // Enhanced offer handling for receiver
    function handleVideoCallOffer(callerId, offer) {
        console.log('handleVideoCallOffer called, callerId:', callerId);
        
        if (!window.peerConnection) {
            console.error('No peer connection available');
            return;
        }
        
        console.log('Setting remote description from offer');
        window.peerConnection.setRemoteDescription(new RTCSessionDescription(offer))
            .then(() => {
                console.log('Remote description set successfully, creating answer');
                return window.peerConnection.createAnswer({
                    offerToReceiveAudio: true,
                    offerToReceiveVideo: true
                });
            })
            .then(answer => {
                console.log('Answer created:', answer.type);
                return window.peerConnection.setLocalDescription(answer);
            })
            .then(() => {
                console.log('Local description set, sending answer to caller');
                const currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content');
                // Send answer via API (primary) and Pusher (fallback)
                return sendAnswerViaAPI(callerId, window.peerConnection.localDescription, currentUserId);
            })
            .then(() => {
                // Also send via Pusher as fallback
                const currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content');
                sendAnswerViaPusher(callerId, window.peerConnection.localDescription, currentUserId);
            })
            .then(() => {
                console.log('Answer sent successfully');
            })
            .catch(error => {
                console.error('Error handling video offer:', error);
                showNotification('Error accepting call', 'error');
            });
    }

    // Accept incoming audio call
    function acceptIncomingAudioCall(callerId) {
        clearTimeout(window.incomingAudioCallTimeout);
        const modal = document.getElementById('incoming-audio-call-modal');
        if (modal) modal.remove();
        
        console.log('Accepting incoming audio call from:', callerId);
        
        // Create audio call UI for receiving (similar to startAudioCall but we're receiving)
        const recipientName = document.querySelector('meta[name="recipient-name"]')?.getAttribute('content') || 'Caller';
        const callModal = document.createElement('div');
        callModal.id = 'audio-call-modal';
        callModal.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[200]';
        callModal.innerHTML = `
            <div class="w-full h-full flex flex-col relative">
                <div class="absolute top-0 left-0 right-0 bg-gradient-to-r from-[#634600] to-[#B59F84] px-4 py-3 flex items-center justify-between z-10">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">${recipientName}</span>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">Incoming Call</h3>
                            <p class="text-white text-xs opacity-80" id="audio-call-status">Connecting...</p>
                        </div>
                    </div>
                    <button onclick="endAudioCall()" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 flex flex-col items-center justify-center text-white">
                    <div class="w-32 h-32 bg-gradient-to-r from-[#634600] to-[#B59F84] rounded-full flex items-center justify-center mb-6">
                        <span class="text-white text-3xl font-semibold">${recipientName}</span>
                    </div>
                    <h3 class="text-2xl font-semibold mb-2">Audio Call</h3>
                    <p class="text-lg opacity-80" id="audio-call-time">00:00</p>
                </div>
                
                <!-- Hidden audio element for remote audio stream -->
                <audio id="remote-audio" autoplay playsinline></audio>
                
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 px-4 py-6 flex items-center justify-center space-x-4 z-10">
                    <button onclick="toggleCallAudio()" id="mute-call-btn" class="p-4 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white transition-colors">
                        <svg id="mute-icon-on" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                        <svg id="mute-icon-off" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path>
                        </svg>
                    </button>
                    <button onclick="endAudioCall()" id="end-audio-call-btn" class="p-4 bg-red-600 hover:bg-red-700 rounded-full text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <button onclick="toggleSpeaker()" id="speaker-btn" class="p-4 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 14.142M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(callModal);
        document.body.style.overflow = 'hidden';
        
        // Initialize WebRTC connection immediately
        initializeAudioCallAsReceiver(callerId);
        startCallTimer();
        showNotification('Call accepted. Connecting...', 'info');
    }
    
    // Initialize audio call as receiver (when accepting incoming call)
    function initializeAudioCallAsReceiver(callerId) {
        const currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content');
        
        console.log('Initializing audio call as receiver, caller:', callerId);
        
        // WebRTC Configuration
        const configuration = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' }
            ]
        };
        
        // Create peer connection
        window.audioPeerConnection = new RTCPeerConnection(configuration);
        
        // Request user media (microphone only)
        navigator.mediaDevices.getUserMedia({ video: false, audio: true })
            .then(stream => {
                console.log('Got local audio stream');
                // Store stream globally for controls
                window.audioCallStream = stream;
                
                // Add tracks to peer connection
                stream.getTracks().forEach(track => {
                    window.audioPeerConnection.addTrack(track, stream);
                });
                
                // Handle remote audio stream
                window.audioPeerConnection.ontrack = (event) => {
                    console.log('Received remote audio stream');
                    if (event.streams[0]) {
                        const statusElement = document.getElementById('audio-call-status');
                        if (statusElement) {
                            statusElement.textContent = 'Connected';
                        }
                        
                        // Attach remote audio stream to audio element
                        const remoteAudio = document.getElementById('remote-audio');
                        if (remoteAudio) {
                            remoteAudio.srcObject = event.streams[0];
                            remoteAudio.play().catch(error => {
                                console.error('Error playing remote audio:', error);
                            });
                            console.log('Remote audio stream attached and playing');
                        }
                    }
                };
                
                // Handle ICE candidates
                window.audioPeerConnection.onicecandidate = (event) => {
                    if (event.candidate) {
                        console.log('Sending ICE candidate');
                        // Send ICE candidate via Pusher
                        if (window.Echo) {
                            window.Echo.private(`chat.user.${callerId}`)
                                .whisper('ice-candidate-audio', {
                                    candidate: event.candidate,
                                    from: currentUserId
                                });
                        }
                    }
                };
                
                // Listen for offer from caller
                if (window.Echo) {
                    window.Echo.private(`chat.user.${currentUserId}`)
                        .listenForWhisper('audio-call-offer', (data) => {
                            console.log('Received audio call offer:', data);
                            if (data.from === callerId && data.offer) {
                                handleAudioCallOffer(callerId, data.offer);
                            }
                        });
                    
                    // Listen for ICE candidates from caller
                    window.Echo.private(`chat.user.${currentUserId}`)
                        .listenForWhisper('ice-candidate-audio', (data) => {
                            if (data.from === callerId && data.candidate && window.audioPeerConnection) {
                                console.log('Received ICE candidate from caller');
                                window.audioPeerConnection.addIceCandidate(new RTCIceCandidate(data.candidate))
                                    .catch(error => console.error('Error adding ICE candidate:', error));
                            }
                        });
                }
            })
            .catch(error => {
                console.error('Error accessing microphone:', error);
                showNotification('Unable to access microphone. Please check permissions.', 'error');
                endAudioCall();
            });
    }

    // Reject incoming audio call
    function rejectIncomingAudioCall() {
        clearTimeout(window.incomingAudioCallTimeout);
        const modal = document.getElementById('incoming-audio-call-modal');
        if (modal) modal.remove();
        document.body.style.overflow = '';
        
        const currentUserId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        const recipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
        
        if (window.Echo && recipientId) {
            window.Echo.private(`chat.user.${recipientId}`)
                .whisper('audio-call-rejected', {
                    from: currentUserId
                });
        }
    }

    // Accept incoming video call
    function acceptIncomingVideoCall(callerId) {
        clearTimeout(window.incomingVideoCallTimeout);
        const modal = document.getElementById('incoming-video-call-modal');
        if (modal) modal.remove();
        
        console.log('Accepting incoming video call from:', callerId);
        
        // Create video call UI for receiving
        const recipientName = document.querySelector('meta[name="recipient-name"]')?.getAttribute('content') || 'Caller';
        const callModal = document.createElement('div');
        callModal.id = 'video-call-modal';
        callModal.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[200]';
        callModal.innerHTML = `
            <div class="w-full h-full flex flex-col relative">
                <div class="absolute top-0 left-0 right-0 bg-gradient-to-r from-[#634600] to-[#B59F84] px-4 py-3 flex items-center justify-between z-10">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">${recipientName}</span>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">Incoming Call</h3>
                            <p class="text-white text-xs opacity-80" id="call-status">Connecting...</p>
                        </div>
                    </div>
                    <button onclick="endVideoCall()" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 flex items-center justify-center relative p-4">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div id="remote-video-container" class="w-full h-full flex items-center justify-center bg-gray-900 rounded-lg overflow-hidden relative">
                            <video id="remote-video" autoplay playsinline class="w-full h-full object-contain hidden"></video>
                            <div id="waiting-indicator" class="text-center text-white">
                                <div class="w-24 h-24 bg-gradient-to-r from-[#634600] to-[#B59F84] rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-white text-2xl font-semibold">${recipientName}</span>
                                </div>
                                <p class="text-lg">Connecting...</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute bottom-4 right-4 w-48 h-36 bg-gray-800 rounded-lg overflow-hidden shadow-xl z-20">
                        <video id="local-video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 p-2 flex items-center justify-center space-x-2">
                            <button id="toggle-video" onclick="toggleVideo()" class="p-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white">
                                <svg id="video-on-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <svg id="video-off-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                            </button>
                            <button id="toggle-audio" onclick="toggleAudio()" class="p-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white">
                                <svg id="audio-on-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                </svg>
                                <svg id="audio-off-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 px-4 py-6 flex items-center justify-center space-x-4 z-10">
                    <button id="end-call-btn" onclick="endVideoCall()" class="p-4 bg-red-600 hover:bg-red-700 rounded-full text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(callModal);
        document.body.style.overflow = 'hidden';
        
        // Initialize WebRTC connection immediately (don't wait for offer)
        initializeVideoCallAsReceiver(callerId);
        showNotification('Call accepted. Connecting...', 'info');
    }
    
    // Initialize video call as receiver (when accepting incoming call)
    function initializeVideoCallAsReceiver(callerId) {
        const currentUserId = document.querySelector('meta[name="user-id"]').getAttribute('content');
        
        console.log('Initializing video call as receiver, caller:', callerId);
        
        // Create peer connection with enhanced configuration
        window.peerConnection = new RTCPeerConnection(getRTCConfiguration());
        
        // Enhanced connection state monitoring
        window.peerConnection.onconnectionstatechange = () => {
            console.log('Connection state:', window.peerConnection.connectionState);
            updateCallStatus(window.peerConnection.connectionState);
        };
        
        window.peerConnection.oniceconnectionstatechange = () => {
            console.log('ICE connection state:', window.peerConnection.iceConnectionState);
        };
        
        // Request user media (camera and microphone)
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }, 
            audio: true 
        })
        .then(stream => {
            console.log('Got local media stream as receiver');
            const localVideo = document.getElementById('local-video');
            if (localVideo) {
                localVideo.srcObject = stream;
            }
            
            // Store stream globally for controls
            window.localStream = stream;
            
            // Add tracks to peer connection
            stream.getTracks().forEach(track => {
                window.peerConnection.addTrack(track, stream);
            });
            
            // Handle remote stream
            window.peerConnection.ontrack = (event) => {
                console.log('Remote track received as receiver:', event.track.kind);
                const remoteVideo = document.getElementById('remote-video');
                const waitingIndicator = document.getElementById('waiting-indicator');
                
                if (remoteVideo && event.streams[0]) {
                    remoteVideo.srcObject = event.streams[0];
                    remoteVideo.classList.remove('hidden');
                    if (waitingIndicator) {
                        waitingIndicator.classList.add('hidden');
                    }
                    
                    updateCallStatus('connected');
                    console.log('Remote video stream attached as receiver');
                }
            };
            
            // Handle ICE candidates
            window.peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    console.log('New ICE candidate from receiver');
                    // Send via API (primary) and Pusher (fallback)
                    sendIceCandidate(callerId, event.candidate, currentUserId);
                }
            };
            
            // Set up offer listener (both API and Pusher)
            setupOfferListener(callerId, currentUserId);
            
            // Request offer if not received within 2 seconds
            setTimeout(() => {
                if (window.peerConnection && window.peerConnection.signalingState === 'stable') {
                    console.log('No offer received, requesting from caller');
                    if (window.Echo) {
                        window.Echo.private(`chat.user.${callerId}`)
                            .whisper('request-offer', {
                                from: currentUserId,
                                type: 'video'
                            });
                    }
                }
            }, 2000);
            
        })
        .catch(error => {
            console.error('Error accessing media devices as receiver:', error);
            showNotification('Unable to access camera or microphone. Please check permissions.', 'error');
            endVideoCall();
        });
    }
    
    // Setup offer listener for receiver (both API and Pusher)
    function setupOfferListener(callerId, currentUserId) {
        if (!window.Echo) return;
        
        // Listen for offer via API (Laravel Event) - primary method
        window.Echo.private(`chat.user.${currentUserId}`)
            .listen('.webrtc.offer', (data) => {
                console.log('📥 Received WebRTC offer via API:', data);
                if (data.offer && data.callerId === callerId) {
                    console.log('Processing offer from caller via API');
                    handleVideoCallOffer(callerId, data.offer);
                }
            });
        
        // Also listen for Pusher whisper (fallback)
        window.Echo.private(`chat.user.${currentUserId}`)
            .listenForWhisper('video-call-offer', (data) => {
                console.log('📥 Received offer via Pusher whisper:', data);
                if (data.from === callerId && data.offer) {
                    console.log('Processing offer from caller via Pusher');
                    handleVideoCallOffer(callerId, data.offer);
                }
            });
    }

    // Reject incoming video call
    function rejectIncomingVideoCall() {
        clearTimeout(window.incomingVideoCallTimeout);
        const modal = document.getElementById('incoming-video-call-modal');
        if (modal) modal.remove();
        document.body.style.overflow = '';
        
        const currentUserId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        const recipientId = document.querySelector('meta[name="recipient-id"]')?.getAttribute('content');
        
        if (window.Echo && recipientId) {
            window.Echo.private(`chat.user.${recipientId}`)
                .whisper('video-call-rejected', {
                    from: currentUserId
                });
        }
    }

    function endIncomingAudioCall() {
        const modal = document.getElementById('incoming-audio-call-modal');
        if (modal) modal.remove();
        document.body.style.overflow = '';
        clearTimeout(window.incomingAudioCallTimeout);
    }

    function endIncomingVideoCall() {
        const modal = document.getElementById('incoming-video-call-modal');
        if (modal) modal.remove();
        document.body.style.overflow = '';
        clearTimeout(window.incomingVideoCallTimeout);
    }
    </script>

    <style>
        /* Search highlight styles */
.conversation-name span.bg-yellow-200,
.conversation-message span.bg-yellow-200 {
    background-color: #fef3c7 !important;
    color: #634600 !important;
    font-weight: 600;
    padding: 0 2px;
    border-radius: 3px;
}

/* Smooth transitions for search */
.conversation-item {
    transition: all 0.3s ease;
}

/* Search input focus styles */
#desktop-search-input:focus,
#mobile-search-input:focus {
    border-color: #634600;
    box-shadow: 0 0 0 2px rgba(99, 70, 0, 0.1);
}

/* Clear button hover effects */
#clear-desktop-search:hover,
#clear-mobile-search:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}
        /* Ensure proper spacing for messages */
        .message-item {
            margin-bottom: 1rem;
        }
        
        /* Make sure messages container has proper height */
        #private-messages-container {
            min-height: 300px;
            max-height: calc(100vh - 240px);
            padding-bottom: 1rem;
        }
        
        /* Compact message input */
        #message-input {
            min-height: 40px;
            font-size: 14px;
        }
        
        /* Ensure timestamps are visible */
        .text-xs.text-gray-500 {
            color: #6b7280 !important;
            font-size: 11px;
        }
        
        /* Mobile optimizations */
        @media (max-width: 1023px) {
            #private-messages-container {
                max-height: calc(100vh - 180px);
                padding: 1rem;
                padding-bottom: 80px; /* Add space for input area */
            }
            
            .message-item {
                margin-bottom: 0.75rem;
            }
            
            /* Ensure input area is always visible on mobile */
            .bg-white.border-t {
                position: sticky;
                bottom: 0;
                z-index: 20;
                background: white;
                border-top: 1px solid #e5e7eb;
            }
        }
        
        /* Desktop optimizations */
        @media (min-width: 1024px) {
            #private-messages-container {
                max-height: calc(100vh - 240px);
            }
        }
        
        /* Unread message styling */
        .unread-message {
            animation: highlightPulse 2s ease-in-out;
        }
        
        @keyframes highlightPulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.95;
            }
        }
        
        .unread-message > div > div:last-child > div:first-child {
            position: relative;
        }
        
        .unread-message > div > div:last-child > div:first-child::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background-color: #3b82f6;
            border-radius: 2px;
        }
        
        /* Alternative simpler selector */
        .unread-message [class*="rounded-2xl"] {
            position: relative;
        }
        
        .unread-message [class*="rounded-2xl"]::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background-color: #3b82f6;
            border-radius: 2px;
        }
    </style>
</x-app-layout>