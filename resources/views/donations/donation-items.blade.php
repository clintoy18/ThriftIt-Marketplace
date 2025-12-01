<x-app-layout>
    <!-- Hero Section for My Products -->
    <section class="w-full bg-[#F4F2ED] dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">

            <!-- Mobile Layout -->
            <div class="flex flex-col md:hidden text-center relative font-poppins">
                <!-- Title -->
                <h1 class="text-3xl font-extrabold text-[#634600] leading-tight dark:text-[#B59F84]">
                    My Products
                </h1>
                <p class="mt-2 text-lg text-[#603E14] dark:text-gray-200 mb-6">
                    Manage your sustainable fashion items 🌿
                </p>
                <!-- Stats Box -->
                <div class="bg-white/70 dark:bg-gray-700/60 rounded-lg p-4 shadow-sm mb-6 text-left">
                    <h2 class="text-lg font-semibold text-[#634600] dark:text-white mb-2">
                        Your Sustainable Impact
                    </h2>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        By selling pre-loved items, you're extending the life of clothing,
                        reducing fashion waste, and promoting circular fashion. Every item sold makes a difference! ♻️
                    </p>
                </div>

                <!-- Benefits -->
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

                <!-- Quote -->
                <p class="mt-6 italic text-gray-600 dark:text-gray-400 text-sm">
                    "Sustainable fashion isn't a trend - it's the future." 👗
                </p>

                <!-- Tag -->
                <span
                    class="absolute bottom-1 right-1 bg-[#F8EED6] px-2 py-0.5 rounded-full text-xs text-[#634600] font-medium shadow">
                    Circular Fashion
                </span>
            </div>

            <!-- Desktop Layout -->
            <div class="hidden md:flex md:flex-row md:items-center gap-8">
                <!-- Text Content -->
                <div class="md:w-1/2 font-poppins">
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-[#634600] dark:text-[#B59F84] leading-tight">
                        My Products
                    </h1>
                    <p class="mt-4 text-xl text-[#603E14] dark:text-gray-200">
                        Manage your thrift store inventory 🌟
                    </p>

                    <!-- Buttons -->
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('products.create') }}"
                            class="inline-block border border-[#B59F84] text-[#634600] hover:bg-[#F8EED6] 
                                  dark:border-[#B59F84] dark:text-[#B59F84] dark:hover:bg-gray-700 
                                  font-semibold px-6 py-3 rounded-full shadow-md transition">
                            List New Product
                        </a>

                    </div>
                </div>

                <!-- Images -->
                <div class="md:w-1/2 h-[420px] flex gap-4 relative">
                    <img src="{{ Storage::disk('s3')->url('images/thrift-fashion.png') }}" alt="Thrift Fashion"
                        class="w-1/2 h-full object-cover rounded-xl shadow-lg hover:scale-[1.02] transition-transform duration-300">
                    <img src="{{ Storage::disk('s3')->url('images/sustainable-style.png') }}" alt="Sustainable Style"
                        class="w-1/2 h-full object-cover rounded-xl shadow-lg hover:scale-[1.02] transition-transform duration-300">
                </div>
            </div>
        </div>
    </section>

    <!-- Donation Hub -->
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Donation Hub
                </h2>
                <a href="{{ route('donations.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#B59F84] text-white shadow-md hover:bg-[#a08e77] transition">
                    <span class="font-semibold">Donate an Item</span>
                </a>
            </div>

            <!-- Grid -->
            <div class="rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6">
                    @if ($donations->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-8">
                            @foreach ($donations as $donation)
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
                                                alt="{{ $donation->name }}" class="w-full h-full object-cover" />
                                            class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                                        </div>

                                        <!-- Info -->
                                        <div class="p-4">
                                            <div class="flex justify-between items-start">
                                                <h3
                                                    class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $donation->name }}
                                                </h3>
                                                <span
                                                    class="text-xs font-medium px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-700 dark:text-gray-300">
                                                    {{ $donation->size ?? 'L' }}
                                                </span>
                                            </div>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-1 truncate">
                                                {{ $donation->category->name ?? 'No Category' }}
                                            </p>
                                            <div
                                                class="absolute top-2 left-2 z-10 dark:bg-green-300 bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full shadow">
                                                Free
                                            </div>

                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-empty-message message="No active donations found." link="{{ route('donations.create') }}"
                            buttonText="Add Donation" icon="shopping-cart" />
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
