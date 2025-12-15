<div class="py-12 bg-[#F4F2ED] dark:from-gray-900 dark:via-gray-800 dark:to-black min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-10 animate-fade-in">
            <div class="flex flex-col items-center justify-center gap-5 mb-6">
                
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#B59F84] to-[#8A7B66] blur-3xl opacity-30 rounded-full w-32 h-32"></div>
                    
                    <div class="relative w-20 h-20 bg-gradient-to-br from-[#B59F84] to-[#8A7B66] rounded-3xl flex items-center justify-center shadow-2xl ring-8 ring-white/50">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <h2 class="text-5xl font-extrabold text-[#634600] dark:text-[#B59F84] mb-3 leading-tight">
                        Eco Educational Portal
                    </h2>
                    <p class="text-lg text-[#603E14] dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
                        Share knowledge, inspire change, and build a sustainable future together
                    </p>
                </div>
            </div>

            <div class="flex justify-center gap-8 mt-8">
                <div class="group text-center transform transition-all duration-300 hover:scale-110">
                    <div class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-[#634600] to-[#8A7B66]">
                        {{ $posts->count() }}
                    </div>
                    <div class="text-sm text-[#8A7B66] dark:text-gray-400 font-medium">Community Posts</div>
                </div>

                <div class="group text-center transform transition-all duration-300 hover:scale-110">
                    <div class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-[#8A7B66] to-[#B59F84]">
                        {{ $posts->unique('user_id')->count() }}
                    </div>
                    <div class="text-sm text-[#8A7B66] dark:text-gray-400 font-medium">Active Contributors</div>
                </div>
            </div>
        </div>