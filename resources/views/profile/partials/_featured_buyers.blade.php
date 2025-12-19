<section class="mt-10">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-4">
        <div>
            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">
                Featured Buyers
            </h2>
            <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                Influencers &amp; celebrities who love sustainable fashion
            </span>
        </div>
        <div class="flex items-center gap-2 text-xs sm:text-sm text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 px-3 py-2 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Only feature buyers after they give explicit consent.</span>
            <a class="inline-flex items-center gap-1 text-xs font-semibold text-amber-800 dark:text-amber-200 underline" href="mailto:?subject=Featured%20Buyer%20Invitation&body=Hi%2C%0D%0AWe%27d%20love%20to%20feature%20you%20as%20a%20buyer%20on%20our%20profile.%20Please%20confirm%20your%20consent%20and%20any%20preferred%20details%20to%20display.%0D%0A">
                Request consent
            </a>
        </div>
    </div>

    {{-- === FEATURED BUYERS + THEIR EXPENSIVE PURCHASES === --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Buyer 1 --}}
        <div class="space-y-3">
            <div class="group flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-tr from-pink-400 via-red-400 to-yellow-300 flex items-center justify-center text-white font-bold text-lg shadow">
                        A
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        @lang('Alexa Rivera')
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        2.3M followers • Sustainable Fashion Creator
                    </p>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 line-clamp-2">
                        “I love finding unique thrifted pieces here. It’s my go-to for styling sustainable looks.”
                    </p>
                </div>
            </div>

            {{-- Alexa's expensive items (static demo) --}}
            <div class="grid grid-cols-1 gap-3">
                <div class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition border border-[#D9D9D9] dark:border-gray-700">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="{{ asset('images/no-image.png') }}"
                             alt="Designer Leather Trench Coat"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-2 left-2">
                            <span class="bg-[#8A7560] text-white text-[10px] sm:text-xs px-2 py-1 rounded-full font-semibold shadow-sm">
                                Sold to @Alexa
                            </span>
                        </div>
                    </div>
                    <div class="p-3">
                        <div class="flex justify-between items-start">
                            <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate max-w-[70%]">
                                Designer Leather Trench Coat
                            </h3>
                            <span class="text-[10px] sm:text-xs font-medium px-1 py-0.5 bg-[#D9D9D9] dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">
                                Size M
                            </span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs mt-0.5 truncate">
                            Premium Outerwear
                        </p>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">
                                ₱9,500.00
                            </p>
                            <span class="text-[10px] sm:text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                Verified purchase
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Buyer 2 --}}
        <div class="space-y-3">
            <div class="group flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-tr from-indigo-500 to-sky-400 flex items-center justify-center text-white font-bold text-lg shadow">
                        L
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        @lang('Liam Cruz')
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        1.1M followers • Eco-conscious Actor
                    </p>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 line-clamp-2">
                        “Supporting circular fashion through this marketplace makes every outfit feel more meaningful.”
                    </p>
                </div>
            </div>

            {{-- Liam's expensive items (static demo) --}}
            <div class="grid grid-cols-1 gap-3">
                <div class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition border border-[#D9D9D9] dark:border-gray-700">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="{{ asset('images/no-image.png') }}"
                             alt="Limited Edition Sneakers"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-2 left-2">
                            <span class="bg-[#8A7560] text-white text-[10px] sm:text-xs px-2 py-1 rounded-full font-semibold shadow-sm">
                                Sold to @Liam
                            </span>
                        </div>
                    </div>
                    <div class="p-3">
                        <div class="flex justify-between items-start">
                            <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate max-w-[70%]">
                                Limited Edition Sneakers
                            </h3>
                            <span class="text-[10px] sm:text-xs font-medium px-1 py-0.5 bg-[#D9D9D9] dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">
                                Size 42
                            </span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs mt-0.5 truncate">
                            Streetwear Collectible
                        </p>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">
                                ₱12,800.00
                            </p>
                            <span class="text-[10px] sm:text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                Verified purchase
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Buyer 3 --}}
        <div class="space-y-3">
            <div class="group flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-tr from-emerald-500 to-lime-400 flex items-center justify-center text-white font-bold text-lg shadow">
                        C
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        @lang('Camille Santos')
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        850k followers • Lifestyle &amp; Thrift Influencer
                    </p>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 line-clamp-2">
                        “Thrift-It makes it easy to discover pre-loved gems while reducing fashion waste.”
                    </p>
                </div>
            </div>

            {{-- Camille's expensive items (static demo) --}}
            <div class="grid grid-cols-1 gap-3">
                <div class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition border border-[#D9D9D9] dark:border-gray-700">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img src="{{ asset('images/no-image.png') }}"
                             alt="Vintage Designer Dress"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-2 left-2">
                            <span class="bg-[#8A7560] text-white text-[10px] sm:text-xs px-2 py-1 rounded-full font-semibold shadow-sm">
                                Sold to @Camille
                            </span>
                        </div>
                    </div>
                    <div class="p-3">
                        <div class="flex justify-between items-start">
                            <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate max-w-[70%]">
                                Vintage Designer Dress
                            </h3>
                            <span class="text-[10px] sm:text-xs font-medium px-1 py-0.5 bg-[#D9D9D9] dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">
                                Size S
                            </span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs mt-0.5 truncate">
                            Special Occasion Wear
                        </p>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">
                                ₱7,900.00
                            </p>
                            <span class="text-[10px] sm:text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                Verified purchase
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


