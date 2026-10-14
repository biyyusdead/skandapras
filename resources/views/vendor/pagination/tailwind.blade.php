@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4">
        <div class="hidden sm:block text-xs text-surface-500">
            Menampilkan
            <span class="font-medium text-surface-700">{{ $paginator->firstItem() }}</span>
            –
            <span class="font-medium text-surface-700">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-medium text-surface-700">{{ $paginator->total() }}</span>
            data
        </div>

        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-surface-300 cursor-not-allowed">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                          text-surface-500 hover:bg-primary-50 hover:text-primary-600
                          transition-all duration-150">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-surface-400">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                         bg-primary-600 text-white text-xs font-medium shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                      text-xs text-surface-600 hover:bg-primary-50 hover:text-primary-600
                                      transition-all duration-150">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                          text-surface-500 hover:bg-primary-50 hover:text-primary-600
                          transition-all duration-150">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-surface-300 cursor-not-allowed">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </span>
            @endif
        </div>
    </nav>
@endif