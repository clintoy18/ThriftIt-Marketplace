<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">
            {{ __('My Appointments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 flex items-center gap-2 bg-green-100 text-green-800 px-4 py-3 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 flex items-center gap-2 bg-red-100 text-red-800 px-4 py-3 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

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
                                        <tr class="hover:bg-[#F8F4EC] dark:hover:bg-gray-700 transition">
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $appointment->appointmentid }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $appointment->upcycler->fname ?? 'N/A' }} {{ $appointment->upcycler->lname ?? '' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $appointment->appdetails }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($appointment->appdate)->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($appointment->appdate)->format('h:i A') }}</td>
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
                    </div>
                @endif
            @endforeach

            @if($appointments->count() == 0)
                <div class="text-center py-10 text-gray-500 dark:text-gray-300">
                    <p>No appointments found.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
