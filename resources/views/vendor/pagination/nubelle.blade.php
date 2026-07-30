@if ($paginator->hasPages())
  <nav aria-label="Pagination" class="mt-10 flex items-center justify-center gap-1.5">
    @if ($paginator->onFirstPage())
      <span class="flex h-9 w-9 items-center justify-center rounded-full text-gray-300">&larr;</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="flex h-9 w-9 items-center justify-center rounded-full text-gray-600 transition-colors hover:bg-cream">&larr;</a>
    @endif

    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="flex h-9 w-9 items-center justify-center text-sm text-gray-400">{{ $element }}</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ember text-sm font-semibold text-white shadow-sm">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="flex h-9 w-9 items-center justify-center rounded-full text-sm text-gray-600 transition-colors hover:bg-cream">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="flex h-9 w-9 items-center justify-center rounded-full text-gray-600 transition-colors hover:bg-cream">&rarr;</a>
    @else
      <span class="flex h-9 w-9 items-center justify-center rounded-full text-gray-300">&rarr;</span>
    @endif
  </nav>
@endif
