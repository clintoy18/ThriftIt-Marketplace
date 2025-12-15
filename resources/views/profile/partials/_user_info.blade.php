<div class="relative bg-[#E1D5B6] dark:bg-gray-800 -mt-16 rounded-b-lg shadow-sm">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Main Container: Stack Vertical, Align Left --}}
        <div class="flex flex-col items-start pb-5">

            {{-- 1. PROFILE IMAGE (Top) --}}
            {{-- Negative margin pulls it up into the banner --}}
            <div class="relative -mt-12 sm:-mt-[60px] mb-3 z-10">
                <div class="w-[80px] h-[80px] sm:w-[120px] sm:h-[120px] rounded-full border-4 border-white dark:border-gray-800 overflow-hidden shadow-lg">
                    <img src="{{ $user->profileImageUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                </div>
            </div>

            {{-- 2. USER DETAILS (Below Profile Image) --}}
            <div class="w-full flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
                
                {{-- Left Side: Name & Reviews --}}
                <div class="text-left">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100 text-lg sm:text-2xl">
                        <x-user-name-badge :user="$user" :show-full-name="true" />
                    </h3>
                    
                    {{-- Reviews Row --}}
                    <div class="flex items-center mt-1 gap-2">
                        @php
                            $averageRating = $user->average_rating;
                            $reviewCount = $user->review_count;
                            $fullStars = floor($averageRating);
                            $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                        @endphp
                        
                        <div class="flex items-center text-yellow-500">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $fullStars)
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" /></svg>
                                @elseif ($i == $fullStars + 1 && $hasHalfStar)
                                    <div class="relative w-4 h-4 sm:w-5 sm:h-5 inline-block">
                                        <svg class="absolute inset-0 w-full h-full fill-current text-gray-300 dark:text-gray-600" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" /></svg>
                                        <svg class="absolute inset-0 w-full h-full fill-current" style="clip-path: inset(0 50% 0 0);" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" /></svg>
                                    </div>
                                @else
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-current text-gray-300 dark:text-gray-600" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" /></svg>
                                @endif
                            @endfor
                        </div>
                        
                        <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 font-medium">
                            {{ $reviewCount > 0 ? number_format($averageRating, 1) . " ($reviewCount)" : 'No reviews' }}
                        </span>
                    </div>
                </div>

                {{-- Right Side: Eco Points --}}
                <div class="text-left sm:text-right mt-2 sm:mt-0">
                    <span class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Eco Points</span>
                    <span class="text-3xl sm:text-4xl font-extrabold text-[#B59F84] dark:text-yellow-400">
                        {{ $user->points ?? 0 }}
                    </span>
                </div>
            </div>

        </div>

        {{-- Action Buttons --}}
        @if (Auth::id() !== $user->id)
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 pb-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-6 w-full sm:w-auto justify-center sm:justify-start">
                        <button x-on:click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'report-modal' }))"
                                class="flex items-center text-red-600 dark:text-red-400 hover:text-red-700 text-sm font-medium transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a1 1 0 00.86 1.5h18.64a1 1 0 00.86-1.5L13.71 3.86a1 1 0 00-1.72 0z" />
                            </svg>
                            Report
                        </button>
                        <button x-on:click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'review-modal' }))"
                                class="flex items-center text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 text-sm font-medium transition">
                            <svg class="w-4 h-4 mr-1.5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg>
                            Write Review
                        </button>
                    </div>

                    <a href="{{ route('private.chat', $user->id) }}"
                       class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2 bg-[#5C4033] hover:bg-[#4a332a] text-white rounded-full text-sm font-semibold shadow-md hover:shadow-lg transition-all dark:bg-[#8E6542] dark:hover:bg-[#7A5238]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Send Message
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>