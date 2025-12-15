<x-app-layout>
    
    <div x-data="{ showPostModal: false }" class="relative min-h-screen bg-gray-50 dark:bg-gray-900">

        @include('eco-posts.partials._header')
        @include('eco-posts.partials._messages')

        <div class="container mx-auto px-4 py-8">
            <div class="grid lg:grid-cols-5 gap-8 items-start">
                
                @include('eco-posts.partials._sidebar')

                <div class="lg:col-span-3">
                    @include('eco-posts.partials._feed')
                </div>

            </div>
        </div>

        @include('eco-posts.partials._fab')
        @include('eco-posts.partials._modal')

    </div>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
    </style>

</x-app-layout>