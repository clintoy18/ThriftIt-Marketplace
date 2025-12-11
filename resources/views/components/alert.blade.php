@props(['message', 'type' => 'success'])

@if ($message)
    @php
        $colors = [
            'success' => 'border-green-500 text-green-700 bg-white',
            'error' => 'border-red-500 text-red-700 bg-white',
            'warning' => 'border-yellow-500 text-yellow-700 bg-white',
        ][$type] ?? 'border-gray-500 text-gray-700 bg-white';

        $iconColor = [
            'success' => 'text-green-500',
            'error' => 'text-red-500',
            'warning' => 'text-yellow-500',
        ][$type] ?? 'text-gray-500';
    @endphp

    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)" 
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-24 left-1/2 transform -translate-x-1/2 z-[2000] max-w-lg w-full rounded-lg shadow-md border-l-4 {{ $colors }} flex items-center pr-10 py-3 px-4 mx-4">
        
        <div class="flex-shrink-0 {{ $iconColor }}">
            @if($type === 'success')
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" /><circle cx="12" cy="12" r="9" /></svg>
            @elseif($type === 'error')
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            @endif
        </div>

        <div class="ml-3 truncate font-medium">
            {{ $message }}
        </div>

        <button @click="show = false" class="absolute top-2 right-2 p-1 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
@endif

{{-- Styles --}}
<style>
@keyframes slideDown {
    from { opacity: 0; transform: translate(-50%, -10px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slide-down {
    animation: slideDown 0.4s ease-out;
}
</style>

{{-- Script --}}
<script>
function closeBanner(button) {
    const banner = button.closest("div[id^='alert-banner']");
    if (banner) {
        banner.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        banner.style.opacity = "0";
        banner.style.transform = "translate(-50%, -20px)";
        setTimeout(() => banner.remove(), 500);
    }
}

// Auto-hide after 5 seconds
document.querySelectorAll("div[id^='alert-banner']").forEach(banner => {
    setTimeout(() => {
        const btn = banner.querySelector("button");
        if (btn) closeBanner(btn);
    }, 5000);
});
</script>
