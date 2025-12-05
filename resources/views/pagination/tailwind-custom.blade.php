@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-2 text-sm">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-500 rounded-lg cursor-not-allowed">
                &laquo; Previous
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-3 py-1 bg-white dark:bg-gray-800 text-[#634600] dark:text-[#D9D9D9] rounded-lg hover:bg-[#634600] hover:text-white dark:hover:bg-[#634600] dark:hover:text-white transition">
                &laquo; Previous
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-500 rounded-lg">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 bg-[#634600] text-white rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-1 bg-white dark:bg-gray-800 text-[#634600] dark:text-[#D9D9D9] rounded-lg hover:bg-[#634600] hover:text-white dark:hover:bg-[#634600] dark:hover:text-white transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-3 py-1 bg-white dark:bg-gray-800 text-[#634600] dark:text-[#D9D9D9] rounded-lg hover:bg-[#634600] hover:text-white dark:hover:bg-[#634600] dark:hover:text-white transition">
                Next &raquo;
            </a>
        @else
            <span class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-500 rounded-lg cursor-not-allowed">
                Next &raquo;
            </span>
        @endif
    </nav>
@endif
