<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Users Management') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    @php
        $activeTab = request()->query('tab', 'pending');
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div x-data="{ tab: '{{ $activeTab }}' }" class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg p-6">
                
                <!-- Tabs -->
                <div class="flex space-x-4 border-b dark:border-gray-700 mb-6">
                    <button @click="tab = 'pending'; history.pushState(null, '', '?tab=pending')"
                        :class="tab === 'pending' ? 'border-b-2 border-yellow-500 text-yellow-600 dark:text-yellow-400' : 'text-gray-600 dark:text-gray-300'"
                        class="pb-2 font-semibold">
                        Pending ({{ $pendingUsers->total() }})
                    </button>
                    <button @click="tab = 'verified'; history.pushState(null, '', '?tab=verified')"
                        :class="tab === 'verified' ? 'border-b-2 border-green-500 text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-300'"
                        class="pb-2 font-semibold">
                        Verified ({{ $users->total() }})
                    </button>
                    <button @click="tab = 'unverified'; history.pushState(null, '', '?tab=unverified')"
                        :class="tab === 'unverified' ? 'border-b-2 border-red-500 text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-300'"
                        class="pb-2 font-semibold">
                        Unverified ({{ $unverifiedUsers->total() }})
                    </button>
                    <button @click="tab = 'rejected'; history.pushState(null, '', '?tab=rejected')"
                        :class="tab === 'rejected' ? 'border-b-2 border-red-500 text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-300'"
                        class="pb-2 font-semibold">
                        Rejected ({{ $rejectedUsers->total() }})
                    </button>
                </div>

                <!-- Tab Content -->
                <div x-show="tab === 'pending'" x-cloak class="w-full">
                    <div class="overflow-x-auto">
                        <div id="pending-content">
                            @include('admin.users._table', [
                                'users' => $pendingUsers,
                                'showDocument' => true,
                                'statusColors' => [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'verified' => 'bg-green-100 text-green-800',
                                    'unverified' => 'bg-red-100 text-red-800',
                                ],
                            ])
                        </div>
                        <div id="pending-pagination" class="mt-4">
                            {{ $pendingUsers->appends(['tab' => 'pending'])->links() }}
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'verified'" x-cloak class="w-full">
                    <div class="overflow-x-auto">
                        <div id="verified-content">
                            @include('admin.users._table', ['users' => $users, 'showDocument' => false])
                        </div>
                        <div id="verified-pagination" class="mt-4">
                            {{ $users->appends(['tab' => 'verified'])->links() }}
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'unverified'" x-cloak class="w-full">
                    <div class="overflow-x-auto">
                        <div id="unverified-content">
                            @include('admin.users._table', ['users' => $unverifiedUsers, 'showDocument' => false])
                        </div>
                        <div id="unverified-pagination" class="mt-4">
                            {{ $unverifiedUsers->appends(['tab' => 'unverified'])->links() }}
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'rejected'" x-cloak class="w-full">
                    <div class="overflow-x-auto">
                        <div id="rejected-content">
                            @include('admin.users._table', ['users' => $rejectedUsers, 'showDocument' => false])
                        </div>
                        <div id="rejected-pagination" class="mt-4">
                            {{ $rejectedUsers->appends(['tab' => 'rejected'])->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        let currentTab = '{{ $activeTab }}';

        document.addEventListener('DOMContentLoaded', function() {
            attachPaginationListeners();
        });

        function attachPaginationListeners() {
            document.querySelectorAll('a[href*="?page="]').forEach(link => {
                link.removeEventListener('click', handlePaginationClick);
                link.addEventListener('click', handlePaginationClick);
            });
        }

        function handlePaginationClick(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            if (!url) return;

            loadPaginatedData(url);
        }

        async function loadPaginatedData(url) {
            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Update all tab contents and paginations
                const tabs = ['pending', 'verified', 'unverified', 'rejected'];
                
                tabs.forEach(tab => {
                    const newContent = doc.getElementById(`${tab}-content`);
                    const newPagination = doc.getElementById(`${tab}-pagination`);
                    
                    if (newContent) {
                        const currentContent = document.getElementById(`${tab}-content`);
                        if (currentContent) {
                            currentContent.innerHTML = newContent.innerHTML;
                        }
                    }
                    
                    if (newPagination) {
                        const currentPagination = document.getElementById(`${tab}-pagination`);
                        if (currentPagination) {
                            currentPagination.innerHTML = newPagination.innerHTML;
                        }
                    }
                });

                // Reattach pagination listeners
                attachPaginationListeners();

                // Scroll to table
                document.querySelector('.overflow-x-auto')?.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });

            } catch (error) {
                console.error('Error loading paginated data:', error);
                alert('Error loading data. Please try again.');
            }
        }
    </script>
</x-app-layout>
