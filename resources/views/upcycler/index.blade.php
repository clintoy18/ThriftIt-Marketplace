<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <!-- Calendar Icon -->
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('My Appointments') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Manage your client appointments and consultations
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                @php
                    $statuses = ['pending', 'approved', 'completed', 'cancelled', 'declined'];
                @endphp

                @foreach($statuses as $status)
                    @php
                        $appointmentsByStatus = $appointments->where('appstatus', $status);
                    @endphp

                    @if($appointmentsByStatus->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4 capitalize">
                                {{ $status }} Appointments
                            </h3>

                            <div class="overflow-x-auto">
                                <table class="min-w-full text-left border border-[#E9DFC7] dark:border-gray-700 rounded-lg">
                                    <thead class="bg-[#F8F4EC] dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">ID</th>
                                            <th class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">Upcycler</th>
                                            <th class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">Details</th>
                                            <th class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">Date</th>
                                            <th class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">Time</th>
                                            <th class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">Status</th>
                                            <th class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600 font-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appointmentsByStatus as $appointment)
                                            <tr class="bg-white dark:bg-gray-800 hover:bg-[#F8F4EC] dark:hover:bg-gray-700 transition-colors duration-150">
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">{{ $appointment->appointmentid }}</td>
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">{{ $appointment->upcycler->fname ?? 'N/A' }} {{ $appointment->upcycler->lname ?? '' }}</td>
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">{{ $appointment->appdetails }}</td>
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">{{ \Carbon\Carbon::parse($appointment->appdate)->format('M d, Y') }}</td>
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">{{ \Carbon\Carbon::parse($appointment->appdate)->format('h:i A') }}</td>
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">
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
                                                <td class="px-4 py-3 border-b border-[#E9DFC7] dark:border-gray-600">
                                                    <div class="flex items-center space-x-3">
                                                        <a href="{{ route('appointments.show', $appointment) }}" class="text-[#B59F84] hover:text-[#8A7560] dark:text-[#D5C39A] dark:hover:text-[#F1E9D2]">View</a>
                                                        @if($appointment->appstatus === 'pending')
                                                            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="text-[#8A7560] hover:text-[#6B5B48] dark:text-[#8A7560] dark:hover:text-[#6B5B48]">Cancel</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p>No {{ $status }} appointments.</p>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
        </div>
    </div>
</x-app-layout>
