@props(['note' => 0, 'avis' => null])

<div {{ $attributes->merge(['class' => 'flex items-center gap-1 text-tangerine']) }}>
  @for ($i = 1; $i <= 5; $i++)
    <span class="text-xs {{ $i <= $note ? 'text-tangerine' : 'text-gray-300' }}">★</span>
  @endfor
  @if (!is_null($avis))
    <span class="ml-1 text-[11px] text-gray-400">({{ $avis }})</span>
  @endif
</div>
