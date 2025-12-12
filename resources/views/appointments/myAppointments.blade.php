<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">
                {{ __('My Appointments') }}
            </h2>

            {{-- BACK BUTTON --}}
            <a href="{{ route('appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-[#B59F84] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#6B5B48] active:bg-[#6B5B48] focus:outline-none focus:border-[#6B5B48] focus:ring ring-[#B59F84] disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Request New Appointment
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        tab: 'pending',
        search: {
            pending: '',
            approved: '',
            completed: '',
            cancelled: '',
            declined: ''
        },
        sort: {
            pending: 'newest',
            approved: 'newest',
            completed: 'newest',
            cancelled: 'newest',
            declined: 'newest'
        },
        matchesSearch(upcyclerName, status) {
            const query = this.search[status].toLowerCase().trim();
            if (!query) return true;
            return upcyclerName.toLowerCase().includes(query);
        },
        sortRows(status) {
            setTimeout(() => {
                const activeTab = document.querySelector(`div[x-show*='${status}']`);
                if (!activeTab || !activeTab.offsetParent) return;
                
                const tbody = activeTab.querySelector('tbody');
                if (!tbody) return;
                
                const allRows = Array.from(tbody.querySelectorAll('tr'));
                const visibleRows = allRows.filter(row => {
                    const upcyclerCell = row.querySelector('td:nth-child(2)');
                    if (!upcyclerCell) return false;
                    const upcyclerName = upcyclerCell.textContent.trim();
                    return this.matchesSearch(upcyclerName, status);
                });
                
                const sortType = this.sort[status];
                visibleRows.sort((a, b) => {
                    if (sortType === 'newest' || sortType === 'oldest') {
                        const aDateText = a.querySelector('td:nth-child(4)')?.textContent.trim() || '';
                        const bDateText = b.querySelector('td:nth-child(4)')?.textContent.trim() || '';
                        const aDate = new Date(aDateText);
                        const bDate = new Date(bDateText);
                        return sortType === 'newest' ? bDate.getTime() - aDate.getTime() : aDate.getTime() - bDate.getTime();
                    } else if (sortType === 'a-z' || sortType === 'z-a') {
                        const aName = (a.querySelector('td:nth-child(2)')?.textContent.trim() || '').toLowerCase();
                        const bName = (b.querySelector('td:nth-child(2)')?.textContent.trim() || '').toLowerCase();
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
                
                {{-- Tabs Navigation --}}
                <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto">
                        @php
                            $statuses = ['pending', 'approved', 'completed', 'cancelled', 'declined'];
                        @endphp
                        @foreach($statuses as $status)
                            <button 
                                @click="tab='{{ $status }}'"
                                :class="tab === '{{ $status }}' ? 'border-b-2 border-[#B59F84] text-[#B59F84] dark:text-[#F1E9D2]' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                                class="px-3 py-2 font-medium text-sm focus:outline-none transition-colors capitalize whitespace-nowrap"
                            >
                                {{ ucfirst($status) }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                {{-- Tab Contents --}}
                @foreach($statuses as $status)
                    @php
                        // Filter the appointments collection for the current status
                        $appointmentsByStatus = $appointments->where('appstatus', $status);
                    @endphp

                    <div x-show="tab === '{{ $status }}'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="transition duration-300">
                        
                        {{-- Search Bar and Sort Dropdown --}}
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="relative  w-96">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    x-model="search.{{ $status }}"
                                    @input="sortRows('{{ $status }}')"
                                    placeholder="Search by upcycler name..." 
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
                                    <option value="a-z">A → Z (Upcycler Name)</option>
                                    <option value="z-a">Z → A (Upcycler Name)</option>
                                </select>
                            </div>
                          

                        </div>
                        
                        @if($appointmentsByStatus->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <thead class="bg-[#F4F2ED] dark:bg-gray-900">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Upcycler</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Details</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($appointmentsByStatus as $appointment)
                                            @php
                                                $upcyclerName = ($appointment->upcycler->fname ?? 'N/A') . ' ' . ($appointment->upcycler->lname ?? '');
                                            @endphp
                                            <tr 
                                                x-show="matchesSearch('{{ addslashes(trim($upcyclerName)) }}', '{{ $status }}')"
                                                class="hover:bg-[#F8F4EC] dark:hover:bg-gray-700 transition"
                                            >
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $appointment->appointmentid }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $upcyclerName }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $appointment->appdetails }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($appointment->appdate)->format('M d, Y') }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"> {{ \Carbon\Carbon::parse($appointment->app_time)->format('h:i A') }}</td>
                                                <td class="px-6 py-4 text-sm">
                                                    @php
                                                        $badgeClasses = [
                                                            'pending' => 'bg-[#F1E9D2] text-[#8A7560] dark:bg-[#8A7560] dark:text-[#F1E9D2]',
                                                            'approved' => 'bg-[#B59F84] text-white dark:bg-[#9C8770] dark:text-[#F1E9D2]',
                                                            'completed' => 'bg-[#F4F2ED] text-[#8A7560] dark:bg-[#7A664D] dark:text-[#F1E9D2]',
                                                            'cancelled' => 'bg-[#F5D6C6] text-[#8A4B2D] dark:bg-[#8A4B2D] dark:text-[#F1E9D2]',
                                                            'declined' => 'bg-[#F5D6C6] text-[#8A4B2D] dark:bg-[#8A4B2D] dark:text-[#F1E9D2]',
                                                        ][$appointment->appstatus] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClasses }}">
                                                        {{ ucfirst($appointment->appstatus) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm">
                                                    <div class="flex items-center gap-2">
                                                        <a href="{{ route('appointments.show', $appointment) }}" class="px-3 py-1.5 bg-[#B59F84] text-white rounded-lg hover:bg-[#9C8770] transition text-xs">
                                                            View
                                                        </a>
                                                        @if($appointment->appstatus == 'pending')
                                                            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="px-3 py-1.5 bg-[#8A7560] text-white rounded-lg hover:bg-[#6B5B48] transition text-xs">
                                                                    Cancel
                                                                </button>
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
                                    const upcyclerCell = row.querySelector('td:nth-child(2)');
                                    if (!upcyclerCell) return false;
                                    const upcyclerName = upcyclerCell.textContent.trim();
                                    return matchesSearch(upcyclerName, '{{ $status }}') && row.offsetParent !== null;
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
                                    <svg class="w-12 h-12 mb-3 text-[#B59F84] opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
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