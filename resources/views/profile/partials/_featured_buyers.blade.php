<section id="featuredBuyersSection" class="mt-8">
    <!-- Header Section -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Featured Buyers
                </h2>
                <span class="flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-gradient-to-r from-emerald-100 to-emerald-50 text-emerald-700 dark:from-emerald-900/30 dark:to-emerald-900/10 dark:text-emerald-400 font-medium shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Consensual Feature
                </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Influencers & celebrities who shop sustainable fashion here.
            </p>
        </div>

        @if (Auth::id() === $user->id)
            <button @click="$dispatch('open-buyer-modal')" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Celebrity Buyer
            </button>
        @endif
    </div>

    <!-- Featured Buyers Content Wrapper -->
    <div id="featuredBuyersWrapper">
        <!-- Loading Indicator -->
        <div id="featuredBuyersLoadingIndicator" class="hidden flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
            <span class="ml-2 text-gray-600 dark:text-gray-300">Loading featured buyers...</span>
        </div>

        <!-- Buyers Grid -->
        <div id="featuredBuyersGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredBuyers as $buyer)
            <!-- Main Card Container -->
            <div class="group relative bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300 shadow-sm hover:shadow-lg">
                
                <!-- Full Background Product Display -->
                <div id="background-product-{{ $loop->index }}" 
                     class="absolute inset-0 overflow-hidden z-0 opacity-0 transition-opacity duration-700">
                    <!-- Background product image will be set as background image -->
                    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-1000"
                         id="background-product-image-{{ $loop->index }}"
                         style="filter: blur(2px) brightness(0.8); transform: scale(1.1);">
                    </div>
                    <!-- Overlay to darken background for better text readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/20 to-transparent"></div>
                    <!-- Additional gradient overlay for depth -->
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-500/5 mix-blend-overlay"></div>
                </div>
                
                <!-- Content Overlay Section -->
                <div class="relative px-5 pb-5 pt-8 z-10">
                    <!-- Avatar Section with glow effect -->
                    <div class="mb-4 relative">
                        <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500/30 to-purple-500/30 rounded-full blur-md opacity-0 group-hover:opacity-70 transition-opacity duration-500"></div>
                        @if($buyer->avatar_path)
                            <img src="{{ Storage::disk('s3')->url($buyer->avatar_path) }}" 
                                class="relative h-20 w-20 rounded-full object-cover border-4 border-white/80 dark:border-gray-800/80 shadow-xl group-hover:scale-105 transition-transform duration-300 backdrop-blur-sm"
                                alt="{{ $buyer->name }}">
                        @else
                            <div class="relative h-20 w-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-2xl border-4 border-white/80 dark:border-gray-800/80 shadow-xl group-hover:scale-105 transition-transform duration-300 backdrop-blur-sm">
                                {{ substr($buyer->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Name and Verified -->
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate drop-shadow-sm">
                            {{ $buyer->name }}
                        </h3>
                        @if($buyer->is_verified)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400 flex-shrink-0 drop-shadow-sm" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                    
                    <p class="text-sm text-indigo-200 dark:text-indigo-300 font-medium truncate mb-3 drop-shadow-sm">
                        {{ $buyer->handle ?? '@celebrity' }}
                    </p>

                    <!-- Bio/Description -->
                    <p class="text-sm text-gray-800 dark:text-gray-200 line-clamp-2 leading-relaxed mb-6 drop-shadow-sm">
                        {{ $buyer->bio }}
                    </p>

                    <!-- Purchased Items Swiper -->
                    @if($buyer->items && count($buyer->items) > 0)
                        <div class="pt-4 border-t border-white/30 dark:border-gray-700/50">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider drop-shadow-sm">
                                    Purchased Items ({{ count($buyer->items) }})
                                </p>
                                <div class="flex gap-1">
                                    <button class="swiper-prev-{{ $loop->index }} p-1.5 rounded-full bg-white/50 dark:bg-gray-800/50 hover:bg-white/80 dark:hover:bg-gray-700/80 backdrop-blur-sm transition-all duration-300 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-800 dark:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button class="swiper-next-{{ $loop->index }} p-1.5 rounded-full bg-white/50 dark:bg-gray-800/50 hover:bg-white/80 dark:hover:bg-gray-700/80 backdrop-blur-sm transition-all duration-300 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-800 dark:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Swiper Container -->
                            <div class="swiper-container buyer-swiper-{{ $loop->index }} relative overflow-hidden rounded-lg backdrop-blur-sm">
                                <div class="swiper-wrapper">
                                    @foreach($buyer->items as $item)
                                        <div class="swiper-slide" 
                                             data-product-image="{{ Storage::disk('s3')->url($item->image_path) }}"
                                             data-product-name="{{ $item->product_name }}">
                                            <div class="group/item relative bg-white/90 dark:bg-gray-900/90 rounded-lg overflow-hidden border border-white/50 dark:border-gray-700/50 hover:border-indigo-300/80 dark:hover:border-indigo-600/80 transition-all duration-300 backdrop-blur-sm">
                                                <div class="aspect-square bg-gradient-to-br from-gray-100/80 to-gray-200/80 dark:from-gray-800/80 dark:to-gray-700/80 overflow-hidden">
                                                    <img src="{{ Storage::disk('s3')->url($item->image_path) }}" 
                                                         class="w-full h-full object-cover group-hover/item:scale-110 transition-transform duration-500"
                                                         alt="{{ $item->product_name }}">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover/item:opacity-100 transition-opacity duration-300"></div>
                                                </div>
                                                <div class="p-3">
                                                    <p class="text-xs font-semibold text-gray-900 dark:text-white truncate mb-1 drop-shadow-sm">{{ $item->product_name }}</p>
                                                    <p class="text-xs font-semibold text-gray-900 dark:text-white truncate drop-shadow-sm">₱{{ number_format($item->price, 2) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="col-span-full">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg p-12 text-center border border-gray-200 dark:border-gray-700">
                        <div class="h-24 w-24 mx-auto rounded-full bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-indigo-900/20 dark:to-purple-900/20 flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                            No Featured Buyers Yet
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-md mx-auto">
                            Showcase celebrities and influencers who love your brand. Their purchases inspire others to shop with you.
                        </p>
                        @if (Auth::id() === $user->id)
                            <button @click="$dispatch('open-buyer-modal')" 
                                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add First Celebrity Buyer
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($featuredBuyers->hasPages())
            <div id="featuredBuyersPagination" class="mt-8 flex justify-center">
                {{ $featuredBuyers->links('pagination.featured-buyers') }}
            </div>
        @endif
    </div>
</section>

<!-- Add this script at the end of your file -->
<script>
// Function to initialize Swipers for featured buyers
function initializeFeaturedBuyerSwipers() {
    // Clear existing Swiper instances if any
    if (window.featuredBuyerSwipers) {
        window.featuredBuyerSwipers.forEach(swiper => {
            if (swiper && swiper.destroy) {
                swiper.destroy(true, true);
            }
        });
    }
    window.featuredBuyerSwipers = [];

    // Initialize new Swiper instances
    const buyerCards = document.querySelectorAll('#featuredBuyersGrid [class*="buyer-swiper-"]');
    buyerCards.forEach((container, index) => {
        // Extract the swiper class name (e.g., "buyer-swiper-0")
        const classList = Array.from(container.classList);
        const swiperClass = classList.find(cls => cls.startsWith('buyer-swiper-'));
        
        if (swiperClass && typeof Swiper !== 'undefined') {
            const swiperIndex = swiperClass.replace('buyer-swiper-', '');
            try {
                const swiper = new Swiper(`.${swiperClass}`, {
                    slidesPerView: 2,
                    spaceBetween: 12,
                    loop: false,
                    navigation: {
                        nextEl: `.swiper-next-${swiperIndex}`,
                        prevEl: `.swiper-prev-${swiperIndex}`,
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2,
                            spaceBetween: 12,
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 12,
                        },
                    },
                    on: {
                        // When slide changes, update the background image
                        slideChange: function() {
                            const activeSlide = this.slides[this.activeIndex];
                            const productImage = activeSlide.getAttribute('data-product-image');
                            const productName = activeSlide.getAttribute('data-product-name');
                            
                            // Get the background container
                            const backgroundContainer = document.getElementById(`background-product-${swiperIndex}`);
                            const backgroundImage = document.getElementById(`background-product-image-${swiperIndex}`);
                            
                            if (backgroundContainer && backgroundImage && productImage) {
                                // Fade out current background
                                backgroundContainer.style.opacity = '0';
                                
                                // Wait for fade out, then change background and fade in
                                setTimeout(() => {
                                    // Set the background image to cover the entire container
                                    backgroundImage.style.backgroundImage = `url('${productImage}')`;
                                    backgroundImage.style.backgroundSize = 'cover';
                                    backgroundImage.style.backgroundPosition = 'center';
                                    backgroundImage.style.backgroundRepeat = 'no-repeat';
                                    
                                    // Reset scale for animation
                                    backgroundImage.style.transform = 'scale(1.1)';
                                    
                                    // Fade in with smooth animation
                                    setTimeout(() => {
                                        backgroundContainer.style.opacity = '1';
                                        // Add subtle zoom animation
                                        setTimeout(() => {
                                            backgroundImage.style.transform = 'scale(1.05)';
                                        }, 100);
                                    }, 200);
                                }, 300);
                            }
                        },
                        // When swiper is initialized, set initial background
                        init: function() {
                            const activeSlide = this.slides[this.activeIndex];
                            if (activeSlide) {
                                const productImage = activeSlide.getAttribute('data-product-image');
                                const productName = activeSlide.getAttribute('data-product-name');
                                const backgroundImage = document.getElementById(`background-product-image-${swiperIndex}`);
                                
                                if (backgroundImage && productImage) {
                                    // Set initial background
                                    backgroundImage.style.backgroundImage = `url('${productImage}')`;
                                    backgroundImage.style.backgroundSize = 'cover';
                                    backgroundImage.style.backgroundPosition = 'center';
                                    backgroundImage.style.backgroundRepeat = 'no-repeat';
                                    
                                    // Show background after a small delay
                                    setTimeout(() => {
                                        const backgroundContainer = document.getElementById(`background-product-${swiperIndex}`);
                                        if (backgroundContainer) {
                                            backgroundContainer.style.opacity = '1';
                                            // Add subtle animation
                                            setTimeout(() => {
                                                backgroundImage.style.transform = 'scale(1.05)';
                                            }, 300);
                                        }
                                    }, 500);
                                }
                            }
                        }
                    }
                });
                window.featuredBuyerSwipers.push(swiper);
            } catch (error) {
                console.warn('Error initializing Swiper:', error);
            }
        }
    });
}

// Function to load featured buyers via AJAX
function loadFeaturedBuyers(url) {
    const loadingIndicator = document.getElementById('featuredBuyersLoadingIndicator');
    const buyersGrid = document.getElementById('featuredBuyersGrid');
    const pagination = document.getElementById('featuredBuyersPagination');

    // Show loading indicator
    if (loadingIndicator) {
        loadingIndicator.classList.remove('hidden');
    }
    if (buyersGrid) {
        buyersGrid.style.opacity = '0.5';
    }

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Find the featured buyers section in the response
        const newSection = doc.querySelector('#featuredBuyersWrapper');
        if (newSection) {
            const newGrid = newSection.querySelector('#featuredBuyersGrid');
            const newPagination = newSection.querySelector('#featuredBuyersPagination');
            
            // Update grid
            if (newGrid && buyersGrid) {
                buyersGrid.innerHTML = newGrid.innerHTML;
            }
            
            // Update pagination
            if (pagination) {
                if (newPagination) {
                    pagination.innerHTML = newPagination.innerHTML;
                } else {
                    pagination.innerHTML = '';
                }
            }
            
            // Reinitialize Swipers
            initializeFeaturedBuyerSwipers();
            
            // Reattach pagination event listeners
            attachFeaturedBuyersPaginationListeners();
        }
    })
    .catch(error => {
        console.error('Error loading featured buyers:', error);
    })
    .finally(() => {
        // Hide loading indicator
        if (loadingIndicator) {
            loadingIndicator.classList.add('hidden');
        }
        if (buyersGrid) {
            buyersGrid.style.opacity = '1';
        }
    });
}

// Function to attach pagination event listeners
function attachFeaturedBuyersPaginationListeners() {
    const pagination = document.getElementById('featuredBuyersPagination');
    if (!pagination) return;

    pagination.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            if (url) {
                loadFeaturedBuyers(url);
            }
        });
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swipers for initial load
    initializeFeaturedBuyerSwipers();
    
    // Attach pagination listeners
    attachFeaturedBuyersPaginationListeners();
});
</script>

<!-- Add Swiper CSS -->
<style>
.swiper-container {
    width: 100%;
    height: auto;
    padding: 5px 0;
}

.swiper-slide {
    height: auto;
}

/* Background product animation */
[id^="background-product-image-"] {
    transition: transform 3s cubic-bezier(0.2, 0.8, 0.4, 1), opacity 0.7s ease;
}

[id^="background-product-"] {
    transition: opacity 0.7s ease;
}
</style>