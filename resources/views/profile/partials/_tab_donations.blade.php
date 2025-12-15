<div id="donations" class="hidden overflow-hidden mb-8">
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
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Available Items for Donations</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Items awaiting to be claimed.</p>
            </div>
        </div>

        <div class="rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6">
                @if ($activeDonations->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-8">
                        @foreach ($activeDonations as $donation)
                            {{-- MODULAR CARD: Uses your new Earth Tone design --}}
                            @include('donations.partials.card', ['donation' => $donation])
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
                <p class="text-sm text-gray-600 dark:text-gray-400">This seller's donated items.</p>
            </div>
        </div>

        <div class="rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6">
                @if ($donatedDonations->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-8">
                        @foreach ($donatedDonations as $donation)
                            {{-- MODULAR CARD: Uses your new Earth Tone design --}}
                            @include('donations.partials.card', ['donation' => $donation])
                        @endforeach
                    </div>
                @else
                    <x-empty-message message="No donated items yet." icon="check-circle" />
                @endif
            </div>
        </div>
    </div>
</div>