<div id="donations" class="hidden overflow-hidden mb-8">
    <!-- Header with Description -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-2">
                <svg class="w-6 h-6 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M4 8h16M4 12h16M4 4h16"></path>
                </svg>
                Donation Management
            </h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm max-w-2xl">
                Track your donations, monitor approval and verification status, and manage proof submissions.
            </p>
        </div>
        <div class="text-right">
            <div class="text-lg font-bold text-[#B59F84]">{{ $donations->count() }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Donations</div>
        </div>
    </div>

    @php
        // Active: exclude donated items and anything still pending approval
        $activeDonations = $donations->filter(function ($donation) {
            return $donation->status !== 'donated' && $donation->approval_status !== 'pending';
        });
        $donatedDonations = $donations->where('status', 'donated');
    @endphp

    <!-- Active Donations -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-[#F1E9D2] dark:bg-[#9C8770] rounded-lg">
                <svg class="w-5 h-5 text-[#B59F84] dark:text-[#F1E9D2]" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M4 8h16M4 12h16M4 4h16"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Active Donations</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Items awaiting pickup or verification</p>
            </div>
        </div>

        <!-- Grid -->
        <div class="rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6">
                @if ($activeDonations->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-8">
                        @foreach ($activeDonations as $donation)
                            <div
                                class="group relative bg-[#F4F2ED] dark:bg-gray-800/90 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow hover:shadow-2xl transition-all duration-300">
                                <a href="{{ route('donations.show', $donation->id) }}" class="block h-full">

                                    <!-- Badge -->
                                    @if ($donation->listingtype === 'for donation')
                                        <div
                                            class="absolute top-2 left-2 z-10 bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full shadow">
                                            Donation
                                        </div>
                                    @endif

                                    <!-- Image -->
                                    <div class="relative aspect-square overflow-hidden">
                                        <img src="{{ $donation->donationImages->isNotEmpty() ? Storage::disk('s3')->url($donation->donationImages->first()->image) : asset('images/default-placeholder.png') }}"
                                            alt="{{ $donation->name }}" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" />

                                        {{-- Pending approval badge --}}
                                        @if ($donation->approval_status === 'pending')
                                            <div
                                                class="absolute top-2 right-2 z-10 bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full font-semibold shadow">
                                                Pending
                                            </div>
                                        @elseif($donation->approval_status === 'rejected' || $donation->approval_status === 'changes_requested')
                                            <div
                                                class="absolute top-2 right-2 z-10 bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-semibold shadow">
                                                {{ ucfirst(str_replace('_', ' ', $donation->approval_status)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Info -->
                                    <div class="p-4 space-y-2">
                                        <div class="flex justify-between items-start gap-2">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $donation->name }}
                                            </h3>
                                            <span
                                                class="text-xs font-medium px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">
                                                {{ $donation->size ?? 'L' }}
                                            </span>
                                        </div>
                                        <p class="text-gray-500 dark:text-gray-400 text-xs truncate">
                                            {{ $donation->category->name ?? 'No Category' }}
                                        </p>
                                        <div
                                            class="absolute top-2 left-2 z-10 dark:bg-green-300 bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full shadow">
                                            Free
                                        </div>
                                        <div class="mt-3 flex flex-wrap gap-2 text-[11px]">
                                            <span class="px-2 py-1 rounded-full font-medium
                                                @if ($donation->status === 'donated') bg-[#E1D5B6] text-[#6B5B48]
                                                @elseif($donation->status === 'available') bg-[#F8F4EC] text-[#B59F84]
                                                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 @endif">
                                                Status: {{ ucfirst($donation->status ?? 'n/a') }}
                                            </span>
                                            <span class="px-2 py-1 rounded-full font-medium
                                                @if ($donation->verification_status === 'verified') bg-[#F8F4EC] text-[#B59F84]
                                                @elseif($donation->verification_status === 'pending') bg-[#F1E9D2] text-[#8A7560]
                                                @elseif($donation->verification_status === 'rejected') bg-red-100 text-red-700
                                                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 @endif">
                                                Verification: {{ ucfirst($donation->verification_status ?? 'none') }}
                                            </span>
                                        </div>
                                    </div>
                                </a>

                                <div class="px-4 pb-4 flex gap-2">
                                    <a href="{{ route('donations.show', $donation->id) }}"
                                        class="inline-flex items-center justify-center px-3 py-2 text-xs bg-[#B59F84] text-white rounded hover:bg-[#9C8770] transition-colors gap-1 flex-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                    @if (Auth::id() === $donation->user_id && $donation->status !== 'donated')
                                        <a href="{{ route('donations.edit', $donation->id) }}"
                                            class="inline-flex items-center justify-center px-3 py-2 text-xs bg-[#8A7560] text-white rounded hover:bg-[#6B5B48] transition-colors gap-1 flex-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M15.5 4.5a2.121 2.121 0 113 3L12 14l-4 1 1-4 6.5-6.5z">
                                                </path>
                                            </svg>
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    @if (Auth::id() === $user->id)
                        <x-empty-message message="No active donations found." link="{{ route('donations.create') }}"
                            buttonText="Add Donation" icon="shopping-cart" />
                    @else
                        <x-empty-message message="No active donations found." icon="shopping-cart" />
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Donated -->
    <div>
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-[#F1E9D2] dark:bg-[#8A7560] rounded-lg">
                <svg class="w-5 h-5 text-[#8A7560] dark:text-[#F1E9D2]" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Donated Items</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Successfully donated items</p>
            </div>
        </div>

        <div class="rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6">
                @if ($donatedDonations->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-8">
                        @foreach ($donatedDonations as $donation)
                            <div
                                class="group relative bg-[#F4F2ED] dark:bg-gray-800/90 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow hover:shadow-2xl transition-all duration-300">
                                <a href="{{ route('donations.show', $donation->id) }}" class="block h-full">
                                    <div class="absolute inset-x-0 top-0 px-3 pt-3 flex justify-between z-10">
                                        <div
                                            class="bg-[#E1D5B6] text-[#6B5B48] text-xs px-3 py-1 rounded-full shadow">
                                            Donated
                                        </div>
                                        @if ($donation->verification_status === 'verified')
                                            <div
                                                class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-semibold shadow">
                                                Verified
                                            </div>
                                        @elseif($donation->verification_status === 'pending')
                                            <div
                                                class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full font-semibold shadow">
                                                Verification Pending
                                            </div>
                                        @elseif($donation->verification_status === 'rejected')
                                            <div
                                                class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-semibold shadow">
                                                Verification Rejected
                                            </div>
                                        @endif
                                    </div>

                                    <div class="relative aspect-square overflow-hidden">
                                        <img src="{{ $donation->donationImages->isNotEmpty() ? Storage::disk('s3')->url($donation->donationImages->first()->image) : asset('images/default-placeholder.png') }}"
                                            alt="{{ $donation->name }}" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" />
                                    </div>

                                    <div class="p-4 space-y-2">
        <div class="flex justify-between items-start gap-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                {{ $donation->name }}
            </h3>
            <span class="text-xs font-medium px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">
                {{ $donation->size ?? 'N/A' }}
            </span>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-xs truncate">
            {{ $donation->category->name ?? 'Uncategorized' }}
        </p>
        
        <div class="mt-3 flex flex-wrap gap-2 text-[11px]">
            <span class="px-2 py-1 rounded-full font-medium bg-[#E1D5B6] text-[#6B5B48]">
                Status: Donated
            </span>
            <span class="px-2 py-1 rounded-full font-medium
                @if ($donation->verification_status === 'verified') bg-[#F8F4EC] text-[#B59F84]
                @elseif($donation->verification_status === 'pending') bg-[#F1E9D2] text-[#8A7560]
                @elseif($donation->verification_status === 'rejected') bg-red-100 text-red-700
                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 @endif">
                Verification: {{ ucfirst($donation->verification_status ?? 'none') }}
            </span>
        </div>
    </div>
                                </a>

                                <div class="px-4 pb-4 flex gap-2">
                                    <a href="{{ route('donations.show', $donation->id) }}"
                                        class="inline-flex items-center justify-center px-3 py-2 text-xs bg-[#B59F84] text-white rounded hover:bg-[#9C8770] transition-colors gap-1 flex-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-empty-message message="No donated items yet." icon="check-circle" />
                @endif
            </div>
        </div>
    </div>
</div>