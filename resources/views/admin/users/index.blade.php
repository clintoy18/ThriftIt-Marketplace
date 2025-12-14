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

            <div x-data="{ 
                tab: '{{ $activeTab }}',
                search: {
                    pending: '',
                    verified: '',
                    unverified: '',
                    rejected: ''
                },
                sort: {
                    pending: 'newest',
                    verified: 'newest',
                    unverified: 'newest',
                    rejected: 'newest'
                },
                matchesSearch(userName, userEmail, status) {
                    const query = this.search[status].toLowerCase().trim();
                    if (!query) return true;
                    const nameMatch = userName.toLowerCase().includes(query);
                    const emailMatch = userEmail.toLowerCase().includes(query);
                    return nameMatch || emailMatch;
                },
                sortRows(status) {
                    setTimeout(() => {
                        const activeTab = document.querySelector(`div[x-show*='${status}']`);
                        if (!activeTab || !activeTab.offsetParent) return;
                        
                        const tbody = activeTab.querySelector('tbody');
                        if (!tbody) return;
                        
                        // Check if Documents column exists (for pending tab)
                        const firstRow = tbody.querySelector('tr');
                        const hasDocuments = firstRow && firstRow.querySelectorAll('td').length > 7;
                        const dateColumnIndex = hasDocuments ? 7 : 6; // Joined date column
                        
                        const allRows = Array.from(tbody.querySelectorAll('tr'));
                        const visibleRows = allRows.filter(row => {
                            const nameCell = row.querySelector('td:nth-child(1)');
                            const emailCell = row.querySelector('td:nth-child(3)');
                            if (!nameCell || !emailCell) return false;
                            const lastName = nameCell.textContent.trim();
                            const firstName = row.querySelector('td:nth-child(2)')?.textContent.trim() || '';
                            const fullName = `${firstName} ${lastName}`.trim();
                            const email = emailCell.textContent.trim();
                            return this.matchesSearch(fullName, email, status);
                        });
                        
                        const sortType = this.sort[status];
                        visibleRows.sort((a, b) => {
                            if (sortType === 'newest' || sortType === 'oldest') {
                                const aDateText = a.querySelector(`td:nth-child(${dateColumnIndex})`)?.textContent.trim() || '';
                                const bDateText = b.querySelector(`td:nth-child(${dateColumnIndex})`)?.textContent.trim() || '';
                                const aDate = new Date(aDateText);
                                const bDate = new Date(bDateText);
                                return sortType === 'newest' ? bDate.getTime() - aDate.getTime() : aDate.getTime() - bDate.getTime();
                            } else if (sortType === 'a-z' || sortType === 'z-a') {
                                const aLastName = (a.querySelector('td:nth-child(1)')?.textContent.trim() || '').toLowerCase();
                                const bLastName = (b.querySelector('td:nth-child(1)')?.textContent.trim() || '').toLowerCase();
                                const aFirstName = (a.querySelector('td:nth-child(2)')?.textContent.trim() || '').toLowerCase();
                                const bFirstName = (b.querySelector('td:nth-child(2)')?.textContent.trim() || '').toLowerCase();
                                const aName = `${aFirstName} ${aLastName}`.trim();
                                const bName = `${bFirstName} ${bLastName}`.trim();
                                return sortType === 'a-z' ? aName.localeCompare(bName) : bName.localeCompare(aName);
                            }
                            return 0;
                        });
                        
                        const hiddenRows = allRows.filter(row => !visibleRows.includes(row));
                        const sortedAllRows = [...visibleRows, ...hiddenRows];
                        sortedAllRows.forEach(row => tbody.appendChild(row));
                    }, 50);
                }
            }" 
            x-effect="if (tab) { sortRows(tab); }"
            x-init="setTimeout(() => sortRows(tab), 200)"
            class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg p-6">
                
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
                    {{-- Search Bar and Sort Dropdown --}}
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="relative w-96">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                x-model="search.pending"
                                @input="sortRows('pending')"
                                placeholder="Search by name or email..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="sort-pending" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Sort by:
                            </label>
                            <select 
                                id="sort-pending"
                                x-model="sort.pending"
                                @change="sortRows('pending')"
                                class="block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                            >
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="a-z">A → Z (Name)</option>
                                <option value="z-a">Z → A (Name)</option>
                            </select>
                        </div>
                    </div>
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
                        {{-- No Search Results Message --}}
                        <div 
                            x-show="search.pending.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                const nameCell = row.querySelector('td:nth-child(1)');
                                const emailCell = row.querySelector('td:nth-child(3)');
                                if (!nameCell || !emailCell) return false;
                                const lastName = nameCell.textContent.trim();
                                const firstName = row.querySelector('td:nth-child(2)')?.textContent.trim() || '';
                                const fullName = `${firstName} ${lastName}`.trim();
                                const email = emailCell.textContent.trim();
                                return matchesSearch(fullName, email, 'pending') && row.offsetParent !== null;
                            }).length === 0"
                            x-cloak
                            class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-yellow-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No users found matching your search.</p>
                                <p class="text-sm">Try adjusting your search terms.</p>
                            </div>
                        </div>
                        <div id="pending-pagination" class="mt-4">
                            {{ $pendingUsers->appends(['tab' => 'pending'])->links() }}
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'verified'" x-cloak class="w-full">
                    {{-- Search Bar and Sort Dropdown --}}
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="relative w-96">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                x-model="search.verified"
                                @input="sortRows('verified')"
                                placeholder="Search by name or email..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="sort-verified" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Sort by:
                            </label>
                            <select 
                                id="sort-verified"
                                x-model="sort.verified"
                                @change="sortRows('verified')"
                                class="block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            >
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="a-z">A → Z (Name)</option>
                                <option value="z-a">Z → A (Name)</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <div id="verified-content">
                            @include('admin.users._table', ['users' => $users, 'showDocument' => false])
                        </div>
                        {{-- No Search Results Message --}}
                        <div 
                            x-show="search.verified.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                const nameCell = row.querySelector('td:nth-child(1)');
                                const emailCell = row.querySelector('td:nth-child(3)');
                                if (!nameCell || !emailCell) return false;
                                const lastName = nameCell.textContent.trim();
                                const firstName = row.querySelector('td:nth-child(2)')?.textContent.trim() || '';
                                const fullName = `${firstName} ${lastName}`.trim();
                                const email = emailCell.textContent.trim();
                                return matchesSearch(fullName, email, 'verified') && row.offsetParent !== null;
                            }).length === 0"
                            x-cloak
                            class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-green-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No users found matching your search.</p>
                                <p class="text-sm">Try adjusting your search terms.</p>
                            </div>
                        </div>
                        <div id="verified-pagination" class="mt-4">
                            {{ $users->appends(['tab' => 'verified'])->links() }}
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'unverified'" x-cloak class="w-full">
                    {{-- Search Bar and Sort Dropdown --}}
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="relative w-96">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                x-model="search.unverified"
                                @input="sortRows('unverified')"
                                placeholder="Search by name or email..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="sort-unverified" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Sort by:
                            </label>
                            <select 
                                id="sort-unverified"
                                x-model="sort.unverified"
                                @change="sortRows('unverified')"
                                class="block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            >
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="a-z">A → Z (Name)</option>
                                <option value="z-a">Z → A (Name)</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <div id="unverified-content">
                            @include('admin.users._table', ['users' => $unverifiedUsers, 'showDocument' => false])
                        </div>
                        {{-- No Search Results Message --}}
                        <div 
                            x-show="search.unverified.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                const nameCell = row.querySelector('td:nth-child(1)');
                                const emailCell = row.querySelector('td:nth-child(3)');
                                if (!nameCell || !emailCell) return false;
                                const lastName = nameCell.textContent.trim();
                                const firstName = row.querySelector('td:nth-child(2)')?.textContent.trim() || '';
                                const fullName = `${firstName} ${lastName}`.trim();
                                const email = emailCell.textContent.trim();
                                return matchesSearch(fullName, email, 'unverified') && row.offsetParent !== null;
                            }).length === 0"
                            x-cloak
                            class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-red-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No users found matching your search.</p>
                                <p class="text-sm">Try adjusting your search terms.</p>
                            </div>
                        </div>
                        <div id="unverified-pagination" class="mt-4">
                            {{ $unverifiedUsers->appends(['tab' => 'unverified'])->links() }}
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'rejected'" x-cloak class="w-full">
                    {{-- Search Bar and Sort Dropdown --}}
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="relative w-96">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                x-model="search.rejected"
                                @input="sortRows('rejected')"
                                placeholder="Search by name or email..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="sort-rejected" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Sort by:
                            </label>
                            <select 
                                id="sort-rejected"
                                x-model="sort.rejected"
                                @change="sortRows('rejected')"
                                class="block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            >
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="a-z">A → Z (Name)</option>
                                <option value="z-a">Z → A (Name)</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <div id="rejected-content">
                            @include('admin.users._table', ['users' => $rejectedUsers, 'showDocument' => false])
                        </div>
                        {{-- No Search Results Message --}}
                        <div 
                            x-show="search.rejected.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                const nameCell = row.querySelector('td:nth-child(1)');
                                const emailCell = row.querySelector('td:nth-child(3)');
                                if (!nameCell || !emailCell) return false;
                                const lastName = nameCell.textContent.trim();
                                const firstName = row.querySelector('td:nth-child(2)')?.textContent.trim() || '';
                                const fullName = `${firstName} ${lastName}`.trim();
                                const email = emailCell.textContent.trim();
                                return matchesSearch(fullName, email, 'rejected') && row.offsetParent !== null;
                            }).length === 0"
                            x-cloak
                            class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-red-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No users found matching your search.</p>
                                <p class="text-sm">Try adjusting your search terms.</p>
                            </div>
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

                // Re-run sorting after pagination loads new data
                // Get the active tab from URL or default to pending
                const urlParams = new URLSearchParams(window.location.search);
                const activeTab = urlParams.get('tab') || 'pending';
                
                // Trigger sortRows via Alpine's reactive system
                setTimeout(() => {
                    const alpineComponent = document.querySelector('[x-data]');
                    if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                        const alpineData = alpineComponent._x_dataStack[0];
                        if (alpineData.sortRows) {
                            alpineData.sortRows(activeTab);
                        }
                    }
                }, 150);

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
