<section class="mt-10">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Featured Buyers
                </h2>
                <span class="flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    Consensual Feature
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                Influencers & celebrities who shop sustainable fashion here.
            </p>
        </div>

        {{-- ACTION BUTTON: Only visible to the owner --}}
        @if (Auth::id() === $user->id)
            <button @click="$dispatch('open-buyer-modal')" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add Celebrity Buyer
            </button>
        @endif
    </div>

    {{-- THE GRID (Visible to All) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($featuredBuyers as $buyer)
            <div class="flex flex-col gap-4">
                {{-- Buyer Profile Card --}}
                <div class="group flex items-center p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md">
                    <div class="flex-shrink-0">
                        @if($buyer->avatar_path)
                            <img src="{{ Storage::disk('s3')->url($buyer->avatar_path) }}" class="h-14 w-14 rounded-full object-cover ring-2 ring-indigo-50 dark:ring-gray-700" alt="{{ $buyer->name }}">
                        @else
                            <div class="h-14 w-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-xl">{{ substr($buyer->name, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $buyer->name }}</p>
                        <p class="text-[11px] text-gray-500">{{ $buyer->handle ?? '@celebrity' }} • {{ $buyer->bio }}</p>
                    </div>
                </div>

                {{-- Buyer's Items --}}
                <div class="space-y-3">
                    @foreach($buyer->items as $item)
                        <div class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="relative aspect-video overflow-hidden bg-gray-100">
                                <img src="{{ Storage::disk('s3')->url($item->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            </div>
                            <div class="p-3">
                                <h3 class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $item->product_name }}</h3>
                                <p class="text-xs font-black text-indigo-600">₱{{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            {{-- EMPTY STATE (Visible to All) --}}
            <div class="col-span-full border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-3xl p-12 text-center">
                <p class="text-gray-500 text-sm">No featured buyers yet.</p>
                @if (Auth::id() === $user->id)
                    <button @click="$dispatch('open-buyer-modal')" class="mt-4 text-xs font-bold text-indigo-600 hover:underline">+ Showcase your first buyer</button>
                @endif
            </div>
        @endforelse
    </div>
</section>