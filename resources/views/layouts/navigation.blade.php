<nav class="fixed top-0 left-0 w-full bg-[#F4F2ED] dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-4 sm:px-6 md:px-6 py-4 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto" x-data="{ mobileMenuOpen: false }">
        
        <div class="flex justify-between items-center">
            <a href="{{ Auth::check() ? (Auth::user()->role === 2 ? route('admin.dashboard') : (Auth::user()->role === 1 ? route('upcycler') : route('dashboard'))) : url('/') }}"
                class="flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="THRIFT - IT Logo" class="h-10 sm:h-12">
            </a>

            @auth
                @php $role = Auth::user()->role; @endphp
                <div class="hidden md:flex items-center gap-2 lg:gap-4 ml-6 ">
                    @if ($role === 0)
                        <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-gray-200 hidden font-bold lg:block">Home</a>
                        <a href="{{ route('products.index') }}" class="text-gray-700 dark:text-gray-200 hidden font-bold lg:block">Sell</a>
                        <a href="{{ route('donations.hub') }}" class="text-gray-700 dark:text-gray-200 hidden font-bold lg:block">Donation Hub</a>
                        <a href="{{ route('appointments.index') }}" class="text-gray-700 dark:text-gray-200 hidden font-bold lg:block">Upcycle</a>
                    @elseif($role === 1)
                        <a href="{{ route('upcycler.index') }}" class="text-gray-700 dark:text-gray-200 hidden font-bold lg:block">Appointments</a>
                        <a href="{{ route('works.index') }}" class="text-gray-700 dark:text-gray-200 hidden font-bold lg:block">Upcycling Works</a>
                        <a href="{{ route('eco-posts.index') }}" class="text-gray-700 dark:text-gray-200 hidden font-bold lg:block">Eco Portal</a>
                    @endif
                </div>
            @endauth

            @auth
                @if ($role !== 2)
                    <div class="hidden md:flex items-center bg-[#F4F2ED] dark:bg-gray-800 px-4 rounded-full w-full max-w-md border border-gray-400 dark:text-gray-200 mx-4">
                        <form action="{{ route('search') }}" method="GET" class="flex w-full items-center">
                            <input type="text" name="query" value="{{ request('query') }}"
                                placeholder="Search for an item or a person ..."
                                class="w-full dark:placeholder:text-gray-200 outline-none text-sm bg-transparent border-0 focus:outline-none focus:ring-0 focus:border-0 focus:shadow-none"
                                required>
                            <button type="submit" class="ml-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 1 1 2.83 6.83l3.88 3.88a1 1 0 0 1-1.42 1.42l-3.88-3.88A4 4 0 0 1 8 4zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endif
            @endauth

            <div class="hidden md:flex items-center gap-2 lg:gap-4">
                @auth
                    @if ($role !== 2)
                     <a href="{{ route('help') }}" class="text-gray-700 dark:text-gray-200 hover:text-[#B59F84] transition-colors duration-200" title="Help & Support">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </a>
                        <a href="{{ route('favorites.index') }}" class="text-gray-700 dark:text-gray-200 hover:text-[#B59F84] transition-colors duration-200" title="Favorites">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </a>
                        <a href="{{ route('messages.index') }}" class="text-gray-700 dark:text-gray-200 relative"
                            x-data="{
                                unreadCount: {{ $totalUnreadCount ?? 0 }},
                                init() {
                                    @if (Auth::check()) if (typeof Echo !== 'undefined') {
                                        Echo.private('chat.user.{{ Auth::id() }}')
                                            .listen('.private-message', (e) => {
                                                if (!window.location.pathname.includes('messages')) {
                                                    this.unreadCount++;
                                                }
                                            });
                                        window.addEventListener('messages-marked-read', (e) => {
                                            this.unreadCount = e.detail?.unread_count || 0;
                                        });
                                    } @endif
                                    window.addEventListener('new-message-received', () => {
                                        if (!window.location.pathname.includes('messages')) {
                                            this.unreadCount++;
                                        }
                                    });
                                    window.addEventListener('messages-marked-read', (e) => {
                                        this.unreadCount = e.detail?.unread_count || 0;
                                    });
                                }
                            }">
                            <svg class="w-6 h-6 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.325 0-2.58-.26-3.68-.725L3 20l1.32-3.96C3.474 15.003 3 13.55 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span x-show="unreadCount > 0" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[1.25rem] flex items-center justify-center transition-all duration-300 z-10">
                                <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                            </span>
                        </a>
                       

                        <div id="notif-bell" x-data="{
                            open: false,
                            notifications: [],
                            groupedNotifications: {},
                            unreadCount: {{ \App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->count() }},
                            loaded: false,
                            initialLoading: false,
                            loadingMore: false,
                            hasMore: true,
                            page: 1,
                            
                            getGroupedNotifications() {
                                const groups = {};
                                const today = new Date();
                                today.setHours(0, 0, 0, 0);
                                const todayStr = today.toDateString();
                                this.notifications.forEach(notif => {
                                    const notifDate = new Date(notif.created_at);
                                    notifDate.setHours(0, 0, 0, 0);
                                    const notifDateStr = notifDate.toDateString();
                                    let groupName;
                                    if (notifDateStr === todayStr) groupName = 'Today';
                                    else {
                                        const yesterday = new Date(today);
                                        yesterday.setDate(yesterday.getDate() - 1);
                                        if (notifDateStr === yesterday.toDateString()) groupName = 'Yesterday';
                                        else {
                                            const weekAgo = new Date(today);
                                            weekAgo.setDate(weekAgo.getDate() - 7);
                                            if (notifDate >= weekAgo) groupName = 'This week';
                                            else groupName = notifDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                                        }
                                    }
                                    if (!groups[groupName]) groups[groupName] = [];
                                    groups[groupName].push(notif);
                                });
                                return groups;
                            },
                            toggleNotifications() {
                                this.open = !this.open;
                                if (this.open) {
                                    if (!this.loaded) this.loadInitialNotifications();
                                    this.markAsRead();
                                }
                            },
                            loadInitialNotifications() {
                                this.initialLoading = true;
                                fetch('{{ route('notifications.load-more') }}?page=1')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.notifications) {
                                            this.notifications = data.notifications;
                                            this.groupedNotifications = this.getGroupedNotifications();
                                            this.hasMore = data.has_more;
                                            this.loaded = true;
                                        }
                                        this.initialLoading = false;
                                    });
                            },
                            markAsRead() {
                                if (this.unreadCount === 0) return;
                                fetch('{{ route('notifications.read') }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
                                }).then(r => r.json()).then(data => {
                                    if (data.success) {
                                        this.unreadCount = 0;
                                        this.notifications.forEach(n => n.is_read = true);
                                        window.dispatchEvent(new CustomEvent('notifications-marked-read', { detail: { unread_count: 0 } }));
                                    }
                                });
                            },
                            loadMoreNotifications() {
                                if (this.loadingMore || !this.hasMore) return;
                                this.loadingMore = true;
                                this.page++;
                                fetch(`/notifications/load-more?page=${this.page}`).then(r => r.json()).then(data => {
                                    if (data.notifications && data.notifications.length > 0) {
                                        this.notifications = [...this.notifications, ...data.notifications];
                                        this.groupedNotifications = this.getGroupedNotifications();
                                        this.hasMore = data.has_more;
                                    } else this.hasMore = false;
                                    this.loadingMore = false;
                                });
                            },
                            init() {
                                window.addEventListener('notifications-marked-read', (e) => {
                                    this.unreadCount = e.detail?.unread_count || 0;
                                    this.notifications.forEach(n => n.is_read = true);
                                });
                            }
                        }" @new-notification.window="notifications.unshift($event.detail); unreadCount++; groupedNotifications = getGroupedNotifications();">
                            
                            <button @click="toggleNotifications()" class="relative focus:outline-none mt-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-700 dark:text-gray-200">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.971 8.971 0 0118 9.75V9a6 6 0 10-12 0v.75a8.971 8.971 0 01-2.311 6.022c1.742.68 3.55 1.17 5.454 1.31m5.714 0a24.048 24.048 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                                <span x-show="unreadCount > 0" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5"><span x-text="unreadCount"></span></span>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-20 mt-2 w-96 bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden z-50 border border-gray-200">
                                <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 dark:bg-gray-800 flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Notifications</span>
                                    <button @click="markAsRead()" class="text-xs text-[#B59F84] hover:underline">Mark all as read</button>
                                </div>
                                <div class="flex flex-col" style="max-height: 70vh;">
                                    <div x-show="initialLoading" class="p-8 flex justify-center items-center">
                                        <svg class="animate-spin h-6 w-6 text-[#B59F84]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </div>
                                    <div x-show="!initialLoading" class="flex-1 overflow-y-auto custom-scroll">
                                        <template x-for="[groupName, groupNotifications] in Object.entries(groupedNotifications)" :key="groupName">
                                            <div>
                                                <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 sticky top-0 z-10"><span class="text-xs font-semibold text-gray-600 dark:text-gray-300" x-text="groupName"></span></div>
                                                <template x-for="notif in groupNotifications" :key="notif.id">
                                                   <a :href="notif.data.order_id ? `/profile/${notif.user_id}?tab=orders`: notif.data.product_id ? `/products/${notif.data.product_id}` : (notif.data.appointment_id ? `/upcycler/${notif.data.appointment_id}`  : (notif.data.donation_id  ? `/donations/${notif.data.donation_id}`  : (notif.data.link || '#')) )
                                                        " class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 last:border-b-0" @click="open = false">
                                                        <div class="flex items-start gap-3">
                                                            <div class="flex-shrink-0"><img :src="notif.data.profile_pic_url || '{{ asset('images/default-profile.jpg') }}'" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600"></div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm text-gray-700 dark:text-gray-200 mb-1"><strong class="text-[#B59F84]" x-text="notif.data.from_user || 'System'"></strong> <span x-text="notif.data.message"></span></p>
                                                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="new Date(notif.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })"></span>
                                                            </div>
                                                            <span x-show="!notif.is_read" class="ml-2 w-2 h-2 bg-[#B59F84] rounded-full mt-1.5 flex-shrink-0"></span>
                                                        </div>
                                                    </a>
                                                </template>
                                            </div>
                                        </template>
                                        <div x-show="notifications.length === 0 && !initialLoading" class="px-4 py-8 text-center"><p class="text-gray-500 dark:text-gray-400 text-sm">No notifications yet</p></div>
                                    </div>
                                    <div x-show="!initialLoading" class="border-t border-gray-200 bg-white dark:bg-gray-800">
                                        <div x-show="hasMore && notifications.length > 0" class="border-b border-gray-200">
                                            <button @click="loadMoreNotifications()" :disabled="loadingMore" class="w-full px-4 py-3 text-sm text-center text-[#B59F84] font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition"><span x-show="!loadingMore">Show more notifications</span><span x-show="loadingMore">Loading...</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    @endif
                @endauth

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = ! open" class="text-gray-700 flex items-center dark:text-gray-200">
                            <span>{{ Auth::user()->fname }}</span>
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 dark:text-gray-200 shadow-lg rounded-md z-50">
                            @if (Auth::user()->role != 2)
                                <a href="{{ route('profile.show', ['user' => Auth::id()]) }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200  hover:bg-gray-200 dark:hover:bg-gray-700">Profile</a>
                                <div x-data="{ settingsOpen: false }" class="relative">
                                    <button @click="settingsOpen = !settingsOpen" class="w-full text-left flex items-center justify-between px-4 py-2 text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md transition-colors duration-200">
                                        <span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg> Settings</span>
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': settingsOpen }" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </button>
                                    <div x-show="settingsOpen" @click.away="settingsOpen = false" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 shadow-lg rounded-md border border-gray-200 dark:border-gray-700 z-50">
                                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700"><span class="text-xs font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wide flex items-center gap-2">Personal Information</span></div>
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center gap-3">Update Profile Information</a>
                                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700"><span class="text-xs font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wide flex items-center gap-2">Security & Sign-in</span></div>
                                        <a href="{{ route('profile.edit1') }}" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center gap-3">Update Password</a>
                                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700"><span class="text-xs font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wide flex items-center gap-2">Data & Privacy</span></div>
                                        <a href="{{ route('profile.edit2') }}" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center gap-3">Data & Privacy</a>
                                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700"><span class="text-xs font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wide flex items-center gap-2">Payment & Subscription</span></div>
                                        <a href="{{ route('pricing.index') }}" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center gap-3">Pricing Plans</a>
                                    </div>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-gray-700 dark:text-gray-200 dark:hover:bg-gray-00 hover:bg-gray-200">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-[#B59F84] dark:text-gray-200 text-white px-[20px] py-1.5 rounded-[25px] text-base font-semibold hover:bg-[#a08e77] hover:scale-105 transition-all duration-200  w-[100px]">Sign up</a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center dark:text-gray-200 bg-[#B59F84] text-white px-[20px] py-1.5 rounded-[25px] text-base font-semibold hover:bg-[#a08e77] hover:scale-105 transition-all duration-200 w-[100px]">Login</a>
                @endauth
            </div>

            <div class="flex items-center justify-between md:hidden w-full px-4 py-1 bg-[#F4F2ED] dark:bg-gray-800 dark:text-gray-200">
                <div class="flex-1 mx-2">
                    <form action="{{ route('search') }}" method="GET" class="flex items-center bg-white dark:bg-gray-800 dark:text-gray-200 px-3 py-2 rounded-full shadow-sm border">
                        <input type="text" name="query" value="{{ request('query') }}" placeholder="Search for a product..." class="w-full border-none outline-none text-sm bg-transparent text-gray-700 dark:text-gray-200" required>
                        <button type="submit" class="ml-2 text-gray-500 hover:text-blue-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 1 1 2.83 6.83l3.88 3.88a1 1 0 0 1-1.42 1.42l-3.88-3.88A4 4 0 0 1 8 4zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" clip-rule="evenodd" /></svg>
                        </button>
                    </form>
                </div>

              <div class="flex items-center space-x-1"> @auth
        @if ($role !== 2)
            <a href="{{ route('favorites.index') }}" class="p-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors" title="Favorites">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </a>
            <a href="{{ route('messages.index') }}" class="p-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-800 relative transition-colors"
                x-data="{
                    unreadCount: {{ $totalUnreadCount ?? 0 }},
                    init() {
                        @if (Auth::check()) if (typeof Echo !== 'undefined') {
                            Echo.private('chat.user.{{ Auth::id() }}').listen('.private-message', (e) => { if (!window.location.pathname.includes('messages')) { this.unreadCount++; } });
                            window.addEventListener('messages-marked-read', (e) => { this.unreadCount = e.detail?.unread_count || 0; });
                        } @endif
                        window.addEventListener('new-message-received', () => { if (!window.location.pathname.includes('messages')) { this.unreadCount++; } });
                        window.addEventListener('messages-marked-read', (e) => { this.unreadCount = e.detail?.unread_count || 0; });
                    }
                }">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.325 0-2.58-.26-3.68-.725L3 20l1.32-3.96C3.474 15.003 3 13.55 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <span x-show="unreadCount > 0" class="absolute top-1 right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[1.25rem] flex items-center justify-center"><span x-text="unreadCount > 9 ? '9+' : unreadCount"></span></span>
            </a>
        @endif
    @endauth

    <div id="notif-bell-mobile" class="relative" x-data="{
        open: false,
        notifications: [],
        groupedNotifications: {},
        unreadCount: {{ \App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->count() }},
        loaded: false,
        initialLoading: false,
        loadingMore: false,
        hasMore: true,
        page: 1,
        
        getGroupedNotifications() {
            const groups = {};
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const todayStr = today.toDateString();
            this.notifications.forEach(notif => {
                const notifDate = new Date(notif.created_at);
                notifDate.setHours(0, 0, 0, 0);
                const notifDateStr = notifDate.toDateString();
                let groupName;
                if (notifDateStr === todayStr) groupName = 'Today';
                else {
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    if (notifDateStr === yesterday.toDateString()) groupName = 'Yesterday';
                    else {
                        const weekAgo = new Date(today);
                        weekAgo.setDate(weekAgo.getDate() - 7);
                        if (notifDate >= weekAgo) groupName = 'This week';
                        else groupName = notifDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }
                }
                if (!groups[groupName]) groups[groupName] = [];
                groups[groupName].push(notif);
            });
            return groups;
        },
        toggleNotifications() {
            this.open = !this.open;
            if (this.open) {
                if (!this.loaded) this.loadInitialNotifications();
                this.markAsRead();
            }
        },
        loadInitialNotifications() {
            this.initialLoading = true;
            fetch('{{ route('notifications.load-more') }}?page=1')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications) {
                        this.notifications = data.notifications;
                        this.groupedNotifications = this.getGroupedNotifications();
                        this.hasMore = data.has_more;
                        this.loaded = true;
                    }
                    this.initialLoading = false;
                });
        },
        markAsRead() {
            if (this.unreadCount === 0) return;
            fetch('{{ route('notifications.read') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    this.unreadCount = 0;
                    this.notifications.forEach(n => n.is_read = true);
                    window.dispatchEvent(new CustomEvent('notifications-marked-read', { detail: { unread_count: 0 } }));
                }
            });
        },
        loadMoreNotifications() {
            if (this.loadingMore || !this.hasMore) return;
            this.loadingMore = true;
            this.page++;
            fetch(`/notifications/load-more?page=${this.page}`).then(r => r.json()).then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    this.notifications = [...this.notifications, ...data.notifications];
                    this.groupedNotifications = this.getGroupedNotifications();
                    this.hasMore = data.has_more;
                } else this.hasMore = false;
                this.loadingMore = false;
            });
        },
        init() {
            window.addEventListener('notifications-marked-read', (e) => {
                this.unreadCount = e.detail?.unread_count || 0;
                this.notifications.forEach(n => n.is_read = true);
            });
        }
    }" @new-notification.window="notifications.unshift($event.detail); unreadCount++; groupedNotifications = getGroupedNotifications();">

        <button @click="toggleNotifications()" class="p-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-800 relative focus:outline-none transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.971 8.971 0 0118 9.75V9a6 6 0 10-12 0v.75a8.971 8.971 0 01-2.311 6.022c1.742.68 3.55 1.17 5.454 1.31m5.714 0a24.048 24.048 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            <span x-show="unreadCount > 0" class="absolute top-1 right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5"><span x-text="unreadCount"></span></span>
        </button>

        <div x-show="open" @click.away="open = false" x-transition 
             class="absolute -right-12 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden z-50 border border-gray-200 origin-top-right">
            
            <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 dark:bg-gray-800 flex justify-between items-center">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Notifications</span>
                <button @click="markAsRead()" class="text-xs text-[#B59F84] hover:underline">Mark all as read</button>
            </div>

            <div class="flex flex-col" style="max-height: 70vh;">
                <div x-show="initialLoading" class="p-8 flex justify-center items-center">
                    <svg class="animate-spin h-6 w-6 text-[#B59F84]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <div x-show="!initialLoading" class="flex-1 overflow-y-auto custom-scroll">
                    <template x-for="[groupName, groupNotifications] in Object.entries(groupedNotifications)" :key="groupName">
                        <div>
                            <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border-b border-gray-200"><span class="text-xs font-semibold text-gray-600 dark:text-gray-300" x-text="groupName"></span></div>
                            <template x-for="notif in groupNotifications" :key="notif.id">
                              <a :href="notif.data.order_id ? `/profile/${notif.user_id}?tab=orders` : notif.data.product_id ? `/products/${notif.data.product_id}` : (notif.data.appointment_id ? `/upcycler/${notif.data.appointment_id}`  : (notif.data.donation_id ? `/donations/${notif.data.donation_id}` : (notif.data.link || '#')))
                        " class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition" @click="open = false">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0"><img :src="notif.data.profile_pic_url || '{{ asset('images/default-profile.jpg') }}'" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-700 dark:text-gray-200 mb-1"><strong class="text-[#B59F84]" x-text="notif.data.from_user || 'System'"></strong> <span x-text="notif.data.message"></span></p>
                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="new Date(notif.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })"></span>
                                        </div>
                                        <span x-show="!notif.is_read" class="ml-2 w-2 h-2 bg-[#B59F84] rounded-full mt-1.5 flex-shrink-0"></span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                    <div x-show="notifications.length === 0 && !initialLoading" class="px-4 py-8 text-center"><p class="text-gray-500 dark:text-gray-400 text-sm">No notifications yet</p></div>
                </div>

                <div x-show="!initialLoading" class="border-t border-gray-200 bg-white dark:bg-gray-800">
                    <div x-show="hasMore && notifications.length > 0" class="border-b border-gray-200">
                        <button @click="loadMoreNotifications()" :disabled="loadingMore" class="w-full px-4 py-3 text-sm text-center text-[#B59F84] font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition"><span x-show="!loadingMore">Show more notifications</span><span x-show="loadingMore">Loading...</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

   

    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-800 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
    </button>
</div>
            </div>

            <div x-show="mobileMenuOpen" x-transition class="mobile-menu-dropdown fixed top-[90px] inset-x-0 bg-white dark:bg-gray-800 dark:text-gray-200 shadow-lg border-t border-gray-200 md:hidden z-50 max-h-[70vh] overflow-y-auto">
                <div class="flex flex-col space-y-0 px-4 py-4">
                    @auth
                        @if ($role !== 2)
                            <a href="{{ route('messages.index') }}" class="flex items-center text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] border-b border-gray-100 relative"
                                x-data="{
                                    unreadCount: {{ $totalUnreadCount ?? 0 }},
                                    init() {
                                        @if (Auth::check()) if (typeof Echo !== 'undefined') {
                                            Echo.private('chat.user.{{ Auth::id() }}').listen('.private-message', (e) => { if (!window.location.pathname.includes('messages')) { this.unreadCount++; } });
                                            window.addEventListener('messages-marked-read', (e) => { this.unreadCount = e.detail?.unread_count || 0; });
                                        } @endif
                                        window.addEventListener('new-message-received', () => { if (!window.location.pathname.includes('messages')) { this.unreadCount++; } });
                                        window.addEventListener('messages-marked-read', (e) => { this.unreadCount = e.detail?.unread_count || 0; });
                                    }
                                }">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.325 0-2.58-.26-3.68-.725L3 20l1.32-3.96C3.474 15.003 3 13.55 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <span class="font-medium">Messages</span>
                                <span x-show="unreadCount > 0" class="ml-2 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[1.25rem] flex items-center justify-center transition-all duration-300"><span x-text="unreadCount > 9 ? '9+' : unreadCount"></span></span>
                            </a>
                        @endif

                        @if ($role === 0)
                            <a href="{{ route('dashboard') }}" class="flex items-center text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] border-b border-gray-100 font-medium">Home</a>
                            <a href="{{ route('products.index') }}" class="flex items-center text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] border-b border-gray-100 font-medium">Sell</a>
                            <a href="{{ route('donations.hub') }}" class="flex items-center text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] border-b border-gray-100 font-medium">Donation Hub</a>
                            <a href="{{ route('appointments.index') }}" class="flex items-center text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] border-b border-gray-100 font-medium">Upcycle</a>
                            @elseif($role === 1)
                            <a href="{{ route('upcycler.index') }}" class="flex items-center text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] border-b border-gray-100 font-medium">Manage Appointments</a>
                            <a href="{{ route('works.index') }}" class="flex items-center text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] border-b border-gray-100 font-medium">Upcycling Works</a>
                            <a href="{{ route('eco-posts.index') }}" class="flex items-center text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] border-b border-gray-100 font-medium">Eco Portal</a>
                        @endif
                        <a href="{{ route('profile.show', ['user' => Auth::id()]) }}" class="flex items-center text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] border-b border-gray-100 font-medium">Profile</a>
                        <div x-data="{ mobileSettingsOpen: false }" class="relative border-b border-gray-100 dark:text-gray-200">
                            <button @click="mobileSettingsOpen = !mobileSettingsOpen" class="w-full text-left flex items-center justify-between text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] font-medium">
                                <span>Settings</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': mobileSettingsOpen }" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                            <div x-show="mobileSettingsOpen" x-transition class="ml-4 mt-2 space-y-0 pb-2 dark:bg-gray-800">
                                <div class="px-2 py-1 dark:bg-gray-800"><span class="text-xs font-semibold text-gray-500 dark:text-gray-200 dark:bg-gray-800 uppercase tracking-wide">Personal Information</span></div>
                                <a href="{{ route('profile.edit') }}" class="block px-2 py-2 text-sm text-gray-600 dark:text-gray-200 hover:text-[#B59F84]">Update Profile Information</a>
                                <div class="px-2 py-1 mt-2 dark:bg-gray-800"><span class="text-xs font-semibold text-gray-500 dark:text-gray-200 uppercase tracking-wide">Security & Sign-in</span></div>
                                <a href="{{ route('profile.edit1') }}#update-password" class="block px-2 py-2 text-sm text-gray-600 dark:text-gray-200 hover:text-[#B59F84]">Update Password</a>
                                <div class="px-2 py-1 mt-2"><span class="text-xs font-semibold text-gray-500 dark:text-gray-200 uppercase tracking-wide">Data & Privacy</span></div>
                                <a href="{{ route('profile.edit2') }}" class="block px-2 py-2 text-sm text-gray-600 dark:text-gray-200 hover:text-[#B59F84]">Data & Privacy</a>
                                <div class="px-2 py-1 mt-2"><span class="text-xs font-semibold text-gray-500 dark:text-gray-200 uppercase tracking-wide">Payment & Subscription</span></div>
                                <a href="{{ route('pricing.index') }}" class="block px-2 py-2 text-sm text-gray-600 dark:text-gray-200 hover:text-[#B59F84]">Pricing Plans</a>
                                <a href="{{ route('help') }}" class="block px-2 py-2 text-sm text-gray-600 dark:text-gray-200 hover:text-[#B59F84]">Help & Support</a>
                            
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center text-gray-700 dark:text-gray-200 py-3 hover:text-[#B59F84] font-medium">Log Out</button>
                        </form>
                    @else
                        <a href="{{ route('register') }}" class="flex items-center text-gray-700 py-3 dark:text-gray-200 hover:text-[#B59F84] border-b border-gray-100 font-medium">Sign up</a>
                        <a href="{{ route('login') }}" class="flex items-center text-gray-700 py-3 dark:text-gray-200 hover:text-[#B59F84] border-b border-gray-100 font-medium">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    body { padding-top: 72px; }
    @media (max-width: 767px) {
        .mobile-menu-dropdown {
            position: fixed !important;
            top: 90px !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            transform: translateX(0) !important;
            margin: 0 !important;
            max-width: 100vw !important;
        }
    }
    .custom-scroll { scrollbar-width: thin; scrollbar-color: #c1c1c1 transparent; }
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background-color: #c1c1c1; border-radius: 20px; }
    .dark .custom-scroll::-webkit-scrollbar-thumb { background-color: #4a5568; }
    #notification-toast { transition: all 0.3s ease-in-out; }
    .notification-item-unread { background-color: rgba(245, 158, 11, 0.05); }
    .dark .notification-item-unread { background-color: rgba(245, 158, 11, 0.1); }
    .notification-item-read { opacity: 0.7; }
    .mark-all-read-btn:hover { transform: translateY(-1px); transition: transform 0.2s ease; }
</style>

<script>
    function refreshUnreadCount() {
        fetch('{{ route('messages.unread-count') }}', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(response => response.json())
        .then(data => {
            window.dispatchEvent(new CustomEvent('messages-marked-read', { detail: { unread_count: data.unread_count || 0 } }));
        }).catch(error => { console.error('Error refreshing unread count:', error); });
    }

    function markMessagesAsRead() {
        fetch('{{ route('messages.mark-read') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(response => response.json())
        .then(data => {
            window.dispatchEvent(new CustomEvent('messages-marked-read', { detail: { unread_count: data.unread_count || 0 } }));
        }).catch(error => { console.error('Error marking messages as read:', error); });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.pathname.includes('messages')) { markMessagesAsRead(); }
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) { refreshUnreadCount(); }
        });
        if (window.location.pathname.includes('chat/')) {
            setTimeout(() => { markMessagesAsRead(); }, 500);
        }

        // Global favorite button handler
        function initGlobalFavoriteButtons() {
            document.querySelectorAll('.favorite-btn').forEach(button => {
                const productId = button.getAttribute('data-id');
                if (!productId) return;

                // Skip if already initialized
                if (button.dataset.initialized === 'true') return;
                button.dataset.initialized = 'true';

                // Check initial favorite status
                fetch(`/products/${productId}/favorite/check`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const svg = button.querySelector('svg');
                    if (data.isFavorited) {
                        svg.setAttribute('fill', 'currentColor');
                        svg.setAttribute('stroke', 'none');
                        button.classList.add('text-red-500');
                        button.classList.remove('text-gray-400');
                    } else {
                        svg.setAttribute('fill', 'none');
                        svg.setAttribute('stroke', 'currentColor');
                        button.classList.remove('text-red-500');
                        button.classList.add('text-gray-400');
                    }
                })
                .catch(error => console.error('Error checking favorite status:', error));

                // Handle click
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const svg = this.querySelector('svg');
                    const isCurrentlyFavorited = svg.getAttribute('fill') === 'currentColor';

                    // Optimistic UI update
                    if (isCurrentlyFavorited) {
                        svg.setAttribute('fill', 'none');
                        svg.setAttribute('stroke', 'currentColor');
                        this.classList.remove('text-red-500');
                        this.classList.add('text-gray-400');
                    } else {
                        svg.setAttribute('fill', 'currentColor');
                        svg.setAttribute('stroke', 'none');
                        this.classList.add('text-red-500');
                        this.classList.remove('text-gray-400');
                    }

                    // Make API call
                    fetch(`/products/${productId}/favorite`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            // Revert on error
                            if (isCurrentlyFavorited) {
                                svg.setAttribute('fill', 'currentColor');
                                svg.setAttribute('stroke', 'none');
                                this.classList.add('text-red-500');
                                this.classList.remove('text-gray-400');
                            } else {
                                svg.setAttribute('fill', 'none');
                                svg.setAttribute('stroke', 'currentColor');
                                this.classList.remove('text-red-500');
                                this.classList.add('text-gray-400');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error toggling favorite:', error);
                        // Revert on error
                        if (isCurrentlyFavorited) {
                            svg.setAttribute('fill', 'currentColor');
                            svg.setAttribute('stroke', 'none');
                            this.classList.add('text-red-500');
                            this.classList.remove('text-gray-400');
                        } else {
                            svg.setAttribute('fill', 'none');
                            svg.setAttribute('stroke', 'currentColor');
                            this.classList.remove('text-red-500');
                            this.classList.add('text-gray-400');
                        }
                    });
                });
            });
        }

        // Initialize on page load
        initGlobalFavoriteButtons();

        // Re-initialize when new content is loaded (for AJAX updates)
        const observer = new MutationObserver(function(mutations) {
            initGlobalFavoriteButtons();
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });
</script>