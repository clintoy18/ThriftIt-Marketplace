<x-app-layout>
    <div class="py-12 bg-[#F4F2ED] dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold text-[#634600] dark:text-[#B59F84] mb-2">
                            My Favorites
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            Your saved items for later
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-[#B59F84]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="text-2xl font-bold text-[#634600] dark:text-[#B59F84]">{{ $favorites->total() }}</span>
                    </div>
                </div>
            </div>

            {{-- Favorites Grid --}}
            @if($favorites->count() > 0)
                <div id="favoritesWrapper">
                    {{-- Top Pagination --}}
                    @if ($favorites->hasPages())
                        <div id="paginationTop" class="mb-4 flex justify-center">
                            {{ $favorites->links('pagination.tailwind-custom') }}
                        </div>
                    @endif
                    
                    {{-- Loading Indicator --}}
                    <div id="loadingIndicator" class="hidden flex items-center justify-center py-4">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#634600]"></div>
                        <span class="ml-2 text-gray-600 dark:text-gray-300">Loading favorites...</span>
                    </div>

                    {{-- Favorites Grid --}}
                    <div id="favoritesGrid" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-4 md:gap-6">
                        @foreach($favorites as $favorite)
                            <x-item-card :product="$favorite->product" />
                        @endforeach
                    </div>

                    {{-- Bottom Pagination --}}
                    @if ($favorites->hasPages())
                        <div id="paginationBottom" class="mt-6 flex justify-center">
                            {{ $favorites->links('pagination.tailwind-custom') }}
                        </div>
                    @endif
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center border border-dashed border-gray-200 dark:border-gray-700">
                    <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No favorites yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Start saving items you love by clicking the heart icon on any product.</p>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-[#B59F84] border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-[#a08e77] transition">
                        Browse Products
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const favoritesGrid = document.getElementById('favoritesGrid');
            const loadingIndicator = document.getElementById('loadingIndicator');
            
            // Initialize favorite buttons
            function initFavoriteButtons() {
                document.querySelectorAll('.favorite-btn').forEach(button => {
                    const productId = button.getAttribute('data-id');
                    if (!productId) return;

                    // Skip if already initialized
                    if (button.dataset.initialized === 'true') return;
                    button.dataset.initialized = 'true';

                    // Check initial favorite status
                    fetch(`/products/${productId}/favorite/check`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const svg = button.querySelector('svg');
                        if (data.isFavorited) {
                            svg.setAttribute('fill', 'currentColor');
                            svg.setAttribute('stroke', 'none');
                            button.classList.add('text-red-500');
                            button.classList.remove('text-gray-400');
                        } else {
                            svg.setAttribute('fill', 'none');
                            svg.setAttribute('stroke', 'currentColor');
                            button.classList.remove('text-red-500');
                            button.classList.add('text-gray-400');
                        }
                    })
                    .catch(error => console.error('Error checking favorite status:', error));

                    // Handle click
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const svg = this.querySelector('svg');
                        const isCurrentlyFavorited = svg.getAttribute('fill') === 'currentColor';

                        // Optimistic UI update
                        if (isCurrentlyFavorited) {
                            svg.setAttribute('fill', 'none');
                            svg.setAttribute('stroke', 'currentColor');
                            this.classList.remove('text-red-500');
                            this.classList.add('text-gray-400');
                        } else {
                            svg.setAttribute('fill', 'currentColor');
                            svg.setAttribute('stroke', 'none');
                            this.classList.add('text-red-500');
                            this.classList.remove('text-gray-400');
                        }

                        // Make API call
                        fetch(`/products/${productId}/favorite`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // If on favorites page and item was unfavorited, remove it
                                if (!data.isFavorited && window.location.pathname.includes('/favorites')) {
                                    const cardElement = button.closest('.group')?.parentElement || button.closest('[class*="group"]');
                                    if (cardElement) {
                                        cardElement.remove();
                                    }
                                    
                                    // Check if no items left
                                    if (favoritesGrid && favoritesGrid.children.length === 0) {
                                        location.reload();
                                    }
                                }
                            } else {
                                // Revert on error
                                if (isCurrentlyFavorited) {
                                    svg.setAttribute('fill', 'currentColor');
                                    svg.setAttribute('stroke', 'none');
                                    this.classList.add('text-red-500');
                                    this.classList.remove('text-gray-400');
                                } else {
                                    svg.setAttribute('fill', 'none');
                                    svg.setAttribute('stroke', 'currentColor');
                                    this.classList.remove('text-red-500');
                                    this.classList.add('text-gray-400');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error toggling favorite:', error);
                            // Revert on error
                            if (isCurrentlyFavorited) {
                                svg.setAttribute('fill', 'currentColor');
                                svg.setAttribute('stroke', 'none');
                                this.classList.add('text-red-500');
                                this.classList.remove('text-gray-400');
                            } else {
                                svg.setAttribute('fill', 'none');
                                svg.setAttribute('stroke', 'currentColor');
                                this.classList.remove('text-red-500');
                                this.classList.add('text-gray-400');
                            }
                        });
                    });
                });
            }

            // Load favorites via AJAX (for pagination)
            function loadFavorites(url) {
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                if (favoritesGrid) favoritesGrid.style.opacity = '0.5';

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Update favorites grid
                    const newFavoritesGrid = doc.getElementById('favoritesGrid');
                    if (newFavoritesGrid && favoritesGrid) {
                        favoritesGrid.innerHTML = newFavoritesGrid.innerHTML;
                    }

                    // Update top & bottom pagination
                    const topPagination = document.getElementById('paginationTop');
                    const bottomPagination = document.getElementById('paginationBottom');
                    const newTopPagination = doc.getElementById('paginationTop');
                    const newBottomPagination = doc.getElementById('paginationBottom');

                    if (topPagination && newTopPagination) {
                        topPagination.innerHTML = newTopPagination.innerHTML;
                    }
                    if (bottomPagination && newBottomPagination) {
                        bottomPagination.innerHTML = newBottomPagination.innerHTML;
                    }

                    // Reattach pagination links
                    attachPaginationLinks();
                    
                    // Re-initialize favorite buttons
                    initFavoriteButtons();

                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    if (favoritesGrid) favoritesGrid.style.opacity = '1';

                    // Smooth scroll to top
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                })
                .catch(error => {
                    console.error('Error loading favorites:', error);
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    if (favoritesGrid) favoritesGrid.style.opacity = '1';
                });
            }

            // Attach pagination link handlers
            function attachPaginationLinks() {
                document.querySelectorAll('#paginationTop a, #paginationBottom a').forEach(link => {
                    link.addEventListener('click', e => {
                        e.preventDefault();
                        loadFavorites(link.href);
                    });
                });
            }

            // Initialize on page load
            initFavoriteButtons();
            attachPaginationLinks();
        });
    </script>
    @endpush
</x-app-layout>

