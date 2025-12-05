<x-app-layout>
    <!-- Hero Section -->
    <section class="w-full bg-[#F4F2ED] dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">

            <!-- Mobile Layout -->
            <div class="flex flex-col md:hidden text-center relative font-poppins">
                <!-- Title -->
                <h1 class="text-3xl font-extrabold text-green-700 leading-tight dark:text-green-400">
                    Donate Your Items!
                </h1>
                <p class="mt-2 text-lg text-custom-brown dark:text-gray-200 mb-6">
                    Give your pre-loved clothes a new life 🌍
                </p>

                <!-- Buttons -->
                <div class="flex flex-col gap-3 mb-8">
                    <a href="{{ route('donations.index') }}"
                        class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                        Donate Now!
                    </a>
                    <a href="{{ route('eco-posts.index') }}"
                        class="inline-block border border-green-600 text-green-700 hover:bg-green-50 dark:border-green-400 dark:text-green-400 dark:hover:bg-gray-700 font-semibold px-6 py-3 rounded-full shadow-md transition">
                        Join Our Community
                    </a>
                </div>

                <!-- Impact Box -->
                <div class="bg-white/70 dark:bg-gray-700/60 rounded-lg p-4 shadow-sm mb-6 text-left">
                    <h2 class="text-lg font-semibold text-custom-brown dark:text-white mb-2">
                        Make an Impact
                    </h2>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Every donation counts. By donating your items, you’re helping communities,
                        reducing waste, and promoting sustainable living. Together, we can create a greener future. 🌱
                    </p>
                </div>

                <!-- Why Donate -->
                <div class="bg-white/70 dark:bg-gray-700/60 rounded-lg p-4 shadow-sm text-left">
                    <h3 class="text-md font-medium text-custom-brown dark:text-white mb-2">
                        Why Donate With Us?
                    </h3>
                    <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                        <li class="flex items-start"><span class="text-green-600 mr-2">✓</span> Support local
                            communities in need</li>
                        <li class="flex items-start"><span class="text-green-600 mr-2">✓</span> Reduce clothing waste
                            and pollution</li>
                        <li class="flex items-start"><span class="text-green-600 mr-2">✓</span> Encourage a cycle of
                            reuse and sustainability</li>
                    </ul>
                </div>

                <!-- Quote -->
                <p class="mt-6 italic text-gray-600 dark:text-gray-400 text-sm">
                    "The greatest threat to our planet is the belief that someone else will save it." 🌎
                </p>

                <!-- Tag -->
                <span
                    class="absolute bottom-1 right-1 bg-green-100 px-2 py-0.5 rounded-full text-xs text-green-700 font-medium shadow">
                    Eco-Friendly Giving
                </span>
            </div>

            <!-- Desktop Layout -->
            <div class="hidden md:flex md:flex-row md:items-center gap-8">
                <!-- Text Content -->
                <div class="md:w-1/2 font-poppins">
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-green-700 dark:text-green-400 leading-tight">
                        Donate Your Items!
                    </h1>
                    <p class="mt-4 text-xl text-custom-brown dark:text-gray-200">
                        Turn clutter into kindness 🌱
                    </p>

                    <!-- Buttons -->
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('donations.index') }}"
                            class="inline-block border border-green-600 text-green-700 hover:bg-green-50 
                            dark:border-green-400 dark:text-green-400 dark:hover:bg-gray-700 
                            font-semibold px-6 py-3 rounded-full shadow-md transition">
                            Donate Now!
                        </a>
                        <a href="{{ route('eco-posts.index') }}"
                            class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold 
                            px-6 py-3 rounded-full shadow-md transition">
                            Eco Educational Community
                        </a>
                    </div>
                </div>

                <!-- Images -->
                <div class="md:w-1/2 h-[420px] flex gap-4 relative">
                    <img src="{{ asset('images/donate-clothes.png') }}" alt="Donate Clothes"
                        class="w-1/2 h-full object-cover rounded-xl shadow-lg hover:scale-[1.02] transition-transform duration-300">
                    <img src="{{ asset('images/helping-community.png') }}" alt="Helping Community"
                        class="w-1/2 h-full object-cover rounded-xl shadow-lg hover:scale-[1.02] transition-transform duration-300">
                </div>
            </div>
        </div>
    </section>

    <!-- Donation Hub -->
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-10">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Donation Hub
                </h2>

                <div class="flex items-center gap-3">
                    {{-- Category & Location filters (dashboard-style) --}}
                    @isset($categories, $barangays)
                        <div class="flex gap-2">
                            {{-- Category dropdown --}}
                            <div x-data="{ open: false, search: '' }" class="relative">
                                <button @click="open = !open"
                                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm shadow-sm">
                                    <span id="donationCategoryButtonText">
                                        {{ isset($categoryId) && $categories->firstWhere('id', $categoryId) ? $categories->firstWhere('id', $categoryId)->name : 'Category' }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700 dark:text-gray-300"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                                <div x-cloak x-show="open" @click.outside="open = false; search = ''"
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-20 py-1">
                                    <div class="px-3 py-1">
                                        <input x-model="search" placeholder="Search category..."
                                            class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="max-h-64 overflow-y-auto mt-1">
                                        <a data-donation-category-link data-category-name="Category"
                                            href="{{ route('donations.hub', ['barangay' => request('barangay')]) }}"
                                            class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                                            All
                                        </a>
                                        @foreach ($categories as $cat)
                                            <a x-show="search === '' || '{{ strtolower($cat->name) }}'.includes(search.toLowerCase())"
                                                data-donation-category-link data-category-name="{{ $cat->name }}"
                                                href="{{ route('donations.hub', ['category' => $cat->id, 'barangay' => request('barangay')]) }}"
                                                class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg {{ isset($categoryId) && (int) $categoryId === $cat->id ? 'font-semibold' : '' }}">
                                                {{ $cat->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Location dropdown --}}
                            <div x-data="{ open: false, search: '' }" class="relative">
                                <button @click="open = !open"
                                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm shadow-sm">
                                    <span id="donationLocationButtonText">
                                        {{ isset($barangayId) && $barangays->firstWhere('id', $barangayId) ? $barangays->firstWhere('id', $barangayId)->name : 'Location' }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700 dark:text-gray-300"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                                <div x-cloak x-show="open" @click.outside="open = false; search = ''"
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-20 py-1">
                                    <div class="px-3 py-1">
                                        <input x-model="search" placeholder="Search location..."
                                            class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="max-h-64 overflow-y-auto mt-1">
                                        <a data-donation-location-link data-location-name="Location"
                                            href="{{ route('donations.hub', ['category' => request('category')]) }}"
                                            class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                                            All
                                        </a>
                                        @foreach ($barangays as $barangay)
                                            <a x-show="search === '' || '{{ strtolower($barangay->name) }}'.includes(search.toLowerCase())"
                                                data-donation-location-link data-location-name="{{ $barangay->name }}"
                                                href="{{ route('donations.hub', ['category' => request('category'), 'barangay' => $barangay->id]) }}"
                                                class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg {{ isset($barangayId) && (int) $barangayId === $barangay->id ? 'font-semibold' : '' }}">
                                                {{ $barangay->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endisset

                   
                </div>
            </div>

            <!-- Grid -->
            <div class="rounded-2xl shadow-sm overflow-hidden">
                <!-- Loading Indicator -->
                <div id="donationsLoadingIndicator" class="hidden flex items-center justify-center py-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#634600]"></div>
                    <span class="ml-2 text-gray-600 dark:text-gray-300">Loading donations...</span>
                </div>

                <div id="donationsGrid" class="p-6">
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
    <script>
        // Dashboard-like filtering for Donation Hub (AJAX)
        document.addEventListener('DOMContentLoaded', function() {
            const donationsGrid = document.getElementById('donationsGrid');
            const loadingIndicator = document.getElementById('donationsLoadingIndicator');
            if (!donationsGrid) return;

            function showLoading() {
                if (loadingIndicator) {
                    loadingIndicator.classList.remove('hidden');
                }
                donationsGrid.style.opacity = '0.5';
            }

            function hideLoading() {
                if (loadingIndicator) {
                    loadingIndicator.classList.add('hidden');
                }
                donationsGrid.style.opacity = '1';
            }

            // Category filter links
            document.querySelectorAll('[data-donation-category-link]').forEach(link => {
                link.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const currentUrl = new URL(window.location);
                    const linkUrl = new URL(e.currentTarget.href, window.location.origin);
                    const buttonText = document.getElementById('donationCategoryButtonText');

                    const categoryName = e.currentTarget.getAttribute('data-category-name') || 'Category';
                    if (buttonText) {
                        buttonText.textContent = categoryName;
                    }

                    const params = new URLSearchParams();
                    if (linkUrl.searchParams.get('category')) {
                        params.set('category', linkUrl.searchParams.get('category'));
                    }
                    if (currentUrl.searchParams.get('barangay')) {
                        params.set('barangay', currentUrl.searchParams.get('barangay'));
                    }

                    showLoading();

                    try {
                        const url = '{{ route('donations.hub') }}' + (params.toString() ? '?' + params.toString() : '');
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        });
                        if (!response.ok) throw new Error('Network error');

                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newGrid = doc.getElementById('donationsGrid');
                        if (newGrid) {
                            donationsGrid.innerHTML = newGrid.innerHTML;
                        }
                    } catch (err) {
                        console.error('Error filtering donations by category:', err);
                    } finally {
                        hideLoading();
                    }

                    const newUrl = new URL(window.location);
                    if (linkUrl.searchParams.get('category')) {
                        newUrl.searchParams.set('category', linkUrl.searchParams.get('category'));
                    } else {
                        newUrl.searchParams.delete('category');
                    }
                    if (currentUrl.searchParams.get('barangay')) {
                        newUrl.searchParams.set('barangay', currentUrl.searchParams.get('barangay'));
                    }
                    window.history.replaceState({}, '', newUrl);
                });
            });

            // Location filter links
            document.querySelectorAll('[data-donation-location-link]').forEach(link => {
                link.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const currentUrl = new URL(window.location);
                    const linkUrl = new URL(e.currentTarget.href, window.location.origin);
                    const buttonText = document.getElementById('donationLocationButtonText');

                    const locationName = e.currentTarget.getAttribute('data-location-name') || 'Location';
                    if (buttonText) {
                        buttonText.textContent = locationName;
                    }

                    const params = new URLSearchParams();
                    if (currentUrl.searchParams.get('category')) {
                        params.set('category', currentUrl.searchParams.get('category'));
                    }
                    if (linkUrl.searchParams.get('barangay')) {
                        params.set('barangay', linkUrl.searchParams.get('barangay'));
                    }

                    showLoading();

                    try {
                        const url = '{{ route('donations.hub') }}' + (params.toString() ? '?' + params.toString() : '');
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        });
                        if (!response.ok) throw new Error('Network error');

                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newGrid = doc.getElementById('donationsGrid');
                        if (newGrid) {
                            donationsGrid.innerHTML = newGrid.innerHTML;
                        }
                    } catch (err) {
                        console.error('Error filtering donations by location:', err);
                    } finally {
                        hideLoading();
                    }

                    const newUrl = new URL(window.location);
                    if (linkUrl.searchParams.get('barangay')) {
                        newUrl.searchParams.set('barangay', linkUrl.searchParams.get('barangay'));
                    } else {
                        newUrl.searchParams.delete('barangay');
                    }
                    if (currentUrl.searchParams.get('category')) {
                        newUrl.searchParams.set('category', currentUrl.searchParams.get('category'));
                    }
                    window.history.replaceState({}, '', newUrl);
                });
            });
        });
    </script>
</x-app-layout>
