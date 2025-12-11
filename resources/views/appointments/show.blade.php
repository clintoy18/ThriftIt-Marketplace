<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-[#F8F4EC] dark:bg-gray-700 rounded-lg">
                <svg class="w-6 h-6 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Appointment Details') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
                
                <div class="p-8">
                    
                    {{-- Note Section --}}
                    <div class="mb-8">
                        <h3 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-bold mb-2">Request Information</h3>
                        <div class="bg-[#F8F4EC] dark:bg-gray-700 rounded-xl p-6 border border-[#E9DFC7] dark:border-gray-600">
                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-[#B59F84] mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <span class="block text-sm font-semibold text-gray-500 dark:text-gray-400">Appointment Note</span>
                                    <p class="text-lg text-gray-800 dark:text-gray-100 leading-relaxed mt-1">
                                        {{ $appointment->appdetails }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Details Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        
                        <div class="space-y-6">
                            {{-- Contact Number --}}
                            <div class="flex items-center space-x-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <div class="p-3 bg-white dark:bg-gray-800 rounded-full shadow-sm text-[#B59F84]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Contact Number</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $appointment->contactnumber }}</p>
                                </div>
                            </div>

                            {{-- Scheduled Time --}}
                            <div class="flex items-center space-x-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <div class="p-3 bg-white dark:bg-gray-800 rounded-full shadow-sm text-[#B59F84]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Scheduled Time</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $appointment->created_at->format('M d, Y • h:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Upcycler Info --}}
                        <div>
                            <div class="h-full p-5 border border-[#E9DFC7] dark:border-gray-600 rounded-xl relative overflow-hidden">
                                <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-[#F8F4EC] dark:bg-gray-600 rounded-full opacity-50"></div>
                                
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Assigned Upcycler</p>
                                
                                @if($appointment->upcycler)
                                    <div class="flex items-center space-x-4">
                                        <div class="h-12 w-12 rounded-full bg-[#B59F84] text-white flex items-center justify-center text-lg font-bold">
                                            {{ substr($appointment->upcycler->fname, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                                {{ $appointment->upcycler->fname }} {{ $appointment->upcycler->lname }}
                                            </p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Available
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-3 text-red-500 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-800">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <span class="font-medium text-sm">Upcycler information unavailable</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS SECTION --}}
                    {{-- 
                        LOGIC: Check if status is locked. 
                        If status is 'approved', 'declined', or 'cancelled', $isLocked becomes true.
                    --}}
                    @php
                        $isLocked = in_array(strtolower($appointment->status), ['approved', 'declined', 'cancelled']);
                    @endphp

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-100 dark:border-gray-700 mt-6 gap-4">
                        
                        {{-- EDIT BUTTON --}}
                        <a href="{{ $isLocked ? '#' : route('appointments.edit', $appointment->appointmentid) }}" 
                           class="inline-flex justify-center items-center px-6 py-3 text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm
                           {{ $isLocked 
                                ? 'bg-gray-300 text-gray-500 cursor-not-allowed pointer-events-none dark:bg-gray-600 dark:text-gray-400' 
                                : 'bg-[#B59F84] hover:bg-[#9C8770] text-white' 
                           }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            Edit Appointment
                        </a>

                        {{-- CANCEL BUTTON --}}
                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                @if($isLocked) disabled @endif
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border text-sm font-medium rounded-lg transition-colors duration-200
                                {{ $isLocked 
                                    ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-500' 
                                    : 'border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20' 
                                }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel Appointment
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>