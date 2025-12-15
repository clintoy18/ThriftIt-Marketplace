<div class="space-y-8">
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-6 mb-8">
        <div class="flex items-center justify-between">
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <svg class="w-7 h-7 text-[#B59F84]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Community Feed
            </h3>
            <div class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                {{ $posts->count() }} posts • {{ $posts->unique('user_id')->count() }} contributors
            </div>
        </div>
    </div>

    <div class="space-y-8">
        @forelse($posts as $post)
            <article class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden transform transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
                
                <header class="bg-gradient-to-r from-[#B59F84] to-[#8A7B66] px-6 py-5">
                    <div class="flex items-center justify-between text-white">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-xl font-bold">
                                {{ substr($post->user->fname ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-lg">{{ $post->user->fname ?? 'Community Member' }}</p>
                                <p class="text-sm opacity-90 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        @if (Auth::id() === $post->user_id)
                            <div class="flex gap-2">
                                <a href="{{ route('eco-posts.edit', $post->id) }}" class="px-4 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-xl text-white font-medium text-sm transition-all flex items-center gap-2">
                                    Edit
                                </a>
                                <form action="{{ route('eco-posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 backdrop-blur-sm rounded-xl text-white font-medium text-sm transition-all flex items-center gap-2">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </header>

                <div class="p-6" x-data="{ expanded: false, showImageModal: false }">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">{{ $post->title }}</h3>
                    
                    <div class="mb-6">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line transition-all duration-300"
                           :class="expanded ? '' : 'line-clamp-3'">
                            {{ $post->content }}
                        </p>

                        @if(Str::length($post->content) > 200)
                            <button @click="expanded = !expanded" 
                                    class="mt-2 text-sm font-bold text-[#B59F84] hover:text-[#a08e77] focus:outline-none flex items-center gap-1 transition-colors">
                                <span x-text="expanded ? 'Show Less' : 'Show More'"></span>
                                <svg class="w-4 h-4 transform transition-transform duration-300" 
                                     :class="expanded ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    @if ($post->image)
                        <div class="mb-6 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg cursor-zoom-in relative group"
                             @click="showImageModal = true">
                            <img src="{{ Storage::disk('s3')->url($post->image) }}" 
                                 alt="Post Image" 
                                 class="w-full object-cover h-96 transition-transform duration-500 group-hover:scale-105">
                            
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300 flex items-center justify-center">
                                <span class="bg-black/50 text-white px-4 py-2 rounded-full text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-sm">
                                    Click to Enlarge
                                </span>
                            </div>
                        </div>

                        <div x-show="showImageModal" 
                             x-transition.opacity
                             style="display: none;"
                             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-4"
                             @keydown.escape.window="showImageModal = false"
                             @click.self="showImageModal = false">
                            
                            <div class="relative w-full h-full flex items-center justify-center">
                                <button @click="showImageModal = false" class="absolute top-4 right-4 text-white hover:text-gray-300 z-50 p-2 bg-black/20 rounded-full backdrop-blur-sm">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>

                                <img src="{{ Storage::disk('s3')->url($post->image) }}" 
                                     alt="Post Image Full" 
                                     class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
                            </div>
                        </div>
                    @endif

                    @if ($post->video_link)
                        <a href="{{ $post->video_link }}" target="_blank" class="inline-flex items-center gap-3 text-[#B59F84] font-semibold hover:underline">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Watch Video
                        </a>
                    @endif
                </div>
            </article>
        @empty
            <div class="text-center py-16 bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg rounded-3xl border border-dashed border-gray-300 dark:border-gray-600">
                <div class="w-28 h-28 mx-auto bg-gradient-to-br from-[#F8EED6] to-[#F4F2ED] dark:from-[#634600]/30 dark:to-[#B59F84]/20 rounded-3xl flex items-center justify-center mb-6">
                    <svg class="w-14 h-14 text-[#B59F84]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">Be the First to Share!</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">Your eco-insights can spark meaningful change.</p>
            </div>
        @endforelse
    </div>
</div> 