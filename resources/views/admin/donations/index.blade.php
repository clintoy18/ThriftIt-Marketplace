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
                            {{ $status }} Rewards
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


{{-- ================= MODALS SECTION ================= --}}

    {{-- 1. PROOF IMAGE PREVIEW MODAL --}}
    <div id="proofModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeProofModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    Donation Proof
                                </h3>
                                <button onclick="closeProofModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-2 flex justify-center bg-gray-100 dark:bg-gray-900 rounded-lg p-2 border border-gray-200 dark:border-gray-700">
                                <img id="proofImage" src="" alt="Proof" class="max-h-[70vh] object-contain rounded-md shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700">
                    <a id="downloadProofLink" href="" target="_blank" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#B59F84] text-base font-medium text-white hover:bg-[#a38e73] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B59F84] sm:ml-3 sm:w-auto sm:text-sm">
                        Open Original <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeProofModal()">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. REJECTION REASON MODAL --}}
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRejectModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700">
                <form id="rejectForm" action="" method="POST">
                    @csrf
                    @method('PUT') {{-- Assuming your route uses PUT --}}
                    
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    Reject Donation Proof
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you sure you want to reject this proof? Please provide a reason for the user.
                                    </p>
                                    <div class="mt-4">
                                        <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Rejection</label>
                                        <textarea name="admin_notes" id="admin_notes" rows="3" 
                                            class="mt-1 shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md" 
                                            placeholder="e.g., Image is blurry, incorrect amount shown..." required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirm Rejection
                        </button>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeRejectModal()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPTS FOR MODALS --}}
    <script>
        // --- Proof Image Modal Logic ---
        function openProofModal(imageUrl) {
            document.getElementById('proofImage').src = imageUrl;
            document.getElementById('downloadProofLink').href = imageUrl;
            // Remove 'hidden' class to show
            document.getElementById('proofModal').classList.remove('hidden');
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        function closeProofModal() {
            document.getElementById('proofModal').classList.add('hidden');
            document.getElementById('proofImage').src = '';
            // Restore body scroll
            document.body.style.overflow = 'auto';
        }

        // --- Rejection Modal Logic ---
        function openRejectModal(id, url) {
            // Set the form action dynamically based on the donation ID
            document.getElementById('rejectForm').action = url;
            // Clear previous notes
            document.getElementById('admin_notes').value = '';
            // Show modal
            document.getElementById('rejectModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // --- Close Modals on Escape Key ---
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeProofModal();
                closeRejectModal();
            }
        });
    </script>