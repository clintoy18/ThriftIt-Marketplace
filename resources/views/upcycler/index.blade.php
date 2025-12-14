<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">

            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>

                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('Upcycler Dashboard') }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Manage your client appointments and consultations
                    </p>
                </div>
            </div>

            <!-- Back Button -->
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-[#B59F84] border border-transparent rounded-md 
    font-semibold text-xs text-white uppercase tracking-widest 
    hover:bg-[#6B5B48] active:bg-[#6B5B48] 
    focus:outline-none focus:border-[#6B5B48] focus:ring ring-[#B59F84] 
    transition ease-in-out duration-150">

                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>

                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        tab: 'pending',
        search: {
            pending: '',
            approved: '',
            completed: '',
            declined: '',
            cancelled: ''
        },
        sort: {
            pending: 'newest',
            approved: 'newest',
            completed: 'newest',
            cancelled: 'newest',
            declined: 'newest'
        },
        matchesSearch(userName, status) {
            const query = this.search[status].toLowerCase().trim();
            if (!query) return true;
            return userName.toLowerCase().includes(query);
        },
        sortRows(status) {
            setTimeout(() => {
                const activeTab = document.querySelector(`div[x-show*='${status}']`);
                if (!activeTab || !activeTab.offsetParent) return;
                
                const tbody = activeTab.querySelector('tbody');
                if (!tbody) return;
                
                const allRows = Array.from(tbody.querySelectorAll('tr'));
                const visibleRows = allRows.filter(row => {
                    const userCell = row.querySelector('td:nth-child(1)');
                    if (!userCell) return false;
                    const userName = userCell.textContent.trim();
                    return this.matchesSearch(userName, status);
                });
                
                const sortType = this.sort[status];
                visibleRows.sort((a, b) => {
                    if (sortType === 'newest' || sortType === 'oldest') {
                        const aDateText = a.querySelector('td:nth-child(3)')?.textContent.trim() || '';
                        const bDateText = b.querySelector('td:nth-child(3)')?.textContent.trim() || '';
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
    x-init="setTimeout(() => sortRows(tab), 200)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto">
                        @php
                            // ADDED 'declined' to the array below
                            $statuses = ['pending', 'approved', 'completed', 'declined', 'cancelled'];
                        @endphp
                        @foreach ($statuses as $status)
                            <button @click="tab='{{ $status }}'"
                                :class="tab === '{{ $status }}' ?
                                    'border-b-2 border-[#B59F84] text-[#B59F84] dark:text-[#F1E9D2]' :
                                    'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                                class="px-3 py-2 font-medium text-sm focus:outline-none transition-colors capitalize whitespace-nowrap">
                                {{ ucfirst($status) }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                @foreach ($statuses as $status)
                    <div x-show="tab === '{{ $status }}'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="transition duration-300">
                        
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
                                    x-model="search.{{ $status }}"
                                    @input="sortRows('{{ $status }}')"
                                    placeholder="Search by user name..." 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-[#B59F84] focus:border-[#B59F84] sm:text-sm"
                                >
                            </div>
                          
                            <div class="flex items-center gap-2">
                                <label for="sort-{{ $status }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    Sort by:
                                </label>
                                <select 
                                    id="sort-{{ $status }}"
                                    x-model="sort.{{ $status }}"
                                    @change="sortRows('{{ $status }}')"
                                    class="block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-[#B59F84] focus:border-[#B59F84] sm:text-sm"
                                >
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="a-z">A → Z (User Name)</option>
                                    <option value="z-a">Z → A (User Name)</option>
                                </select>
                            </div>
                        </div>

                        @if (isset($appointments[$status]) && $appointments[$status]->count() > 0)
                            <div class="overflow-x-auto mb-6">
                                <table
                                    class="min-w-full text-left text-sm text-gray-700 dark:text-gray-200 border border-[#E9DFC7] dark:border-gray-700 rounded-lg">
                                    <thead class="bg-[#F8F4EC] dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">
                                                User</th>
                                            <th
                                                class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">
                                                Type</th>
                                            <th
                                                class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">
                                                Date</th>

                                            <th
                                                class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($appointments[$status] as $appointment)
                                            @php
                                                $userName = $appointment->user->lname . ', ' . $appointment->user->fname;
                                            @endphp
                                            <tr
                                                x-show="matchesSearch('{{ addslashes(trim($userName)) }}', '{{ $status }}')"
                                                class="bg-white dark:bg-gray-800 hover:bg-[#F8F4EC] dark:hover:bg-gray-700 transition-colors duration-150">

                                                {{-- USER COLUMN --}}
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">
                                                    <div class="flex items-center space-x-3">
                                                        <div
                                                            class="w-8 h-8 bg-[#F1E9D2] dark:bg-[#9C8770] rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-[#B59F84] dark:text-[#F1E9D2]"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                        <span>{{ $userName }}</span>
                                                    </div>
                                                </td>

                                                {{-- TYPE COLUMN --}}
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">
                                                    {{ $appointment->apptype }}</td>

                                                {{-- DATE COLUMN --}}
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">
                                                    {{ \Carbon\Carbon::parse($appointment->appdate)->setTimezone('Asia/Manila')->format('F j, Y') }}
                                                    {{ \Carbon\Carbon::parse($appointment->app_time)->format('h:i A') }}
                                                </td>

                                                {{-- STATUS COLUMN --}}
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize 
                                                        @if ($appointment->appstatus === 'confirmed' || $appointment->appstatus === 'approved') bg-[#F8F4EC] text-[#B59F84] dark:bg-[#9C8770] dark:text-[#F1E9D2]
                                                        @elseif($appointment->appstatus === 'pending') bg-[#F1E9D2] text-[#8A7560] dark:bg-[#8A7560] dark:text-[#F1E9D2]
                                                        @elseif($appointment->appstatus === 'declined') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                                                        @else bg-[#F4F2ED] text-[#8A7560] dark:bg-gray-700 dark:text-gray-200 @endif">
                                                        {{ $appointment->appstatus }}
                                                    </span>
                                                </td>

                                                {{-- ACTIONS COLUMN (Updated Logic) --}}
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">
                                                    <div class="flex items-center space-x-3">

                                                        {{-- View is ALWAYS visible --}}
                                                        <a href="{{ route('upcycler.show', $appointment) }}"
                                                            class="text-[#B59F84] hover:text-[#8A7560] dark:text-[#D5C39A] dark:hover:text-[#F1E9D2]">View</a>

                                                        {{-- Delete is ONLY visible if status is PENDING --}}
                                                        @if (strtolower($appointment->appstatus) === 'pending')
                                                            <form
                                                                action="{{ route('upcycler.destroy', $appointment) }}"
                                                                method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    onclick="return confirm('Are you sure you want to delete this appointment?')"
                                                                    class="text-[#8A7560] hover:text-[#6B5B48] dark:text-[#8A7560] dark:hover:text-[#6B5B48]">Delete</button>
                                                            </form>
                                                        @endif

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- No Search Results Message --}}
                            <div 
                                x-show="search.{{ $status }}.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                    const userCell = row.querySelector('td:nth-child(1)');
                                    if (!userCell) return false;
                                    const userName = userCell.textContent.trim();
                                    return matchesSearch(userName, '{{ $status }}') && row.offsetParent !== null;
                                }).length === 0"
                                x-cloak
                                class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                            >
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-3 text-[#B59F84] opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No appointments found matching your search.</p>
                                    <p class="text-sm">Try adjusting your search terms.</p>
                                </div>
                            </div>
                        @else
                            {{-- Empty State Message --}}
                            <div class="text-center py-10 text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-3 text-[#B59F84] opacity-50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="text-lg font-medium">No {{ $status }} appointments found.</p>
                                    <p class="text-sm">Any appointments with this status will appear here.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</x-app-layout>
