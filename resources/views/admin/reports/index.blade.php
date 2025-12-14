<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Reports Management') }}
            </h2>
            <a href="{{ route('admin.dashboard') }}"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div x-data="{ 
                search: '',
                sort: 'newest',
                matchesSearch(reporterName, reportedUserName, reason) {
                    const query = this.search.toLowerCase().trim();
                    if (!query) return true;
                    const reporterMatch = reporterName.toLowerCase().includes(query);
                    const reportedMatch = reportedUserName.toLowerCase().includes(query);
                    const reasonMatch = reason.toLowerCase().includes(query);
                    return reporterMatch || reportedMatch || reasonMatch;
                },
                sortRows() {
                    setTimeout(() => {
                        const tbody = document.querySelector('#reports-content tbody');
                        if (!tbody) return;
                        
                        const allRows = Array.from(tbody.querySelectorAll('tr'));
                        const visibleRows = allRows.filter(row => {
                            const reporterCell = row.querySelector('td:nth-child(1)');
                            const reportedUserCell = row.querySelector('td:nth-child(2)');
                            const reasonCell = row.querySelector('td:nth-child(3)');
                            if (!reporterCell || !reportedUserCell || !reasonCell) return false;
                            const reporterName = reporterCell.textContent.trim();
                            const reportedUserName = reportedUserCell.textContent.trim();
                            const reason = reasonCell.textContent.trim();
                            return this.matchesSearch(reporterName, reportedUserName, reason);
                        });
                        
                        const sortType = this.sort;
                        visibleRows.sort((a, b) => {
                            if (sortType === 'newest' || sortType === 'oldest') {
                                const aDateText = a.querySelector('td:nth-child(5)')?.textContent.trim() || '';
                                const bDateText = b.querySelector('td:nth-child(5)')?.textContent.trim() || '';
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
            x-effect="sortRows()"
            x-init="setTimeout(() => sortRows(), 200)"
            class="bg-white/20 dark:bg-gray-900/30 backdrop-blur-xl border border-white/10 dark:border-gray-700 rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6">

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
                                x-model="search"
                                @input="sortRows()"
                                placeholder="Search by reporter, reported user, or reason..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="sort-reports" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Sort by:
                            </label>
                            <select 
                                id="sort-reports"
                                x-model="sort"
                                @change="sortRows()"
                                class="block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="a-z">A → Z (Reporter Name)</option>
                                <option value="z-a">Z → A (Reporter Name)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Reports Table --}}
                    <div class="overflow-x-auto">
                        <div id="reports-content">
                            <table class="min-w-full divide-y divide-white/10">
                                <thead>
                                    <tr class="text-gray-400 dark:text-gray-300">
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Reporter
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Reported User
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Reason
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/10">
                                    @forelse($reports as $report)
                                        <tr class="bg-white/10 dark:bg-gray-800/30 hover:bg-white/20 dark:text-gray-200 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $report->reporter->fname }} {{ $report->reporter->lname }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $report->reportedUser->fname }} {{ $report->reportedUser->lname }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $report->reason }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full 
                                                {{ $report->status === 'pending'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : ($report->status === 'resolved'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $report->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-4">
                                                <a href="{{ route('admin.reports.show', $report) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    View
                                                </a>
                                                <form action="{{ route('admin.reports.destroy', $report) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                        onclick="return confirm('Are you sure you want to delete this report?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                                No reports found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- No Search Results Message --}}
                        <div 
                            x-show="search.trim() !== '' && Array.from($el.previousElementSibling.querySelectorAll('tbody tr')).filter(row => {
                                const reporterCell = row.querySelector('td:nth-child(1)');
                                const reportedUserCell = row.querySelector('td:nth-child(2)');
                                const reasonCell = row.querySelector('td:nth-child(3)');
                                if (!reporterCell || !reportedUserCell || !reasonCell) return false;
                                const reporterName = reporterCell.textContent.trim();
                                const reportedUserName = reportedUserCell.textContent.trim();
                                const reason = reasonCell.textContent.trim();
                                return matchesSearch(reporterName, reportedUserName, reason) && row.offsetParent !== null;
                            }).length === 0"
                            x-cloak
                            class="text-center py-10 text-gray-500 dark:text-gray-400 mt-4"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-indigo-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No reports found matching your search.</p>
                                <p class="text-sm">Try adjusting your search terms.</p>
                            </div>
                        </div>
                    </div>

                    <div id="reports-pagination" class="mt-4">
                        {{ $reports->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
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

                // Update reports content and pagination
                const newContent = doc.getElementById('reports-content');
                const newPagination = doc.getElementById('reports-pagination');
                
                if (newContent) {
                    const currentContent = document.getElementById('reports-content');
                    if (currentContent) {
                        currentContent.innerHTML = newContent.innerHTML;
                    }
                }
                
                if (newPagination) {
                    const currentPagination = document.getElementById('reports-pagination');
                    if (currentPagination) {
                        currentPagination.innerHTML = newPagination.innerHTML;
                    }
                }

                // Reattach pagination listeners
                attachPaginationListeners();

                // Re-run sorting after pagination loads new data
                setTimeout(() => {
                    const alpineComponent = document.querySelector('[x-data]');
                    if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                        const alpineData = alpineComponent._x_dataStack[0];
                        if (alpineData.sortRows) {
                            alpineData.sortRows();
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
