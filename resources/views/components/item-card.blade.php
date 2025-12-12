@props(['product'])

@php
    // Determine status for cleaner logic in the template
    $isSold = $product->status === 'sold';
    $isPending = $product->approval_status === 'pending';
    $isRejected = $product->approval_status === 'rejected';
    $isDonation = $product->listingtype === 'for donation';
@endphp

<div {{ $attributes->merge(['class' => 'group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition border border-[#D9D9D9] dark:border-gray-700']) }}>
    <a href="{{ route('products.show', $product->id) }}" class="block h-full">
        
        {{-- === STATUS BADGES (Top Left) === --}}
        <div class="absolute top-1 left-1 z-20 flex flex-col gap-1">
            @if($isSold)
                <div class="bg-[#8A7560] text-white text-[10px] sm:text-xs px-2 py-1 rounded-full font-semibold w-max shadow-sm">
                    Sold
                </div>
            @endif

            @if($isPending)
                <div class="bg-amber-100 text-amber-800 border border-amber-200 text-[10px] sm:text-xs px-2 py-1 rounded-full font-semibold w-max shadow-sm">
                    Pending
                </div>
            @endif

            @if($isRejected)
                <div class="bg-red-100 text-red-800 border border-red-200 text-[10px] sm:text-xs px-2 py-1 rounded-full font-semibold w-max shadow-sm">
                    Rejected
                </div>
            @endif

            @if($isDonation)
                <div class="bg-[#D9D9D9] text-gray-700 text-[10px] sm:text-xs px-2 py-1 rounded-full w-max font-medium shadow-sm">
                    Donation
                </div>
            @endif
        </div>

        {{-- === IMAGE SECTION === --}}
        <div class="relative aspect-square overflow-hidden bg-gray-100">
            {{-- Use first_image accessor if you have it, or raw relationship --}}
            <img src="{{ optional($product->images->first())->image 
                ? Storage::disk('s3')->url($product->images->first()->image) 
                : asset('images/no-image.png') }}" 
                alt="{{ $product->name }}" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            
            {{-- Hover Overlay --}}
            <div class="absolute inset-0 bg-gray-800 bg-opacity-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                <span class="bg-white text-gray-800 px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-medium shadow-sm">
                    @if($isRejected)
                        View Reason
                    @elseif($isSold)
                        View Archive
                    @else
                        Quick view
                    @endif
                </span>
            </div>
        </div>

        {{-- === INFO SECTION === --}}
        <div class="p-3">
            {{-- Title & Size --}}
            <div class="flex justify-between items-start">
                <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate max-w-[70%]" title="{{ $product->name }}">
                    {{ $product->name }}
                </h3>
                <span class="text-[10px] sm:text-xs font-medium px-1 py-0.5 bg-[#D9D9D9] dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">
                    {{ $product->size ?? 'N/A' }}
                </span>
            </div>

            {{-- Category --}}
            <p class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs mt-0.5 truncate">
                {{ $product->category->name ?? 'No Category' }}
            </p>

            {{-- Location --}}
            <p class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs mt-0.5 truncate">
                <i>{{ $product->barangay->name ?? 'N/A' }}, Cebu City</i>
            </p>

            {{-- Price & Actions --}}
            <div class="flex justify-between items-center mt-2">
                <p class="text-xs sm:text-sm font-bold {{ $isDonation ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white' }}">
                    {{ $isDonation ? 'Free' : '₱' . number_format($product->price, 2) }}
                </p>
                
                {{-- Only show Favorite button if Approved --}}
                @if($product->approval_status === 'approved')
                    <button class="favorite-btn text-gray-400 hover:text-red-500 transition-colors focus:outline-none" 
                            data-id="{{ $product->id }}"
                            onclick="event.preventDefault();"> <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                @elseif($isRejected)
                     <span class="text-[10px] text-red-500 font-medium">Fix Issues &rarr;</span>
                @endif
            </div>
        </div>
    </a>
</div>