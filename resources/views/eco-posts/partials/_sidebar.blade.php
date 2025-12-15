<div class="lg:col-span-2 space-y-8 lg:sticky lg:top-24 self-start">

    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-6 transform transition-all duration-300 hover:shadow-3xl">
        
        <div class="mb-6">
            <h4 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                Top Contributors
            </h4>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 ml-1 leading-relaxed">
                Ranked by their <span class="font-semibold text-emerald-600 dark:text-emerald-400">generous donations and upcycling</span>. Because of their high community standing, we strongly urge you to <span class="font-semibold text-emerald-600 dark:text-emerald-400">transact with these trusted members</span> for a safe experience.
            </p>
        </div>

        <div class="space-y-3">
            @forelse ($leaderboard as $user)
                @php $rank = $loop->iteration; @endphp
                
                {{-- Clickable Profile --}}
                <a href="{{ route('profile.show', $user->id) }}" 
                   class="group flex items-center gap-4 p-4 rounded-2xl cursor-pointer
                    bg-gradient-to-r {{ $rank === 1 ? 'from-amber-50 to-yellow-50 dark:from-amber-900/30 dark:to-yellow-900/20' : 'hover:from-emerald-50/50 hover:to-teal-50/50 dark:hover:from-emerald-900/20 dark:hover:to-teal-900/20' }} 
                    transition-all duration-300 border border-transparent {{ $rank === 1 ? 'border-amber-200 dark:border-amber-700' : 'hover:border-emerald-100 dark:hover:border-emerald-800' }}">

                    <div class="relative flex-shrink-0">
                        @switch($rank)
                            @case(1)
                                <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-yellow-300/50">
                                    <span class="text-white font-bold text-sm">1</span>
                                </div>
                                @break
                            @case(2)
                                <div class="w-10 h-10 bg-gradient-to-br from-gray-300 to-gray-400 rounded-full flex items-center justify-center shadow-lg ring-4 ring-gray-300/50">
                                    <span class="text-white font-bold text-sm">2</span>
                                </div>
                                @break
                            @case(3)
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-orange-300/50">
                                    <span class="text-white font-bold text-sm">3</span>
                                </div>
                                @break
                            @default
                                <div class="w-10 h-10 bg-gray-300 dark:bg-gray-700 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white font-bold text-sm">{{ $rank }}</span>
                                </div>
                        @endswitch
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                           <div class="w-7 h-7 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full overflow-hidden flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                <img src="{{ $user->profileImageUrl() }}" 
                                    alt="{{ $user->name }}" 
                                    class="w-full h-full object-cover">
                            </div>
                            <p class="font-semibold text-gray-800 dark:text-gray-100 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                 <x-user-name-badge :user="$user" />
                            </p>
                            
                            {{-- Trusted Icon --}}
                            {{-- <div class="group/tooltip relative">
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover/tooltip:block px-2 py-1 bg-gray-900 text-white text-[10px] rounded whitespace-nowrap z-50">
                                    Trusted User
                                </span>
                            </div> --}}
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                            @if($user->is_top_donor)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                    Top Donor
                                </span>
                            @endif
                            @if($user->isUpcyler)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                    {{ $user->works->count() }} Upcycles
                                </span>
                            @endif
                            <span class="text-[10px] text-gray-400">
                                {{ $user->eco_posts_count }} posts
                            </span>
                        </div>
                    </div>

                    <div class="hidden sm:block"> 
                         <div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md whitespace-nowrap">
                            {{ number_format($user->points) }} pts
                              
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">No contributors yet</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg rounded-3xl border border-gray-200/50 dark:border-gray-700/50 p-6">
        <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-3">
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Community Guidelines
        </h4>
        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
            @foreach (['Share factual, evidence-based environmental information', 'Be respectful and constructive in discussions', 'Credit sources and provide references when possible', 'Focus on solutions and positive environmental actions'] as $rule)
                <li class="flex items-start gap-3">
                    <div class="w-5 h-5 bg-emerald-100 dark:bg-emerald-900/50 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span>{{ $rule }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>