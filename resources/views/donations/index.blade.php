<x-app-layout>
    {{-- HEADER SECTION --}}
    <section class="w-full bg-[#F4F2ED] dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
            <div class="text-center font-poppins">
                <h1 class="text-3xl md:text-5xl font-extrabold text-[#634600] dark:text-[#B59F84] leading-tight">
                    My Donations
                </h1>
                <p class="mt-4 text-lg text-[#603E14] dark:text-gray-200 mb-6 max-w-2xl mx-auto">
                    Manage the items you're giving away. Thank you for supporting circular fashion and reducing waste! 🌿
                </p>
                
                <a href="{{ route('donations.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#B59F84] text-white shadow-lg hover:bg-[#a08e77] hover:scale-105 transition-all duration-300 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    List a Donation
                </a>
            </div>
        </div>
    </section>

    <div class="py-10 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- LOGIC: Split the 'approved' verification items into Available vs Claimed --}}
            @php
                // Items that are verified AND claimed/donated
                $donatedItems = $approved->filter(fn($d) => $d->status === 'donated'); 
                
                // Items that are verified AND still available
                $availableDonations = $approved->diff($donatedItems);
            @endphp

            {{-- SECTION 1: REJECTED DONATIONS --}}
            @if($rejected->count() > 0)
                <div class="mb-10">
                    <div class="flex items-center gap-2 mb-4 px-1">
                        <div class="w-1.5 h-6 bg-red-500 rounded-full"></div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Action Required</h3>
                        <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $rejected->count() }}</span>
                    </div>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                        @foreach ($rejected as $donation)
                            @include('donations.partials.card', ['donation' => $donation])
                        @endforeach
                    </div>
                    <p class="mt-2 text-sm text-red-500 italic pl-1">* Click items to see why they were rejected.</p>
                </div>
                <hr class="border-gray-100 dark:border-gray-800 mb-10">
            @endif

            {{-- SECTION 2: PENDING APPROVAL --}}
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-4 px-1">
                    <div class="w-1.5 h-6 bg-amber-500 rounded-full"></div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Pending Review</h3>
                    <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $pending->count() }}</span>
                </div>

                @if($pending->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                        @foreach ($pending as $donation)
                            @include('donations.partials.card', ['donation' => $donation])
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 text-center border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-gray-500 text-sm">No donations currently waiting for approval.</p>
                    </div>
                @endif
            </div>

            <hr class="border-gray-100 dark:border-gray-800 mb-10">

            {{-- SECTION 3: AVAILABLE DONATIONS --}}
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-4 px-1">
                    <div class="w-1.5 h-6 bg-green-500 rounded-full"></div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Available Donations</h3>
                    <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $availableDonations->count() }}</span>
                </div>

                @if($availableDonations->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                        @foreach ($availableDonations as $donation)
                            @include('donations.partials.card', ['donation' => $donation])
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-10 text-center border border-dashed border-gray-200 dark:border-gray-700">
                        <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">No available donations</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 mb-6">You haven't listed any items for donation yet.</p>
                    </div>
                @endif
            </div>

            <hr class="border-gray-100 dark:border-gray-800 mb-10">

            {{-- SECTION 4: DONATED/CLAIMED ITEMS --}}
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-4 px-1">
                    <div class="w-1.5 h-6 bg-gray-500 rounded-full"></div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Donated Items</h3>
                    <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $donatedItems->count() }}</span>
                </div>

                @if($donatedItems->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 opacity-80">
                        @foreach ($donatedItems as $donation)
                            @include('donations.partials.card', ['donation' => $donation])
                        @endforeach
                    </div>
                @else
                    {{-- UPDATED EMPTY STATE: Matches the style of "Available" but using gray/neutral colors --}}
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-10 text-center border border-dashed border-gray-200 dark:border-gray-700">
                        <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">No items donated yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 mb-6">Once your items find a new home, they will appear here in your history.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>