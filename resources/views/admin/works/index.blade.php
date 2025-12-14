<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Works Management') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" 
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div x-data="{ 
                tab: 'pending',
                search: {
                    pending: '',
                    approved: '',
                    rejected: ''
                },
                sort: {
                    pending: 'newest',
                    approved: 'newest',
                    rejected: 'newest'
                },
                matchesSearch(workTitle, upcyclerName, description, upcycleType, status) {
                    const query = this.search[status].toLowerCase().trim();
                    if (!query) return true;
                    const titleMatch = workTitle.toLowerCase().includes(query);
                    const upcyclerMatch = upcyclerName.toLowerCase().includes(query);
                    const descriptionMatch = description.toLowerCase().includes(query);
                    const typeMatch = upcycleType.toLowerCase().includes(query);
                    return titleMatch || upcyclerMatch || descriptionMatch || typeMatch;
                },
                sortRows(status) {
                    setTimeout(() => {
                        const activeTab = document.querySelector(`div[x-show*='${status}']`);
                        if (!activeTab || !activeTab.offsetParent) return;
                        
                        const tbody = activeTab.querySelector('tbody');
                        if (!tbody) return;
                        
                        const allRows = Array.from(tbody.querySelectorAll('tr'));
                        const visibleRows = allRows.filter(row => {
                            const workTitleCell = row.querySelector('td:nth-child(1)');
                            const upcyclerNameCell = row.querySelector('td:nth-child(2)');
                            const descriptionCell = row.querySelector('td:nth-child(3)');
                            const upcycleTypeCell = row.querySelector('td:nth-child(4)');
                            if (!workTitleCell || !upcyclerNameCell || !descriptionCell || !upcycleTypeCell) return false;
                            const workTitle = workTitleCell.textContent.trim();
                            const upcyclerName = upcyclerNameCell.textContent.trim();
                            const description = descriptionCell.textContent.trim();
                            const upcycleType = upcycleTypeCell.textContent.trim();
                            return this.matchesSearch(workTitle, upcyclerName, description, upcycleType, status);
                        });
                        
                        const sortType = this.sort[status];
                        visibleRows.sort((a, b) => {
                            if (sortType === 'newest' || sortType === 'oldest') {
                                const aDateText = a.querySelector('td:nth-child(6)')?.textContent.trim() || '';
                                const bDateText = b.querySelector('td:nth-child(6)')?.textContent.trim() || '';
                                const aDate = new Date(aDateText);
                                const bDate = new Date(bDateText);
                                return sortType === 'newest' ? bDate.getTime() - aDate.getTime() : aDate.getTime() - bDate.getTime();
                            } else if (sortType === 'a-z' || sortType === 'z-a') {
                                const aName = (a.querySelector('td:nth-child(1)')?.textContent.trim() || '').toLowerCase();
                                const bName = (b.querySelector('td:nth-child(1)')?.textContent.trim() || '').toLowerCase();
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
            class="bg-white/20 dark:bg-gray-900/30 backdrop-blur-xl shadow-lg sm:rounded-lg p-6">
                <!-- Tabs -->
                <div class="flex space-x-4 border-b dark:border-gray-700 mb-6">
                    <button @click="tab = 'pending'"
                            :class="tab === 'pending' ? 'border-b-2 border-yellow-500 text-yellow-600 dark:text-yellow-400' : 'text-gray-600 dark:text-gray-300'"
                            class="pb-2 font-semibold">
                        Pending ({{ $pendingWorks->total() }})
                    </button>
                    <button @click="tab = 'approved'"
                            :class="tab === 'approved' ? 'border-b-2 border-green-500 text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-300'"
                            class="pb-2 font-semibold">
                        Approved ({{ $approvedWorks->total() }})
                    </button>
                    <button @click="tab = 'rejected'"
                            :class="tab === 'rejected' ? 'border-b-2 border-red-500 text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-300'"
                            class="pb-2 font-semibold">
                        Rejected ({{ $rejectedWorks->total() }})
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
                                placeholder="Search by title, upcycler, description, or type..." 
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
                                <option value="a-z">A → Z (Work Title)</option>
                                <option value="z-a">Z → A (Work Title)</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <div id="pending-content">
                            @include('admin.works._table', ['works' => $pendingWorks])
                        </div>
                        {{-- No Search Results Message --}}
                        <div 
                            x-show="search.pending.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                const workTitleCell = row.querySelector('td:nth-child(1)');
                                const upcyclerNameCell = row.querySelector('td:nth-child(2)');
                                const descriptionCell = row.querySelector('td:nth-child(3)');
                                const upcycleTypeCell = row.querySelector('td:nth-child(4)');
                                if (!workTitleCell || !upcyclerNameCell || !descriptionCell || !upcycleTypeCell) return false;
                                const workTitle = workTitleCell.textContent.trim();
                                const upcyclerName = upcyclerNameCell.textContent.trim();
                                const description = descriptionCell.textContent.trim();
                                const upcycleType = upcycleTypeCell.textContent.trim();
                                return matchesSearch(workTitle, upcyclerName, description, upcycleType, 'pending') && row.offsetParent !== null;
                            }).length === 0"
                            x-cloak
                            class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-yellow-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No works found matching your search.</p>
                                <p class="text-sm">Try adjusting your search terms.</p>
                            </div>
                        </div>
                        <div id="pending-pagination" class="mt-4">
                            {{ $pendingWorks->links() }}
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'approved'" x-cloak class="w-full">
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
                                x-model="search.approved"
                                @input="sortRows('approved')"
                                placeholder="Search by title, upcycler, description, or type..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="sort-approved" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Sort by:
                            </label>
                            <select 
                                id="sort-approved"
                                x-model="sort.approved"
                                @change="sortRows('approved')"
                                class="block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            >
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="a-z">A → Z (Work Title)</option>
                                <option value="z-a">Z → A (Work Title)</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <div id="approved-content">
                            @include('admin.works._table', ['works' => $approvedWorks])
                        </div>
                        {{-- No Search Results Message --}}
                        <div 
                            x-show="search.approved.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                const workTitleCell = row.querySelector('td:nth-child(1)');
                                const upcyclerNameCell = row.querySelector('td:nth-child(2)');
                                const descriptionCell = row.querySelector('td:nth-child(3)');
                                const upcycleTypeCell = row.querySelector('td:nth-child(4)');
                                if (!workTitleCell || !upcyclerNameCell || !descriptionCell || !upcycleTypeCell) return false;
                                const workTitle = workTitleCell.textContent.trim();
                                const upcyclerName = upcyclerNameCell.textContent.trim();
                                const description = descriptionCell.textContent.trim();
                                const upcycleType = upcycleTypeCell.textContent.trim();
                                return matchesSearch(workTitle, upcyclerName, description, upcycleType, 'approved') && row.offsetParent !== null;
                            }).length === 0"
                            x-cloak
                            class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-green-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No works found matching your search.</p>
                                <p class="text-sm">Try adjusting your search terms.</p>
                            </div>
                        </div>
                        <div id="approved-pagination" class="mt-4">
                            {{ $approvedWorks->links() }}
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
                                placeholder="Search by title, upcycler, description, or type..." 
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
                                <option value="a-z">A → Z (Work Title)</option>
                                <option value="z-a">Z → A (Work Title)</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <div id="rejected-content">
                            @include('admin.works._table', ['works' => $rejectedWorks])
                        </div>
                        {{-- No Search Results Message --}}
                        <div 
                            x-show="search.rejected.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                const workTitleCell = row.querySelector('td:nth-child(1)');
                                const upcyclerNameCell = row.querySelector('td:nth-child(2)');
                                const descriptionCell = row.querySelector('td:nth-child(3)');
                                const upcycleTypeCell = row.querySelector('td:nth-child(4)');
                                if (!workTitleCell || !upcyclerNameCell || !descriptionCell || !upcycleTypeCell) return false;
                                const workTitle = workTitleCell.textContent.trim();
                                const upcyclerName = upcyclerNameCell.textContent.trim();
                                const description = descriptionCell.textContent.trim();
                                const upcycleType = upcycleTypeCell.textContent.trim();
                                return matchesSearch(workTitle, upcyclerName, description, upcycleType, 'rejected') && row.offsetParent !== null;
                            }).length === 0"
                            x-cloak
                            class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-red-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No works found matching your search.</p>
                                <p class="text-sm">Try adjusting your search terms.</p>
                            </div>
                        </div>
                        <div id="rejected-pagination" class="mt-4">
                            {{ $rejectedWorks->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        let currentTab = 'pending';

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
                const tabs = ['pending', 'approved', 'rejected'];
                
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
                setTimeout(() => {
                    const alpineComponent = document.querySelector('[x-data]');
                    if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                        const alpineData = alpineComponent._x_dataStack[0];
                        if (alpineData.sortRows) {
                            // Get current active tab
                            const activeTab = alpineData.tab || 'pending';
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
