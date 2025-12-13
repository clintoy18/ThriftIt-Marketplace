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
            <a href="{{ url()->previous() }}"
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

    <div class="py-12" x-data="{ tab: 'pending' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-4">
                        @php
                            // ADDED 'declined' to the array below
                            $statuses = ['pending', 'approved', 'completed', 'declined', 'cancelled'];
                        @endphp
                        @foreach ($statuses as $status)
                            <button @click="tab='{{ $status }}'"
                                :class="tab === '{{ $status }}' ?
                                    'border-b-2 border-[#B59F84] text-[#B59F84] dark:text-[#F1E9D2]' :
                                    'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                                class="px-3 py-2 font-medium text-sm focus:outline-none transition-colors">
                                {{ ucfirst($status) }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                @foreach ($statuses as $status)
                    <div x-show="tab === '{{ $status }}'" class="transition duration-300">
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
                                            <tr
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
                                                        <span>{{ $appointment->user->lname }},
                                                            {{ $appointment->user->fname }}</span>
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
                        @else
                            <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-3 text-[#B59F84]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p>No {{ $status }} appointments.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</x-app-layout>
