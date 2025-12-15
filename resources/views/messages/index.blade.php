<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Messages
            </h2>
            <a href="{{ route('dashboard') }}"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="recipient-id" content="">
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden h-[600px] flex flex-col md:flex-row">
                <!-- Conversations Sidebar -->
                <div
                    class="w-full md:w-80 bg-[#F4F2ED] dark:bg-gray-900 border-b md:border-b-0 md:border-r border-[#B59F84] dark:border-gray-700 flex flex-col">
                    <!-- Sidebar Header -->
                    <div class="p-4 border-b border-[#B59F84] dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-[#634600] dark:text-white">Messages</h2>
                        <div class="flex items-center gap-2">
                            <!-- Settings Dropdown -->
                            <div id="messages-settings-dropdown" class="relative">
                                <button id="messages-settings-toggle-btn" 
                                        class="p-2 text-[#786126] dark:text-white hover:text-[#634600] dark:hover:text-yellow-400 hover:bg-[#B59F84] hover:bg-opacity-20 rounded-full transition-colors relative">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                                <!-- Dropdown Menu -->
                                <div id="messages-settings-dropdown-menu"
                                     class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[100] py-1">
                                    <!-- Blocked Users -->
                                    <button type="button" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center space-x-2"
                                            onclick="showBlockedUsers()">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <span>Blocked Users</span>
                                    </button>
                                </div>
                            </div>
                            <button
                                class="md:hidden p-2 text-[#786126] dark:text-white hover:text-[#634600] hover:bg-[#B59F84] hover:bg-opacity-20 rounded-full transition-colors"
                                onclick="document.getElementById('sidebar').classList.toggle('hidden')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Search - UPDATED WITH FUNCTIONALITY -->
                    <div class="p-4">
                        <div class="relative">
                            <input type="text" 
                                   id="conversations-search-input"
                                   placeholder="Search conversations..."
                                   class="w-full pl-10 pr-10 py-2 border border-[#B59F84] dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#634600] focus:border-transparent bg-white dark:bg-gray-800 text-sm dark:text-white placeholder-gray-500 dark:placeholder-text-white">
                            <svg class="absolute left-3 top-2.5 w-4 h-4 text-[#786126] dark:text-white"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <!-- Clear search button -->
                            <button id="clear-search-btn" 
                                    class="absolute right-3 top-2.5 hidden text-[#786126] dark:text-white hover:text-[#634600] dark:hover:text-yellow-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Conversations List - UPDATED WITH SEARCH ATTRIBUTES -->
                    <div class="flex-1 overflow-y-auto" id="sidebar">
                        @if ($conversations->count() > 0)
                            @foreach ($conversations as $conversation)
                                <a href="{{ route('private.chat', $conversation['user']->id) }}"
                                    class="flex items-center p-4 hover:bg-[#B59F84] hover:bg-opacity-20 dark:hover:bg-yellow-800 transition-colors conversation-item"
                                    data-user-id="{{ $conversation['user']->id }}"
                                    data-user-name="{{ strtolower($conversation['user']->fname . ' ' . $conversation['user']->lname) }}"
                                    data-latest-message="{{ strtolower($conversation['latest_message']->message ?? '') }}"
                                    x-data="{
                                        unreadCount: {{ (int)($conversation['unread_count'] ?? 0) }},
                                        conversationUserId: {{ $conversation['user']->id }},
                                        init() {
                                            // Listen for conversation read events
                                            window.addEventListener('conversation-read', (e) => {
                                                if (e.detail?.user_id === this.conversationUserId) {
                                                    this.unreadCount = 0;
                                                    this.updateBadgeDisplay();
                                                }
                                            });
                                            
                                            // Listen for new messages
                                            @if(Auth::check())
                                            if (typeof Echo !== 'undefined') {
                                                Echo.private('chat.user.{{ Auth::id() }}')
                                                    .listen('.private-message', (e) => {
                                                        if (e.message && e.message.user_id === this.conversationUserId) {
                                                            const currentRecipientId = document.querySelector('meta[name=\"recipient-id\"]')?.getAttribute('content');
                                                            if (currentRecipientId != this.conversationUserId) {
                                                                this.unreadCount++;
                                                                this.updateBadgeDisplay();
                                                                window.dispatchEvent(new CustomEvent('new-message-received', {
                                                                    detail: { user_id: this.conversationUserId }
                                                                }));
                                                            }
                                                        }
                                                    });
                                            }
                                            @endif
                                            
                                            // Initialize badge display
                                            this.updateBadgeDisplay();
                                        },
                                        updateBadgeDisplay() { updateGlobalUnreadCount(); }
                                    }">
                                    <!-- Avatar -->
                                    <div class="relative">
                                        <img src="{{ $conversation['user']->profileImageUrl() }}" 
                                            alt="{{ $conversation['user']->fname }} {{ $conversation['user']->lname }}"
                                            class="w-12 h-12 rounded-full object-cover">
                                        <div x-cloak 
                                            x-show="unreadCount > 0"
                                            class="absolute -top-1.5 -right-1.5 min-w-[1.75rem] h-7 px-1 bg-[#FF4D4F] text-white text-xs font-bold rounded-full flex items-center justify-center shadow-md ring-2 ring-white transition-all duration-300"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 scale-75"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-75">
                                            <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                                        </div>
                                    </div>

                                    <!-- Conversation Info -->
                                    <div class="ml-3 flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-[#634600] dark:text-white truncate conversation-name">
                                                {{ $conversation['user']->fname }} {{ $conversation['user']->lname }}
                                            </p>
                                            <p class="text-xs text-[#786126] dark:text-white conversation-time">
                                                {{ $conversation['latest_message']->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <!-- Latest Message - Bold when unread -->
                                        <p class="text-sm truncate mt-1 text-[#786126] dark:text-white conversation-message"
                                            x-bind:class="unreadCount > 0 ? 'font-extrabold text-[#3d2f1f] dark:text-yellow-200' : ''">
                                            {{ $conversation['latest_message']->message ?: '[Image]' }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                            
                            <!-- No search results message -->
                            <div id="no-search-results" class="hidden flex items-center justify-center h-full p-4">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-[#B59F84] bg-opacity-30 dark:bg-[#f5d68b] rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-[#786126] dark:text-[#634600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-[#634600] dark:text-white text-sm">No conversations found</p>
                                    <p class="text-[#786126] dark:text-white text-xs mt-1">Try different search terms</p>
                                </div>
                            </div>
                            
                        @else
                            <div class="flex items-center justify-center h-full p-4" id="empty-state">
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 bg-[#B59F84] bg-opacity-30 dark:bg-[#f5d68b] rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-[#786126] dark:text-[#634600]" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.325 0-2.58-.26-3.68-.725L3 20l1.32-3.96C3.474 15.003 3 13.55 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-[#634600] dark:text-white text-sm">No conversations yet</p>
                                    <p class="text-[#786126] dark:text-white text-xs">Start chatting with someone!
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Welcome / Chat Area -->
                <div class="flex-1 flex items-center justify-center bg-[#F4F2ED] dark:bg-gray-800 p-4">
                    <div class="text-center">
                        <div
                            class="w-24 h-24 bg-gradient-to-r from-[#634600] to-[#B59F84] rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.325 0-2.58-.26-3.68-.725L3 20l1.32-3.96C3.474 15.003 3 13.55 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#634600] dark:text-white mb-2">Welcome to Messages
                        </h3>
                        <p class="text-[#786126] dark:text-white mb-6">Select a conversation from the sidebar to
                            start chatting</p>
                        {{-- <div class="flex flex-col sm:flex-row justify-center gap-3">
                            <a href="{{ route('dashboard') }}"
                                class="px-4 py-2 bg-[#634600] dark:bg-yellow-600 text-white rounded-lg hover:bg-[#56432C] dark:hover:bg-yellow-500 transition-colors">
                                Browse Items
                            </a>
                            <a href="{{ route('products.index') }}"
                                class="px-4 py-2 border border-[#B59F84] dark:border-yellow-400 text-[#634600] dark:text-white rounded-lg hover:bg-[#B59F84] hover:bg-opacity-20 dark:hover:bg-yellow-700 transition-colors">
                                My Items
                            </a>
                        </div> --}}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Search functionality for conversations
        function initConversationSearch() {
            const searchInput = document.getElementById('conversations-search-input');
            const clearSearchBtn = document.getElementById('clear-search-btn');
            const sidebar = document.getElementById('sidebar');
            const conversationItems = document.querySelectorAll('.conversation-item');
            const noSearchResults = document.getElementById('no-search-results');
            const emptyState = document.getElementById('empty-state');

            if (!searchInput || !clearSearchBtn || conversationItems.length === 0) {
                return;
            }

            // Store original text for each conversation element
            conversationItems.forEach(item => {
                const nameElement = item.querySelector('.conversation-name');
                const messageElement = item.querySelector('.conversation-message');
                
                if (nameElement) {
                    nameElement.setAttribute('data-original-text', nameElement.textContent);
                }
                if (messageElement) {
                    messageElement.setAttribute('data-original-text', messageElement.textContent);
                }
            });

            // Function to perform search
            function performSearch(searchTerm) {
                const term = searchTerm.toLowerCase().trim();
                let hasVisibleResults = false;

                if (term === '') {
                    // Show all conversations
                    conversationItems.forEach(item => {
                        item.classList.remove('hidden');
                        removeHighlighting(item);
                    });
                    
                    if (emptyState) {
                        emptyState.classList.remove('hidden');
                    }
                    
                    if (noSearchResults) {
                        noSearchResults.classList.add('hidden');
                    }
                    
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
                        removeHighlighting(item);
                    }
                });

                // Show/hide no results message
                if (hasVisibleResults) {
                    if (noSearchResults) {
                        noSearchResults.classList.add('hidden');
                    }
                } else {
                    if (noSearchResults) {
                        noSearchResults.classList.remove('hidden');
                    }
                }
            }

            // Function to highlight matching text
            function highlightText(item, term) {
                const nameElement = item.querySelector('.conversation-name');
                const messageElement = item.querySelector('.conversation-message');
                
                if (nameElement) {
                    const originalName = nameElement.getAttribute('data-original-text') || nameElement.textContent;
                    const highlightedName = originalName.replace(
                        new RegExp(term, 'gi'),
                        match => `<span class="bg-yellow-200 dark:bg-yellow-600 text-[#634600] dark:text-white px-1 rounded">${match}</span>`
                    );
                    nameElement.innerHTML = highlightedName;
                }
                
                if (messageElement) {
                    const originalMessage = messageElement.getAttribute('data-original-text') || messageElement.textContent;
                    const highlightedMessage = originalMessage.replace(
                        new RegExp(term, 'gi'),
                        match => `<span class="bg-yellow-200 dark:bg-yellow-600 text-[#634600] dark:text-white px-1 rounded">${match}</span>`
                    );
                    messageElement.innerHTML = highlightedMessage;
                }
            }

            // Function to remove highlighting
            function removeHighlighting(item) {
                const nameElement = item.querySelector('.conversation-name');
                const messageElement = item.querySelector('.conversation-message');
                
                if (nameElement && nameElement.getAttribute('data-original-text')) {
                    nameElement.textContent = nameElement.getAttribute('data-original-text');
                }
                
                if (messageElement && messageElement.getAttribute('data-original-text')) {
                    messageElement.textContent = messageElement.getAttribute('data-original-text');
                }
            }

            // Function to clear search
            function clearSearch() {
                searchInput.value = '';
                clearSearchBtn.classList.add('hidden');
                
                // Remove highlighting from all items
                conversationItems.forEach(item => {
                    removeHighlighting(item);
                });
                
                performSearch('');
            }

            // Search event listener
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value;
                performSearch(searchTerm);
                
                // Show/hide clear button
                if (searchTerm.length > 0) {
                    clearSearchBtn.classList.remove('hidden');
                } else {
                    clearSearchBtn.classList.add('hidden');
                }
            });

            // Clear search button event listener
            clearSearchBtn.addEventListener('click', function() {
                clearSearch();
                searchInput.focus();
            });

            // Clear search on escape key
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    clearSearch();
                    searchInput.blur();
                }
            });

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

            // Apply debouncing to search input
            const debouncedSearch = debounce((searchTerm) => {
                performSearch(searchTerm);
            }, 300);

            searchInput.addEventListener('input', (e) => {
                debouncedSearch(e.target.value);
            });
        }

        // Initialize search when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize search functionality
            setTimeout(() => {
                initConversationSearch();
            }, 100);

            // Settings dropdown toggle
            const settingsToggleBtn = document.getElementById('messages-settings-toggle-btn');
            const settingsDropdownMenu = document.getElementById('messages-settings-dropdown-menu');
            
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

            // Mark conversation as read when clicked
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    markConversationAsRead(userId);
                });
            });
        });

        function updateGlobalUnreadCount() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/messages/unread-count', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.ok ? response.json() : Promise.reject())
            .then(data => {
                window.dispatchEvent(new CustomEvent('messages-marked-read', {
                    detail: { unread_count: data.unread_count || 0 }
                }));
            })
            .catch(error => console.warn('Unable to refresh global unread count:', error));
        }

        // Function to mark conversation as read
        function markConversationAsRead(userId) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/conversations/${userId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (!response.ok) {
                    throw new Error('Failed to mark conversation as read');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Dispatch event to update all conversation badges
                    window.dispatchEvent(new CustomEvent('conversation-read', {
                        detail: {
                            user_id: parseInt(userId),
                            unread_count: 0
                        }
                    }));
                    
                    // Update the recipient ID in meta tag
                    const recipientMeta = document.querySelector('meta[name="recipient-id"]');
                    if (recipientMeta) {
                        recipientMeta.setAttribute('content', userId);
                    }

                    updateGlobalUnreadCount();
                }
            })
            .catch(error => {
                console.error('Error marking conversation as read:', error);
            });
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

        function showBlockedUsers() {
            // Close dropdown
            const dropdownMenu = document.getElementById('messages-settings-dropdown-menu');
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
        
        .dark .conversation-name span.bg-yellow-600,
        .dark .conversation-message span.bg-yellow-600 {
            background-color: #92400e !important;
            color: white !important;
            font-weight: 600;
            padding: 0 2px;
            border-radius: 3px;
        }
        
        /* Smooth transitions for search */
        .conversation-item {
            transition: all 0.3s ease;
        }
        
        /* Search input focus styles */
        #conversations-search-input:focus {
            border-color: #634600;
            box-shadow: 0 0 0 2px rgba(99, 70, 0, 0.1);
        }
        
        .dark #conversations-search-input:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.2);
        }
        
        /* Clear button hover effects */
        #clear-search-btn:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }
    </style>
</x-app-layout>