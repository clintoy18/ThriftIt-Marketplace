<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
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

            {{-- Status Badge --}}
            @php
                $statusColors = [
                    'pending'   => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'approved'  => 'bg-green-100 text-green-800 border-green-200',
                    'completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'declined'  => 'bg-red-100 text-red-800 border-red-200',
                    'rejected'  => 'bg-red-100 text-red-800 border-red-200',
                    'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                ];
                $statusColor = $statusColors[strtolower($appointment->appstatus)] ?? 'bg-gray-100 text-gray-800 border-gray-200';
            @endphp
            <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border {{ $statusColor }}">
                {{ $appointment->appstatus }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                
                <div class="p-8">
                    
                    {{-- Top Section: Upcycler Card --}}
                    <div class="mb-10">
                        <h3 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-bold mb-3">Appointment With</h3>
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl p-5 shadow-sm flex items-center justify-between">
                            @if($appointment->upcycler)
                                <div class="flex items-center space-x-4">
                                    <div class="h-14 w-14 rounded-full bg-[#B59F84] text-white flex items-center justify-center text-xl font-bold shadow-md">
                                        {{ substr($appointment->upcycler->fname, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ $appointment->upcycler->fname }} {{ $appointment->upcycler->lname }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Professional Upcycler</p>
                                    </div>
                                </div>
                                <a href="{{ route('upcycler', $appointment->upcycler->id) }}" class="text-sm text-[#B59F84] font-semibold hover:underline">
                                    View Profile &rarr;
                                </a>
                            @else
                                <div class="flex items-center space-x-3 text-red-500">
                                    <span class="font-medium">Upcycler information unavailable</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Details Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Date & Time</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ \Carbon\Carbon::parse($appointment->appdate)->format('M d, Y') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 pl-6">
                                {{ \Carbon\Carbon::parse($appointment->app_time)->format('h:i A') }}
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Service Type</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"></path></svg>
                                {{ $appointment->apptype ?? 'Custom Service' }}
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Contact Number</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $appointment->contactnumber }}
                            </p>
                        </div>
                    </div>

                    {{-- Main Content: Notes & Images --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                        {{-- Left Col: Notes --}}
                        <div class="lg:col-span-2 space-y-2">
                            <h3 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-bold">Instructions & Notes</h3>
                            <div class="bg-[#F8F4EC] dark:bg-gray-700/50 rounded-xl p-6 border border-[#E9DFC7] dark:border-gray-600 min-h-[160px]">
                                <p class="text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-wrap">{{ $appointment->appdetails }}</p>
                            </div>
                        </div>

                        {{-- Right Col: Reference Photos --}}
                        <div class="space-y-2">
                            <h3 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-bold flex items-center justify-between">
                                Reference Photos
                                <span class="text-[10px] bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full text-gray-500">{{ $appointment->apptImages->count() }}</span>
                            </h3>
                            
                            @if($appointment->apptImages && $appointment->apptImages->count() > 0)
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($appointment->apptImages->take(4) as $img)
                                        <div class="group relative aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-200 cursor-pointer hover:shadow-md transition">
                                            <img src="{{ Storage::disk('s3')->url($img->image_path) }}" 
                                                 alt="Reference" 
                                                 class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500"
                                                 onclick="window.open(this.src, '_blank')">
                                        </div>
                                    @endforeach
                                </div>
                                @if($appointment->apptImages->count() > 4)
                                    <p class="text-xs text-center text-gray-500 mt-2">+{{ $appointment->apptImages->count() - 4 }} more photos</p>
                                @endif
                            @else
                                <div class="h-40 bg-gray-50 dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-sm">No photos attached</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ACTION BUTTONS SECTION --}}
                    @php
                        // Check if modification is locked
                        $isLocked = in_array(strtolower($appointment->appstatus), ['approved', 'declined', 'cancelled']);
                    @endphp

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end pt-6 border-t border-gray-100 dark:border-gray-700 gap-4">
                        
                        <a href="{{ route('appointments.myAppointments') }}" class="px-6 py-3 text-sm font-medium text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition">
                            Back to List
                        </a>

                        {{-- EDIT BUTTON --}}
                        <a href="{{ $isLocked ? '#' : route('appointments.edit', $appointment->appointmentid) }}" 
                           class="inline-flex justify-center items-center px-6 py-3 text-sm font-medium rounded-xl transition-all duration-200 shadow-sm
                           {{ $isLocked 
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed pointer-events-none border border-gray-200' 
                                : 'bg-white border border-[#B59F84] text-[#B59F84] hover:bg-[#B59F84] hover:text-white' 
                           }}">
                            Edit Request
                        </a>

                        {{-- CANCEL BUTTON --}}
                        {{-- <form method="PAT" action="{{ route('appointments.cancel', $appointment->appointmentid) }}" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                @if($isLocked) disabled @endif
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border text-sm font-medium rounded-xl transition-all duration-200
                                {{ $isLocked 
                                    ? 'border-transparent bg-gray-100 text-gray-400 cursor-not-allowed hidden' 
                                    : 'border-transparent bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700' 
                                }}">
                                Cancel Appointment
                            </button> --}}
                        {{-- </form> --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>