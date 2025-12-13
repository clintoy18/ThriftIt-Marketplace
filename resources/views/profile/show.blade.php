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
                @include('profile.partials._tab_products')
                @include('profile.partials._tab_reviews')

                @if ($user->isUpcycler())
                    @include('profile.partials._tab_works')
                @endif

                {{-- Only show Orders for NON-Upcyclers (even if they own the profile) --}}
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
                0%, 100% { transform: scale(1) }
                50% { transform: scale(0.95) }
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
                    orders: document.getElementById('tab-orders')
                };

                const sections = {
                    products: document.getElementById('products'),
                    reviews: document.getElementById('reviews'),
                    works: document.getElementById('works'),
                    orders: document.getElementById('orders')
                };

                // Added 'shouldScroll' parameter (default true)
                function activate(tabKey, shouldScroll = true) {
                    // 1. Hide all sections
                    Object.values(sections).forEach(sec => {
                        if(sec) sec.classList.add('hidden');
                    });

                    // 2. Reset all button styles
                    Object.values(tabs).forEach(btn => {
                        if(btn) btn.classList.remove('bg-[#E1D5B6]', 'font-semibold', 'shadow-md');
                    });

                    const btn = tabs[tabKey];
                    const sec = sections[tabKey];

                    // Guard clause: if tab/section doesn't exist (e.g. orders on another user's profile), stop.
                    if (!btn || !sec) return;

                    // 3. Show active section & style button
                    sec.classList.remove('hidden');
                    btn.classList.add('bg-[#E1D5B6]', 'font-semibold', 'shadow-md');

                    // 4. Scroll ONLY if requested (disabled on initial load)
                    if (shouldScroll) {
                        sec.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }

                // Click handlers (Keep scrolling enabled for manual clicks)
                tabs.products?.addEventListener('click', () => activate('products'));
                tabs.reviews?.addEventListener('click', () => activate('reviews'));
                tabs.works?.addEventListener('click', () => activate('works'));
                tabs.orders?.addEventListener('click', () => activate('orders'));

                // --- LOGIC: Handle URL Params & Default State ---
                
                // 1. Check for ?tab=xyz in the URL
                const urlParams = new URLSearchParams(window.location.search);
                const requestedTab = urlParams.get('tab');

                // 2. Determine User Role
                const isUpcycler = @json($user->isUpcycler());
                
                // 3. Decide which tab to open
                let activeTab = 'products'; // Default fallback

                if (requestedTab && sections[requestedTab]) {
                    // If URL has a valid tab (e.g. ?tab=orders), use it
                    activeTab = requestedTab;
                } else {
                    // Otherwise use role defaults
                    activeTab = isUpcycler ? 'works' : 'products';
                }

                // 4. Activate without scrolling
                activate(activeTab, false); 
            });
        </script>
    @endpush

</x-app-layout>