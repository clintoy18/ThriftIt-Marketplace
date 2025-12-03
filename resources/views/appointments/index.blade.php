<x-app-layout>
    <!-- Hero Section -->
    <section class="w-full bg-[#F4F2ED] dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">

            <!-- Mobile Layout -->
            <div class="flex flex-col md:hidden text-center relative font-poppins">
                <h1 class="text-3xl font-extrabold text-green-700 leading-tight dark:text-green-400">
                    Upcycle with Experts!
                </h1>
                <p class="mt-2 text-lg text-custom-brown dark:text-gray-200 mb-6">
                    Transform your old items into something new ✂️
                </p>

                <!-- Buttons -->
                <div class="flex flex-col gap-3 mb-8">
                    <a href="#upcyclers"
                        class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                        Find an Upcycler
                    </a>
                    <a href="{{ route('eco-posts.index') }}"
                        class="inline-block border border-green-600 text-green-700 hover:bg-green-50 dark:border-green-400 dark:text-green-400 dark:hover:bg-gray-700 font-semibold px-6 py-3 rounded-full shadow-md transition">
                        Join Our Community
                    </a>
                </div>
            </div>

            <!-- Desktop Layout -->
            <div class="hidden md:flex md:flex-row md:items-center gap-8">
                <!-- Text Content -->
                <div class="md:w-1/2 font-poppins">
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-green-700 dark:text-green-400 leading-tight">
                        Upcycle with Experts!
                    </h1>
                    <p class="mt-4 text-xl text-custom-brown dark:text-gray-200">
                        Give your old items a new purpose ♻️
                    </p>

                    <!-- Buttons -->
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="#upcyclers"
                            class="inline-block border border-green-600 text-green-700 hover:bg-green-50 
                                  dark:border-green-400 dark:text-green-400 dark:hover:bg-gray-700 
                                  font-semibold px-6 py-3 rounded-full shadow-md transition">
                            Find an Upcycler
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
                    <img src="{{ asset('images/upcycle-fashion.png') }}" alt="Upcycle Fashion"
                        class="w-1/2 h-full object-cover rounded-xl shadow-lg hover:scale-[1.02] transition-transform duration-300">
                    <img src="{{ asset('images/upcycle-community.png') }}" alt="Upcycle Community"
                        class="w-1/2 h-full object-cover rounded-xl shadow-lg hover:scale-[1.02] transition-transform duration-300">
                </div>
            </div>
        </div>
    </section>

    <!-- Upcycler Cards -->
    <section id="upcyclers" class="py-16">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-10 text-gray-900 dark:text-white">
                Meet Our Upcyclers
            </h2>

            {{-- Location Filter (similar style to dashboard location filter) --}}
            @if(isset($barangays) && $barangays->count() > 0)
                <div class="flex justify-end mb-6">
                    <div x-data="{ open: false, locationSearch: '' }" class="relative z-[100]">
                        <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm shadow-sm z-50">
                            <span id="locationButtonText">
                                {{ isset($selectedBarangayId) && $barangays->where('id', $selectedBarangayId)->first()
                                    ? $barangays->where('id', $selectedBarangayId)->first()->name
                                    : 'Location' }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700 dark:text-gray-300"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div x-cloak x-show="open" @click.outside="open = false; locationSearch = ''"
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[101] py-1">

                            <div class="px-3 py-1">
                                <input x-model="locationSearch" placeholder="Search location..."
                                    class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="max-h-64 overflow-y-auto mt-1">
                                {{-- "All" resets the filter --}}
                                <a data-location-link data-location-name="All" href="{{ route('appointments.index') }}"
                                    class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                                    All
                                </a>
                                @foreach ($barangays as $barangay)
                                    <a x-show="locationSearch === '' || '{{ strtolower($barangay->name) }}'.includes(locationSearch.toLowerCase())"
                                        data-location-link data-location-name="{{ $barangay->name }}"
                                        href="{{ route('appointments.index', ['barangay' => $barangay->id]) }}"
                                        class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg {{ isset($selectedBarangayId) && (int) $selectedBarangayId === $barangay->id ? 'font-semibold' : '' }}">
                                        {{ $barangay->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Loading Indicator (like dashboard) -->
            <div id="loadingIndicator" class="hidden flex items-center justify-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-700"></div>
                <span class="ml-2 text-gray-600 dark:text-gray-300">Loading upcyclers...</span>
            </div>

            <!-- Upcyclers Grid -->
            <div id="upcyclersGrid" class="mt-4 relative z-10">
                @if ($upcyclers->isEmpty())
                    <x-empty-message message="We currently have no registered upcyclers. Please check back soon!"
                        link="{{ route('register') }}" buttonText="Join as an Upcycler" icon="shopping-cart" />
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($upcyclers as $upcycler)
                            <div
                                class="relative group bg-[#F4F2ED] dark:bg-gray-800/90 rounded-2xl border border-gray-200 dark:border-gray-700 shadow hover:shadow-2xl transition-all duration-300 overflow-hidden">
                                <a href="{{ route('profile.show', $upcycler->id) }}" class="absolute inset-0 z-10"></a>
                                <div
                                    class="h-20 bg-gradient-to-r from-[#E1D5B6] to-[#cbbda2] dark:from-gray-700 dark:to-gray-600">
                                </div>
                                <div class="p-6 relative z-20">
                                <a href="{{ route('profile.show', $upcycler->id) }}"
                                    class="-mt-12 mb-4 w-20 h-20 mx-auto rounded-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 flex items-center justify-center text-lg font-bold text-gray-800 dark:text-gray-200 shadow-md overflow-hidden transition-transform duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#B59F84]">
                                    <img src="{{ $upcycler->profileImageUrl() }}" alt="{{ $upcycler->name }}"
                                        class="w-full h-full object-cover rounded-full">
                                </a>

                                    <div class="text-center">
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-[#6f5e49] transition-colors">
                                            {{ $upcycler->fname }} {{ $upcycler->lname }}
                                        </h3>
                                        <div
                                            class="mt-2 flex items-center justify-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M20 4H4a2 2 0 0 0-2 2v.01L12 13l10-6.99V6a2 2 0 0 0-2-2Zm0 4.236-8 5.59-8-5.59V18a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.236Z" />
                                            </svg>
                                            <a href="mailto:{{ $upcycler->email }}"
                                                class="hover:underline">{{ $upcycler->email }}</a>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-center gap-2 flex-wrap">
                                        <span
                                            class="px-3 py-1 text-xs rounded-full bg-white text-gray-700 border border-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">Specialization</span>
                                        <span
                                            class="px-3 py-1 text-xs rounded-full bg-[#E1D5B6]/30 text-[#6f5e49] ring-1 ring-[#E1D5B6]/40">
                                            {{ $upcycler->specialization ?? 'N/A' }}
                                        </span>
                                    </div>

                                    <div class="mt-6 relative z-30">
                                        <a href="{{ route('appointments.create', ['upcycler_id' => $upcycler->id]) }}"
                                            class="w-full inline-flex items-center justify-center gap-2 bg-[#B59F84] hover:bg-[#a08e77] text-white font-semibold py-2.5 px-4 rounded-full shadow-md transition active:scale-[.98]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Request Appointment
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
    
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>

    <!-- AJAX filtering for location (similar to dashboard) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const upcyclersGrid = document.getElementById('upcyclersGrid');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const locationButtonText = document.getElementById('locationButtonText');

            if (!upcyclersGrid) return;

            function showLoading() {
                if (loadingIndicator) {
                    loadingIndicator.classList.remove('hidden');
                }
                upcyclersGrid.style.opacity = '0.5';
            }

            function hideLoading() {
                if (loadingIndicator) {
                    loadingIndicator.classList.add('hidden');
                }
                upcyclersGrid.style.opacity = '1';
            }

            document.querySelectorAll('[data-location-link]').forEach(link => {
                link.addEventListener('click', async (e) => {
                    e.preventDefault();

                    const currentUrl = new URL(window.location);
                    const linkUrl = new URL(e.currentTarget.href, window.location.origin);

                    // Update button text
                    const locationName = e.currentTarget.getAttribute('data-location-name') || 'Location';
                    if (locationButtonText) {
                        locationButtonText.textContent = locationName;
                    }

                    // Build query params for API call
                    const params = new URLSearchParams();
                    if (linkUrl.searchParams.get('barangay')) {
                        params.set('barangay', linkUrl.searchParams.get('barangay'));
                    }

                    showLoading();

                    try {
                        const appointmentsUrl = '{{ route('appointments.index') }}' + (params.toString() ?
                            '?' + params.toString() : '');
                        const response = await fetch(appointmentsUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newUpcyclersGrid = doc.getElementById('upcyclersGrid');

                        if (newUpcyclersGrid) {
                            upcyclersGrid.innerHTML = newUpcyclersGrid.innerHTML;
                        }
                    } catch (error) {
                        console.error('Error filtering upcyclers:', error);
                    } finally {
                        hideLoading();
                    }

                    // Update query string in URL (for back/forward and sharing)
                    const newUrl = new URL(window.location);
                    if (linkUrl.searchParams.get('barangay')) {
                        newUrl.searchParams.set('barangay', linkUrl.searchParams.get('barangay'));
                    } else {
                        newUrl.searchParams.delete('barangay');
                    }
                    window.history.replaceState({}, '', newUrl);
                });
            });
        });
    </script>
</x-app-layout>
