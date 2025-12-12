<x-app-layout>
    <x-slot name="header">
       <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Reports Management') }}
            </h2>
            <a href="{{ route('admin.reports.index') }}" 
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                ← Back to Reports List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Reporter Information</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Name:</span> {{ $report->reporter->fname }} {{ $report->reporter->lname }}</p>
                                <p><span class="font-medium">Email:</span> {{ $report->reporter->email }}</p>
                                <p><span class="font-medium">Role:</span> {{ $report->reporter->role_name }}</p>
                            </div>
                        </div>

                        @php
                            $currentStrikes = $report->reportedUser->strikes;
                            $isSuspended = $report->reportedUser->is_active === 0;
                        @endphp
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg border-l-4 {{ $currentStrikes >= 3 ? 'border-red-600' : ($currentStrikes >= 1 ? 'border-orange-400' : 'border-blue-500') }}">
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Reported User Information</h3>
                                <span class="px-2 py-1 text-xs font-bold rounded {{ $currentStrikes >= 3 ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-800' }}">
                                    {{ $currentStrikes }} Total Strike(s)
                                </span>
                            </div>
                            <div class="space-y-2">
                                <p><span class="font-medium">Name:</span> {{ $report->reportedUser->fname }} {{ $report->reportedUser->lname }}</p>
                                <p><span class="font-medium">Email:</span> {{ $report->reportedUser->email }}</p>
                                <p><span class="font-medium">Role:</span> {{ $report->reportedUser->role_name }}</p>
                                <p><span class="font-medium">Account Status:</span> 
                                    @if($isSuspended)
                                        <span class="text-red-600 font-bold">Suspended</span>
                                        @if($report->reportedUser->suspended_until)
                                            <span class="text-xs text-gray-500">(Until {{ \Carbon\Carbon::parse($report->reportedUser->suspended_until)->toFormattedDateString() }})</span>
                                        @endif
                                    @else
                                        <span class="text-green-600 font-bold">Active</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Report Details</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="font-medium">Reason:</p>
                                <p class="mt-1">{{ $report->reason }}</p>
                            </div>
                            <div>
                                <p class="font-medium">Report Date:</p>
                                <p class="mt-1">{{ $report->created_at->format('F j, Y g:i A') }}</p>
                            </div>
                            <div>
                                <p class="font-medium">Current Status:</p>
                                <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                      ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @php
                        // If current status is resolved, the 'strikes' count includes this one. We subtract 1 to get the 'base'.
                        // If current status is pending/rejected, the 'strikes' count does NOT include this one.
                        $baseStrikes = ($report->status === 'resolved') ? $currentStrikes - 1 : $currentStrikes;
                    @endphp

                    <div class="mt-6" x-data="{ 
                        selectedStatus: '{{ $report->status }}', 
                        baseStrikes: {{ $baseStrikes }},
                        
                        // Helper to calculate what the strikes WILL be based on dropdown selection
                        get projectedStrikes() {
                            return this.selectedStatus === 'resolved' ? this.baseStrikes + 1 : this.baseStrikes;
                        }
                    }">
                        <form action="{{ route('admin.reports.update', $report) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Update Status</label>
                                    <select name="status" id="status" x-model="selectedStatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                        <option value="pending">Pending</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>

                                <div x-show="selectedStatus === 'resolved' && projectedStrikes >= 3" 
                                     x-transition
                                     style="display: none;"
                                     class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-bold">⚠️ Automatic Suspension Trigger</p>
                                            <p class="text-sm mt-1">
                                                Resolving this report will bring the user's total strikes to 
                                                <strong x-text="projectedStrikes"></strong>.
                                            </p>
                                            <p class="text-sm mt-1 font-semibold">
                                                This action will automatically suspend the user account for 3 days.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Admin Notes</label>
                                    <textarea name="admin_notes" id="admin_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">{{ $report->admin_notes }}</textarea>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Update Report
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>