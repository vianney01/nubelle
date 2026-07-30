@props(['items' => []])

<nav aria-label="Fil d'Ariane" {{ $attributes->merge(['class' => 'max-w-6xl mx-auto px-5 pt-5 text-xs sm:text-sm text-gray-500']) }}>
  <ol class="flex flex-wrap items-center gap-1.5">
    @foreach ($items as $item)
      <li class="flex items-center gap-1.5">
        @if (!$loop->first)<span class="text-gray-300">/</span>@endif
        @if (!empty($item['url']) && !$loop->last)
          <a href="{{ $item['url'] }}" class="transition-colors hover:text-ember">{{ $item['label'] }}</a>
        @else
          <span class="{{ $loop->last ? 'font-medium text-gray-800' : '' }}">{{ $item['label'] }}</span>
        @endif
      </li>
    @endforeach
  </ol>
</nav>
