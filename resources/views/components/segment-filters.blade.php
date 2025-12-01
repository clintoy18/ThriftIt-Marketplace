@props(['segment', 'categories', 'barangays', 'selectedCategoryId' => null, 'selectedBarangayId' => null])

<div class="flex gap-2 justify-end relative z-[100]">
    
    <div x-data="{ open: false, categorySearch: '' }" class="relative z-[100]">
        <button @click="open = !open"
            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm shadow-sm z-50">
            <span id="categoryButtonText">
                {{-- Display the selected category name or 'Category' --}}
                {{ $selectedCategoryId && $categories->where('id', $selectedCategoryId)->first() ? $categories->where('id', $selectedCategoryId)->first()->name : 'Category' }}
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700 dark:text-gray-300"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </button>
        <div x-cloak x-show="open" @click.outside="open = false; categorySearch = ''"
            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[101] py-1">
            
            <div class="px-3 py-1">
                <input x-model="categorySearch" placeholder="Search category..."
                    class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="max-h-64 overflow-y-auto mt-1">
                <a data-category-link data-category-name="All" href="{{ route('segments.show', ['segment' => $segment->id]) }}"
                    class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                    All
                </a>
                @foreach ($categories as $cat)
                    <a x-show="categorySearch === '' || '{{ strtolower($cat->name) }}'.includes(categorySearch.toLowerCase())"
                        data-category-link data-category-name="{{ $cat->name }}"
                        href="{{ route('segments.show', ['segment' => $segment->id, 'category' => $cat->id]) }}"
                        class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg {{ (int)$selectedCategoryId === $cat->id ? 'font-semibold' : '' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div x-data="{ open: false, locationSearch: '' }" class="relative z-[100]">
        <button @click="open = !open"
            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm shadow-sm z-50">
            <span id="locationButtonText">
                {{-- Display the selected barangay name or 'Location' --}}
                {{ $selectedBarangayId && $barangays->where('id', $selectedBarangayId)->first() ? $barangays->where('id', $selectedBarangayId)->first()->name : 'Location' }}
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700 dark:text-gray-300"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </button>
        <div x-cloak x-show="open" @click.outside="open = false; locationSearch = ''"
            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[101] py-1">
            
            <div class="px-3 py-1">
                <input x-model="locationSearch" placeholder="Search location..."
                    class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="max-h-64 overflow-y-auto mt-1">
                {{-- The "All" link should return to the base segment URL --}}
                <a data-location-link data-location-name="All" href="{{ route('segments.show', ['segment' => $segment->id]) }}"
                    class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                    All
                </a>
                @foreach ($barangays as $barangay)
                    <a x-show="locationSearch === '' || '{{ strtolower($barangay->name) }}'.includes(locationSearch.toLowerCase())"
                        data-location-link data-location-name="{{ $barangay->name }}"
                        href="{{ route('segments.show', ['segment' => $segment->id, 'barangay' => $barangay->id]) }}"
                        class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg {{ (int)$selectedBarangayId === $barangay->id ? 'font-semibold' : '' }}">
                        {{ $barangay->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

</div>