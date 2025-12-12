<x-app-layout>
    <section class="w-full bg-[#F4F2ED] dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
            <div class="flex flex-col md:hidden text-center relative font-poppins">
                <h1 class="text-3xl font-extrabold text-[#634600] leading-tight dark:text-[#B59F84]">
                    My Items
                </h1>
                <p class="mt-2 text-lg text-[#603E14] dark:text-gray-200 mb-6">
                    Manage your sustainable fashion items 🌿
                </p>
                <div class="bg-white/70 dark:bg-gray-700/60 rounded-lg p-4 shadow-sm mb-6 text-left">
                    <h2 class="text-lg font-semibold text-[#634600] dark:text-white mb-2">
                        Your Sustainable Impact
                    </h2>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        By selling pre-loved items, you're extending the life of clothing,
                        reducing fashion waste, and promoting circular fashion. Every item sold makes a difference! ♻️
                    </p>
                </div>
                <div class="bg-white/70 dark:bg-gray-700/60 rounded-lg p-4 shadow-sm text-left">
                    <h3 class="text-md font-medium text-[#634600] dark:text-white mb-2">
                        Why Sell With Us?
                    </h3>
                    <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                        <li class="flex items-start"><span class="text-[#B59F84] mr-2">✓</span> Earn from your pre-loved
                            fashion</li>
                        <li class="flex items-start"><span class="text-[#B59F84] mr-2">✓</span> Reduce clothing waste
                            and pollution</li>
                        <li class="flex items-start"><span class="text-[#B59F84] mr-2">✓</span> Connect with conscious
                            buyers</li>
                    </ul>
                </div>
                <p class="mt-6 italic text-gray-600 dark:text-gray-400 text-sm">
                    "Sustainable fashion isn't a trend - it's the future." 👗
                </p>
                <span
                    class="absolute bottom-1 right-1 bg-[#F8EED6] px-2 py-0.5 rounded-full text-xs text-[#634600] font-medium shadow">
                    Circular Fashion
                </span>
            </div>

            <div class="hidden md:flex md:flex-row md:items-center gap-8">
                <div class="md:w-1/2 font-poppins">
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-[#634600] dark:text-[#B59F84] leading-tight">
                        My Items
                    </h1>
                    <p class="mt-4 text-xl text-[#603E14] dark:text-gray-200">
                        Manage your thrift store inventory 🌟
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('products.create') }}"
                            class="inline-block border border-[#B59F84] text-[#634600] hover:bg-[#F8EED6] dark:border-[#B59F84] dark:text-[#B59F84] dark:hover:bg-gray-700 font-semibold px-6 py-3 rounded-full shadow-md transition">
                            List New Item
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 h-[420px] flex gap-4 relative">
                    <img src="{{ Storage::disk('s3')->url('images/thrift-fashion.png') }}" alt="Thrift Fashion"
                        class="w-1/2 h-full object-cover rounded-xl shadow-lg hover:scale-[1.02] transition-transform duration-300">
                    <img src="{{ Storage::disk('s3')->url('images/sustainable-style.png') }}" alt="Sustainable Style"
                        class="w-1/2 h-full object-cover rounded-xl shadow-lg hover:scale-[1.02] transition-transform duration-300">
                </div>
            </div>
        </div>
    </section>

    <div class="py-6 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">Inventory Management
                </h2>
                <a href="{{ route('products.create') }}"
                    class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-full bg-[#B59F84] text-white shadow-sm hover:bg-[#a08e77] active:scale-[.98] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span class="font-semibold">List an Item</span>
                </a>
            </div>

            {{-- SECTION 1: REJECTED ITEMS (Action Required) --}}
            @if ($rejected->count() > 0)
                <div class="mb-10">
                    <div class="flex items-center gap-2 mb-4 px-1">
                        <div class="w-1.5 h-6 bg-red-500 rounded-full"></div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Action Required</h3>
                        <span
                            class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $rejected->count() }}</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-4 md:gap-6">
                        @foreach ($rejected as $product)
                            {{-- USING COMPONENT --}}
                            <x-item-card :product="$product" />
                        @endforeach
                    </div>
                    <p class="mt-2 text-sm text-red-500 italic pl-1">* Click items to see rejection reasons and edit.
                    </p>
                </div>
                <hr class="border-gray-100 dark:border-gray-800 mb-10">
            @endif

            {{-- SECTION 2: PENDING ITEMS --}}
            @if (!Auth::user()->is_verified || $pending->count() > 0)
                <div class="mb-10">
                    <div class="flex items-center gap-2 mb-4 px-1">
                        <div class="w-1.5 h-6 bg-amber-500 rounded-full"></div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Pending Review</h3>
                        <span
                            class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $pending->count() }}</span>
                    </div>

                    @if ($pending->count() > 0)
                        <div
                            class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-4 md:gap-6">
                            @foreach ($pending as $product)
                                <x-item-card :product="$product" />
                            @endforeach
                        </div>
                    @else
                        {{-- This "Empty State" box is now only visible to Unverified users --}}
                        <div
                            class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 text-center border border-dashed border-gray-200 dark:border-gray-700">
                            <p class="text-gray-500 text-sm">No items currently waiting for approval.</p>
                        </div>
                    @endif
                </div>

                <hr class="border-gray-100 dark:border-gray-800 mb-10">
            @endif

            <hr class="border-gray-100 dark:border-gray-800 mb-10">

            {{-- SECTION 3: APPROVED/ACTIVE ITEMS --}}
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-4 px-1">
                    <div class="w-1.5 h-6 bg-green-500 rounded-full"></div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Available & Sold Items</h3>
                    <span
                        class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $approved->count() }}</span>
                </div>

                @if ($approved->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-4 md:gap-6">
                        @foreach ($approved as $product)
                            {{-- USING COMPONENT --}}
                            <x-item-card :product="$product" />
                        @endforeach
                    </div>
                @else
                    <div
                        class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 text-center border border-dashed border-gray-200 dark:border-gray-700">
                        <div
                            class="mx-auto w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">No active listings</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 mb-6">Start selling your pre-loved
                            fashion today.</p>
                        <a href="{{ route('products.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-[#B59F84] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#a08e77] transition">
                            List Item
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
