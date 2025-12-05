@props([
    'totalListings' => 0,
    'itemsSold' => 0,
    'itemsDonated' => 0,
    'revenue' => 0,
    'approvedWorks' => 0,
    'completedAppointmentsCount' => 0,
    'completedAppointmentsAsUpcyclerCount' => 0,
    'user'
])

@php
    $isUpcycler = $user->isUpcycler(); // or $user->hasRole('upcycler')
@endphp

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Dashboard</h2>
        <p class="text-gray-500 dark:text-gray-400 mt-2">Welcome back! Here's your overview.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <select id="timePeriodSelect" class="text-sm rounded-lg border border-gray-300 bg-white dark:bg-gray-800 px-3 py-2 dark:text-gray-200">
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
            <option value="all" selected>All time</option>
        </select>
    </div>
</div>

<!-- Desktop Cards -->
<div id="dashboardStats" class="hidden sm:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @if(!$isUpcycler)
        <!-- Normal User -->
        <div class="stat-card group"><div class="stat-icon-wrapper"><div class="stat-icon bg-blue-50 dark:bg-blue-900/20 text-blue-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div><div class="stat-glow bg-blue-500/10"></div></div><div class="stat-content"><h3 class="stat-label">Total Listings</h3><p class="stat-value" id="stat-totalListings">{{ $totalListings }}</p></div></div>

        <div class="stat-card group"><div class="stat-icon-wrapper"><div class="stat-icon bg-green-50 dark:bg-green-900/20 text-green-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg></div><div class="stat-glow bg-green-500/10"></div></div><div class="stat-content"><h3 class="stat-label">Items Sold</h3><p class="stat-value" id="stat-itemsSold">{{ $itemsSold }}</p></div></div>

        <div class="stat-card group"><div class="stat-icon-wrapper"><div class="stat-icon bg-purple-50 dark:bg-purple-900/20 text-purple-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div><div class="stat-glow bg-purple-500/10"></div></div><div class="stat-content"><h3 class="stat-label">Items Donated</h3><p class="stat-value" id="stat-itemsDonated">{{ $itemsDonated }}</p></div></div>

        <div class="stat-card group"><div class="stat-icon-wrapper"><div class="stat-icon bg-amber-50 dark:bg-amber-900/20 text-amber-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="stat-glow bg-amber-500/10"></div></div><div class="stat-content"><h3 class="stat-label">Revenue</h3><p class="stat-value" id="stat-revenue">₱{{ number_format($revenue, 2) }}</p></div></div>
    @else
        <!-- Upcycler Only -->
        <div class="stat-card group"><div class="stat-icon-wrapper"><div class="stat-icon bg-pink-50 dark:bg-pink-900/20 text-pink-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="stat-glow bg-pink-500/10"></div></div><div class="stat-content"><h3 class="stat-label">Approved Works</h3><p class="stat-value" id="stat-approvedWorks">{{ $approvedWorks }}</p></div></div>

        {{-- <div class="stat-card group"><div class="stat-icon-wrapper"><div class="stat-icon bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div><div class="stat-glow bg-indigo-500/10"></div></div><div class="stat-content"><h3 class="stat-label">Completed Appointments</h3><p class="stat-value">{{ $completedAppointmentsCount }}</p></div></div> --}}

        <div class="stat-card group"><div class="stat-icon-wrapper"><div class="stat-icon bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg></div><div class="stat-glow bg-emerald-500/10"></div></div><div class="stat-content"><h3 class="stat-label">Completed Appointments</h3><p class="stat-value" id="stat-completedAppointmentsAsUpcyclerCount">{{ $completedAppointmentsAsUpcyclerCount }}</p></div></div>
    @endif
</div>

<!-- Mobile View -->
<div class="sm:hidden space-y-4">
    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
        <span id="mobileTimePeriod" class="text-sm font-medium text-gray-600 dark:text-gray-400">All time</span>
    </div>
    <div id="mobileStats" class="space-y-3">
        @if(!$isUpcycler)
            <div class="flex justify-between py-3"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Listings</span><span id="mobile-totalListings" class="text-lg font-semibold">{{ $totalListings }}</span></div>
            <div class="flex justify-between py-3"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Items Sold</span><span id="mobile-itemsSold" class="text-lg font-semibold">{{ $itemsSold }}</span></div>
            <div class="flex justify-between py-3"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Items Donated</span><span id="mobile-itemsDonated" class="text-lg font-semibold">{{ $itemsDonated }}</span></div>
            <div class="flex justify-between py-3"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Revenue</span><span id="mobile-revenue" class="text-lg font-semibold">₱{{ number_format($revenue, 2) }}</span></div>
        @else
            <div class="flex justify-between py-3"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Approved Works</span><span id="mobile-approvedWorks" class="text-lg font-semibold">{{ $approvedWorks }}</span></div>
            {{-- <div class="flex justify-between py-3"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Completed Appointments</span><span class="text-lg font-semibold">{{ $completedAppointmentsCount }}</span></div> --}}
            <div class="flex justify-between py-3"><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Completed Appointments</span><span id="mobile-completedAppointmentsAsUpcyclerCount" class="text-lg font-semibold">{{ $completedAppointmentsAsUpcyclerCount }}</span></div>
        @endif
    </div>
</div>
@push('styles')
<style>
    /* Desktop Styles */
    .stat-card {
        @apply bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 p-6 transition-all duration-500 ease-out relative overflow-hidden;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    }
    
    .stat-card::before {
        content: '';
        @apply absolute inset-0 bg-gradient-to-br from-white/50 to-transparent dark:from-gray-800/50 opacity-0 transition-opacity duration-500;
    }
    
    .stat-card:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 4px 10px -2px rgba(0, 0, 0, 0.04);
        @apply transform -translate-y-2 border-gray-300/80 dark:border-gray-600/80;
    }
    
    .stat-card:hover::before {
        @apply opacity-100;
    }
    
    .stat-icon-wrapper {
        @apply relative mb-4;
    }
    
    .stat-icon {
        @apply w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-500 relative z-10;
        box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.08);
    }
    
    .stat-glow {
        @apply absolute -inset-2 rounded-2xl opacity-0 blur-md transition-all duration-500;
    }
    
    .stat-card:hover .stat-glow {
        @apply opacity-100;
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.05);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.12);
    }
    
    .stat-label {
        @apply text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2 tracking-wide uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
    }
    
    .stat-value {
        @apply text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 transition-colors duration-300;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .stat-card:hover .stat-value {
        @apply text-gray-800 dark:text-white;
    }

    /* Mobile Styles */
    .mobile-stat-item {
        @apply border-b border-gray-100 dark:border-gray-800 last:border-b-0;
    }

    .mobile-stat-item:last-child {
        @apply border-b-0;
    }

    /* Enhanced animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .stat-card {
        animation: fadeInUp 0.6s cubic-bezier(0.22, 0.61, 0.36, 1) forwards;
        opacity: 0;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    
    /* Mobile animations */
    @keyframes mobileSlideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .mobile-stat-item {
        animation: mobileSlideIn 0.4s ease-out forwards;
        opacity: 0;
    }
    
    .mobile-stat-item:nth-child(1) { animation-delay: 0.1s; }
    .mobile-stat-item:nth-child(2) { animation-delay: 0.2s; }
    .mobile-stat-item:nth-child(3) { animation-delay: 0.3s; }
    .mobile-stat-item:nth-child(4) { animation-delay: 0.4s; }
    
    /* Smooth dark mode transitions */
    .dashboard-stats * {
        @apply transition-colors duration-300;
    }

    /* Optimize for reduced motion */
    @media (prefers-reduced-motion: reduce) {
        .stat-card,
        .mobile-stat-item {
            animation: none;
            opacity: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timePeriodSelect = document.getElementById('timePeriodSelect');
        const userId = @json($user->id);
        const isUpcycler = @json($isUpcycler);
        
        if (!timePeriodSelect) return;
        
        let isProcessing = false;
        
        // Function to fetch and update stats
        function fetchDashboardStats() {
            // Prevent multiple simultaneous requests
            if (isProcessing) return;
            
            const period = timePeriodSelect.value;
            const periodText = timePeriodSelect.options[timePeriodSelect.selectedIndex].text;
            
            isProcessing = true;
            
            // Show loading state
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach(el => {
                el.style.opacity = '0.5';
            });
            
            // Update mobile time period text
            const mobileTimePeriod = document.getElementById('mobileTimePeriod');
            if (mobileTimePeriod) {
                mobileTimePeriod.textContent = periodText;
            }
            
            // Make AJAX request
            fetch(`/profile/${userId}/dashboard-stats?period=${period}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Update desktop stats
                if (!isUpcycler) {
                    updateElement('stat-totalListings', data.totalListings);
                    updateElement('stat-itemsSold', data.itemsSold);
                    updateElement('stat-itemsDonated', data.itemsDonated);
                    updateElement('stat-revenue', '₱' + parseFloat(data.revenue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    
                    // Update mobile stats
                    updateElement('mobile-totalListings', data.totalListings);
                    updateElement('mobile-itemsSold', data.itemsSold);
                    updateElement('mobile-itemsDonated', data.itemsDonated);
                    updateElement('mobile-revenue', '₱' + parseFloat(data.revenue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                } else {
                    updateElement('stat-approvedWorks', data.approvedWorks);
                    updateElement('stat-completedAppointmentsAsUpcyclerCount', data.completedAppointmentsAsUpcyclerCount);
                    
                    // Update mobile stats
                    updateElement('mobile-approvedWorks', data.approvedWorks);
                    updateElement('mobile-completedAppointmentsAsUpcyclerCount', data.completedAppointmentsAsUpcyclerCount);
                }
                
                // Restore opacity
                statValues.forEach(el => {
                    el.style.opacity = '1';
                });
                
                isProcessing = false;
            })
            .catch(error => {
                console.error('Error fetching dashboard stats:', error);
                // Restore opacity on error
                statValues.forEach(el => {
                    el.style.opacity = '1';
                });
                isProcessing = false;
            });
        }
        
        // Handle change event (when value actually changes)
        timePeriodSelect.addEventListener('change', function() {
            fetchDashboardStats();
        });
        
        // Track initial selection before dropdown opens
        let initialIndex = timePeriodSelect.selectedIndex;
        
        // When dropdown is clicked, store the initial selection
        timePeriodSelect.addEventListener('focus', function() {
            initialIndex = this.selectedIndex;
        });
        
        // When dropdown closes, check if same option was selected
        timePeriodSelect.addEventListener('blur', function() {
            setTimeout(() => {
                // If same option was selected (clicked but didn't change), still fetch
                if (this.selectedIndex === initialIndex) {
                    fetchDashboardStats();
                }
            }, 100);
        });
        
        // Also handle input event as additional fallback
        timePeriodSelect.addEventListener('input', function() {
            fetchDashboardStats();
        });
        
        function updateElement(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        }
    });
</script>
@endpush