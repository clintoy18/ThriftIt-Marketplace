<x-app-layout>
    <div class="py-6 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{}">

            {{-- ===== HEADER & AVATAR ===== --}}
            @include('profile.partials._header')

            {{-- ===== USER INFO (Name, Points, Actions) ===== --}}
            @include('profile.partials._user_info')

            {{-- ===== OWNER DASHBOARD ===== --}}
            @if (Auth::id() === $user->id)
                @include('profile.partials._dashboard')
            @endif

            {{-- ===== TABS ===== --}}
            @include('profile.partials._tabs')

            {{-- ===== TAB CONTENTS ===== --}}
            <div class="mt-6 space-y-12">
                
                {{-- 1. PRODUCTS (Visible to All) --}}
                @include('profile.partials._tab_products')

                @if (!$user->isUpcycler())
                    @include('profile.partials._tab_donations')
                @endif

                {{-- 3. REVIEWS (Visible to All) --}}
                @include('profile.partials._tab_reviews')

                {{-- 4. WORKS (Upcyclers Only) --}}
                @if ($user->isUpcycler())
                    @include('profile.partials._tab_works')
                @endif

                {{-- 5. ORDERS (Owner & Non-Upcycler Only) --}}
                @if (Auth::id() === $user->id && !$user->isUpcycler())
                    @include('profile.partials._tab_orders')
                @endif

            </div>

            {{-- ===== MODALS ===== --}}
            <x-review-modal :user="$user" />
            <x-report-modal :user="$user" />
        </div>
    </div>

    {{-- ===== STYLES ===== --}}
    @push('styles')
        <style>
            html {
                scroll-behavior: smooth;
            }

            .transition-all {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .group:hover .group-hover\:scale-105 {
                transform: scale(1.05);
            }

            @keyframes pulse {

                0%,
                100% {
                    transform: scale(1)
                }

                50% {
                    transform: scale(0.95)
                }
            }

            .button-click {
                animation: pulse 0.3s ease-in-out;
            }
        </style>
    @endpush

    {{-- ===== SCRIPTS (Handles all tabs dynamically) ===== --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabs = {
                    products: document.getElementById('tab-products'),
                    reviews: document.getElementById('tab-reviews'),
                    works: document.getElementById('tab-works'),
                    orders: document.getElementById('tab-orders'),
                    donations: document.getElementById('tab-donations')
                };

                const sections = {
                    products: document.getElementById('products'),
                    reviews: document.getElementById('reviews'),
                    works: document.getElementById('works'),
                    orders: document.getElementById('orders'),
                    donations: document.getElementById('donations')
                };

                function activate(tabKey, shouldScroll = true) {
                    // 1. Hide all sections
                    Object.values(sections).forEach(sec => {
                        if (sec) sec.classList.add('hidden');
                    });

                    // 2. Reset all button styles
                    Object.values(tabs).forEach(btn => {
                        if (btn) btn.classList.remove('bg-[#E1D5B6]', 'font-semibold', 'shadow-md');
                    });

                    const btn = tabs[tabKey];
                    const sec = sections[tabKey];

                    // Guard clause if tab/section doesn't exist
                    if (!btn || !sec) return;

                    // 3. Show active section & style button
                    sec.classList.remove('hidden');
                    btn.classList.add('bg-[#E1D5B6]', 'font-semibold', 'shadow-md');

                    // 4. Scroll ONLY if requested
                    if (shouldScroll) {
                        document.getElementById('profile-tabs-container')?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }

                // Click handlers
                tabs.products?.addEventListener('click', () => activate('products', true));
                tabs.reviews?.addEventListener('click', () => activate('reviews', true));
                tabs.works?.addEventListener('click', () => activate('works', true));
                tabs.orders?.addEventListener('click', () => activate('orders', true));
                tabs.donations?.addEventListener('click', () => activate('donations', true));

                // --- INITIALIZATION LOGIC ---

                const urlParams = new URLSearchParams(window.location.search);
                const requestedTab = urlParams.get('tab');
                const isUpcycler = @json($user->isUpcycler());

                let activeTab = 'products'; // Default fallback
                let shouldScrollOnLoad = false;

                if (requestedTab && sections[requestedTab]) {
                    // If specifically requested via URL (e.g. notifications)
                    activeTab = requestedTab;
                    shouldScrollOnLoad = true;
                } else {
                    // Default Landing Tabs
                    // If Upcycler, default to Works. Everyone else default to Products.
                    activeTab = isUpcycler ? 'works' : 'products';
                    shouldScrollOnLoad = false;
                }

                activate(activeTab, shouldScrollOnLoad);
            });
        </script>
    @endpush
</x-app-layout>