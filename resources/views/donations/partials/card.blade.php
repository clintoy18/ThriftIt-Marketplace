<div class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition duration-200 border border-[#D9D9D9] dark:border-gray-700">
    <a href="{{ route('donations.show', $donation->id) }}" class="block h-full">

        {{-- BADGES (Left Side) --}}
        <div class="absolute top-1 left-1 z-10 flex flex-col gap-1">
            <div class="bg-green-100 text-green-800 border border-green-200 text-[10px] sm:text-xs px-2 py-0.5 rounded-full font-bold shadow-sm">
                Free
            </div>
            
            @if($donation->status === 'taken')
                <div class="bg-gray-800 text-white text-[10px] sm:text-xs px-2 py-0.5 rounded-full font-bold shadow-sm">
                    Taken
                </div>
            @endif
        </div>

        {{-- STATUS BADGES (Right Side) --}}
        <div class="absolute top-1 right-1 z-10 flex flex-col gap-1">
            @if($donation->approval_status === 'pending')
                <div class="bg-amber-100 text-amber-800 border border-amber-200 text-[10px] sm:text-xs px-2 py-0.5 rounded-full font-bold shadow-sm">
                    Pending
                </div>
            @endif

            @if($donation->approval_status === 'rejected')
                <div class="bg-red-100 text-red-800 border border-red-200 text-[10px] sm:text-xs px-2 py-0.5 rounded-full font-bold shadow-sm">
                    Rejected
                </div>
            @endif
        </div>

        {{-- IMAGE --}}
        <div class="relative aspect-square overflow-hidden bg-gray-100">
            {{-- Handle image logic safely --}}
            @php
                $imgUrl = asset('images/default-placeholder.png');
                if($donation->donationImages && $donation->donationImages->isNotEmpty()) {
                    $imgUrl = Storage::disk('s3')->url($donation->donationImages->first()->image);
                }
            @endphp
            
            <img src="{{ $imgUrl }}" alt="{{ $donation->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

            {{-- Hover Overlay --}}
            <div class="absolute inset-0 bg-gray-800 bg-opacity-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                <span class="bg-white text-gray-800 px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold shadow-sm transform translate-y-2 group-hover:translate-y-0 transition-transform">
                    @if($donation->approval_status === 'rejected')
                        View Reason
                    @else
                        View Details
                    @endif
                </span>
            </div>
        </div>

        {{-- INFO --}}
        <div class="p-3">
            <div class="flex justify-between items-start mb-1">
                <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate max-w-[70%]" title="{{ $donation->name }}">
                    {{ $donation->name }}
                </h3>
                <span class="text-[10px] sm:text-xs font-bold px-1.5 py-0.5 bg-[#D9D9D9] dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">
                    {{ $donation->size ?? 'N/A' }}
                </span>
            </div>

            <p class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs truncate">
                {{ $donation->category->name ?? 'No Category' }}
            </p>

            <p class="text-gray-400 dark:text-gray-500 text-[10px] sm:text-xs mt-0.5 truncate flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $donation->barangay->name ?? 'Cebu City' }}
            </p>

            <div class="flex justify-between items-center mt-3 pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
                <p class="text-xs sm:text-sm font-bold text-green-600 dark:text-green-400">
                    For Donation
                </p>
                @if($donation->approval_status === 'approved')
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-red-400 transition-colors" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                @endif
            </div>
        </div>
    </a>
</div>