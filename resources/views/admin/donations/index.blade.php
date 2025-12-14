<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Donations Management') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Glassmorphism Container --}}
            <div x-data="{ 
                currentTab: 'approval',
                search: {
                    approvalPending: '',
                    approvalApproved: '',
                    approvalRejected: '',
                    rewardPending: '',
                    rewardVerified: '',
                    rewardRejected: ''
                },
                sort: {
                    approvalPending: 'newest',
                    approvalApproved: 'newest',
                    approvalRejected: 'newest',
                    rewardPending: 'newest',
                    rewardVerified: 'newest',
                    rewardRejected: 'newest'
                },
                matchesSearch(donationName, donorName, categoryName, searchKey) {
                    const query = this.search[searchKey].toLowerCase().trim();
                    if (!query) return true;
                    const nameMatch = donationName.toLowerCase().includes(query);
                    const donorMatch = donorName.toLowerCase().includes(query);
                    const categoryMatch = categoryName.toLowerCase().includes(query);
                    return nameMatch || donorMatch || categoryMatch;
                },
                sortRows(section, status) {
                    setTimeout(() => {
                        const statusKey = `${section}${status.charAt(0).toUpperCase() + status.slice(1)}`;
                        const searchKey = `${section}${status.charAt(0).toUpperCase() + status.slice(1)}`;
                        const sortKey = `${section}${status.charAt(0).toUpperCase() + status.slice(1)}`;
                        
                        const sectionId = section === 'approval' ? 'approval-section' : 'reward-section';
                        const activeSection = document.getElementById(sectionId);
                        if (!activeSection || activeSection.classList.contains('hidden')) return;
                        
                        const statusLower = status.toLowerCase();
                        const contentId = `${section}-${statusLower}-content`;
                        const contentDiv = document.getElementById(contentId);
                        if (!contentDiv) return;
                        
                        const tbody = contentDiv.querySelector('tbody');
                        if (!tbody) return;
                        
                        // Check if it's reward table (has Proof column)
                        const firstRow = tbody.querySelector('tr');
                        const hasProof = firstRow && firstRow.querySelectorAll('td').length > 6;
                        const dateColumnIndex = hasProof ? 6 : 5;
                        
                        const allRows = Array.from(tbody.querySelectorAll('tr'));
                        const visibleRows = allRows.filter(row => {
                            const donationNameCell = row.querySelector('td:nth-child(1)');
                            const donorNameCell = row.querySelector('td:nth-child(2)');
                            const categoryCell = row.querySelector('td:nth-child(3)');
                            if (!donationNameCell || !donorNameCell || !categoryCell) return false;
                            const donationName = donationNameCell.textContent.trim();
                            const donorName = donorNameCell.textContent.trim();
                            const categoryName = categoryCell.textContent.trim();
                            return this.matchesSearch(donationName, donorName, categoryName, searchKey);
                        });
                        
                        const sortType = this.sort[sortKey];
                        visibleRows.sort((a, b) => {
                            if (sortType === 'newest' || sortType === 'oldest') {
                                const aDateText = a.querySelector(`td:nth-child(${dateColumnIndex})`)?.textContent.trim() || '';
                                const bDateText = b.querySelector(`td:nth-child(${dateColumnIndex})`)?.textContent.trim() || '';
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
            x-effect="if (currentTab) { 
                if (currentTab === 'approval') {
                    sortRows('approval', 'pending');
                    sortRows('approval', 'approved');
                    sortRows('approval', 'rejected');
                } else {
                    sortRows('reward', 'pending');
                    sortRows('reward', 'verified');
                    sortRows('reward', 'rejected');
                }
            }"
            x-init="setTimeout(() => {
                sortRows('approval', 'pending');
                sortRows('approval', 'approved');
                sortRows('approval', 'rejected');
            }, 200)"
            class="bg-white/20 dark:bg-gray-900/30 backdrop-blur-xl shadow-xl sm:rounded-xl p-6">

                {{-- Tabs --}}
                <div class="flex space-x-6 border-b border-gray-300 dark:border-gray-700 mb-6">
                    <button id="tab-approval"
                        class="pb-2 font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-[#B59F84]"
                        onclick="switchTab('approval')">
                        Approval Management
                    </button>
                    <button id="tab-reward"
                        class="pb-2 font-semibold text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700 dark:hover:text-gray-300"
                        onclick="switchTab('reward')">
                        Reward Management
                    </button>
                </div>

                {{-- Approval Management --}}
                <div id="approval-section">
                    @foreach (['Pending' => $pendingDonations, 'Approved' => $approvedDonations, 'Rejected' => $rejectedDonations] as $status => $donations)
                        @php
                            $statusLower = strtolower($status);
                            $searchKey = 'approval' . $status;
                            $sortKey = 'approval' . $status;
                        @endphp
                        <h3
                            class="text-lg font-semibold
                            {{ $status === 'Pending' ? 'text-yellow-700 dark:text-yellow-300' : '' }}
                            {{ $status === 'Approved' ? 'text-green-700 dark:text-green-300' : '' }}
                            {{ $status === 'Rejected' ? 'text-red-700 dark:text-red-300' : '' }}
                            mb-4 mt-6">
                            {{ $status }} Donations
                        </h3>
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
                                    x-model="search.approval{{ $status }}"
                                    @input="sortRows('approval', '{{ $statusLower }}')"
                                    placeholder="Search by donation, donor, or category..." 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 {{ $status === 'Pending' ? 'focus:ring-yellow-500 focus:border-yellow-500' : ($status === 'Approved' ? 'focus:ring-green-500 focus:border-green-500' : 'focus:ring-red-500 focus:border-red-500') }} sm:text-sm"
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <label for="sort-approval-{{ $statusLower }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    Sort by:
                                </label>
                                <select 
                                    id="sort-approval-{{ $statusLower }}"
                                    x-model="sort.approval{{ $status }}"
                                    @change="sortRows('approval', '{{ $statusLower }}')"
                                    class="block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 {{ $status === 'Pending' ? 'focus:ring-yellow-500 focus:border-yellow-500' : ($status === 'Approved' ? 'focus:ring-green-500 focus:border-green-500' : 'focus:ring-red-500 focus:border-red-500') }} sm:text-sm"
                                >
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="a-z">A → Z (Donation Name)</option>
                                    <option value="z-a">Z → A (Donation Name)</option>
                                </select>
                            </div>
                        </div>
                        <div class="overflow-x-auto bg-white/30 dark:bg-gray-800/50 rounded-xl p-3 shadow-inner mb-6">
                            <div id="approval-{{ $statusLower }}-content">
                                @include('admin.donations._table', ['donations' => $donations])
                            </div>
                            {{-- No Search Results Message --}}
                            <div 
                                x-show="search.approval{{ $status }}.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                    const donationNameCell = row.querySelector('td:nth-child(1)');
                                    const donorNameCell = row.querySelector('td:nth-child(2)');
                                    const categoryCell = row.querySelector('td:nth-child(3)');
                                    if (!donationNameCell || !donorNameCell || !categoryCell) return false;
                                    const donationName = donationNameCell.textContent.trim();
                                    const donorName = donorNameCell.textContent.trim();
                                    const categoryName = categoryCell.textContent.trim();
                                    return matchesSearch(donationName, donorName, categoryName, 'approval{{ $status }}') && row.offsetParent !== null;
                                }).length === 0"
                                x-cloak
                                class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                            >
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-3 {{ $status === 'Pending' ? 'text-yellow-500' : ($status === 'Approved' ? 'text-green-500' : 'text-red-500') }} opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No donations found matching your search.</p>
                                    <p class="text-sm">Try adjusting your search terms.</p>
                                </div>
                            </div>
                            <div id="approval-{{ $statusLower }}-pagination" class="mt-4">
                                {{ $donations->links() }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Reward Management --}}
                <div id="reward-section" class="hidden">
                    @foreach (['Pending' => $pendingVerifications, 'Verified' => $verifiedDonations, 'Rejected' => $rejectedProofs] as $status => $donations)
                        @php
                            $statusLower = strtolower($status);
                            $searchKey = 'reward' . $status;
                            $sortKey = 'reward' . $status;
                        @endphp
                        <h3
                            class="text-lg font-semibold
                            {{ $status === 'Pending' ? 'text-yellow-700 dark:text-yellow-300' : '' }}
                            {{ $status === 'Verified' ? 'text-green-700 dark:text-green-300' : '' }}
                            {{ $status === 'Rejected' ? 'text-red-700 dark:text-red-300' : '' }}
                            mb-4 mt-6">
                            {{ $status }} Donations
                        </h3>
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
                                    x-model="search.reward{{ $status }}"
                                    @input="sortRows('reward', '{{ $statusLower }}')"
                                    placeholder="Search by donation, donor, or category..." 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 {{ $status === 'Pending' ? 'focus:ring-yellow-500 focus:border-yellow-500' : ($status === 'Verified' ? 'focus:ring-green-500 focus:border-green-500' : 'focus:ring-red-500 focus:border-red-500') }} sm:text-sm"
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <label for="sort-reward-{{ $statusLower }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    Sort by:
                                </label>
                                <select 
                                    id="sort-reward-{{ $statusLower }}"
                                    x-model="sort.reward{{ $status }}"
                                    @change="sortRows('reward', '{{ $statusLower }}')"
                                    class="block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 {{ $status === 'Pending' ? 'focus:ring-yellow-500 focus:border-yellow-500' : ($status === 'Verified' ? 'focus:ring-green-500 focus:border-green-500' : 'focus:ring-red-500 focus:border-red-500') }} sm:text-sm"
                                >
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="a-z">A → Z (Donation Name)</option>
                                    <option value="z-a">Z → A (Donation Name)</option>
                                </select>
                            </div>
                        </div>
                        <div class="overflow-x-auto bg-white/30 dark:bg-gray-800/50 rounded-xl p-3 shadow-inner mb-6">
                            <div id="reward-{{ $statusLower }}-content">
                                @include('admin.donations.reward-management._reward_table', [
                                    'donations' => $donations,
                                    'type' => $statusLower,
                                ])
                            </div>
                            {{-- No Search Results Message --}}
                            <div 
                                x-show="search.reward{{ $status }}.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                    const donationNameCell = row.querySelector('td:nth-child(1)');
                                    const donorNameCell = row.querySelector('td:nth-child(2)');
                                    const categoryCell = row.querySelector('td:nth-child(3)');
                                    if (!donationNameCell || !donorNameCell || !categoryCell) return false;
                                    const donationName = donationNameCell.textContent.trim();
                                    const donorName = donorNameCell.textContent.trim();
                                    const categoryName = categoryCell.textContent.trim();
                                    return matchesSearch(donationName, donorName, categoryName, 'reward{{ $status }}') && row.offsetParent !== null;
                                }).length === 0"
                                x-cloak
                                class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                            >
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-3 {{ $status === 'Pending' ? 'text-yellow-500' : ($status === 'Verified' ? 'text-green-500' : 'text-red-500') }} opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No donations found matching your search.</p>
                                    <p class="text-sm">Try adjusting your search terms.</p>
                                </div>
                            </div>
                            <div id="reward-{{ $statusLower }}-pagination" class="mt-4">
                                {{ $donations->links() }}
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    {{-- Tab Switch & AJAX Pagination Script --}}
    <script>
        let currentTab = 'approval';

        function switchTab(tab) {
            currentTab = tab;
            const approvalSection = document.getElementById('approval-section');
            const rewardSection = document.getElementById('reward-section');

            const tabApproval = document.getElementById('tab-approval');
            const tabReward = document.getElementById('tab-reward');

            // Update Alpine.js currentTab
            const alpineComponent = document.querySelector('[x-data]');
            if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                alpineComponent._x_dataStack[0].currentTab = tab;
            }

            if (tab === 'approval') {
                // Show Approval, hide Reward
                approvalSection.classList.remove('hidden');
                rewardSection.classList.add('hidden');

                // Update tab styles
                tabApproval.classList.add('border-[#B59F84]', 'text-gray-700', 'dark:text-gray-300');
                tabApproval.classList.remove('text-gray-500', 'dark:text-gray-400');

                tabReward.classList.remove('border-[#B59F84]', 'text-gray-700', 'dark:text-gray-300');
                tabReward.classList.add('text-gray-500', 'dark:text-gray-400');

            } else {
                // Show Reward, hide Approval
                approvalSection.classList.add('hidden');
                rewardSection.classList.remove('hidden');

                // Update tab styles
                tabReward.classList.add('border-[#B59F84]', 'text-gray-700', 'dark:text-gray-300');
                tabReward.classList.remove('text-gray-500', 'dark:text-gray-400');

                tabApproval.classList.remove('border-[#B59F84]', 'text-gray-700', 'dark:text-gray-300');
                tabApproval.classList.add('text-gray-500', 'dark:text-gray-400');
            }
        }

        // AJAX Pagination Handler
        document.addEventListener('DOMContentLoaded', function() {
            attachPaginationListeners();
        });

        function attachPaginationListeners() {
            // Get all pagination links
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

                // Determine which section is active and update
                if (currentTab === 'approval') {
                    const statuses = ['pending', 'approved', 'rejected'];
                    statuses.forEach(status => {
                        const newContent = doc.getElementById(`approval-${status}-content`);
                        const newPagination = doc.getElementById(`approval-${status}-pagination`);
                        
                        if (newContent) {
                            const currentContent = document.getElementById(`approval-${status}-content`);
                            if (currentContent) currentContent.innerHTML = newContent.innerHTML;
                        }
                        
                        if (newPagination) {
                            const currentPagination = document.getElementById(`approval-${status}-pagination`);
                            if (currentPagination) currentPagination.innerHTML = newPagination.innerHTML;
                        }
                    });
                } else {
                    const statuses = ['pending', 'verified', 'rejected'];
                    statuses.forEach(status => {
                        const newContent = doc.getElementById(`reward-${status}-content`);
                        const newPagination = doc.getElementById(`reward-${status}-pagination`);
                        
                        if (newContent) {
                            const currentContent = document.getElementById(`reward-${status}-content`);
                            if (currentContent) currentContent.innerHTML = newContent.innerHTML;
                        }
                        
                        if (newPagination) {
                            const currentPagination = document.getElementById(`reward-${status}-pagination`);
                            if (currentPagination) currentPagination.innerHTML = newPagination.innerHTML;
                        }
                    });
                }

                // Reattach pagination listeners for new links
                attachPaginationListeners();

                // Re-run sorting after pagination loads new data
                setTimeout(() => {
                    const alpineComponent = document.querySelector('[x-data]');
                    if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                        const alpineData = alpineComponent._x_dataStack[0];
                        if (alpineData.sortRows) {
                            if (currentTab === 'approval') {
                                alpineData.sortRows('approval', 'pending');
                                alpineData.sortRows('approval', 'approved');
                                alpineData.sortRows('approval', 'rejected');
                            } else {
                                alpineData.sortRows('reward', 'pending');
                                alpineData.sortRows('reward', 'verified');
                                alpineData.sortRows('reward', 'rejected');
                            }
                        }
                    }
                }, 150);

                // Scroll to top of tables
                document.querySelector('.bg-white\\/20')?.scrollIntoView({ behavior: 'smooth', block: 'start' });

            } catch (error) {
                console.error('Error loading paginated data:', error);
                alert('Error loading data. Please try again.');
            }
        }
    </script>
</x-app-layout>
