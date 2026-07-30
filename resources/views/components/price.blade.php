@props(['prix', 'ancienPrix' => null, 'size' => 'base'])

@php
  $tailleClasse = $size === 'lg' ? 'text-2xl sm:text-3xl' : 'text-base sm:text-lg';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-baseline gap-2']) }}>
  <span class="font-serif {{ $tailleClasse }} font-bold text-ember">
    {{ number_format($prix, 0, ',', ' ') }} <span class="text-[11px] font-sans font-medium text-gray-400">FCFA</span>
  </span>
  @if ($ancienPrix)
    <span class="text-xs sm:text-sm font-medium text-gray-400 line-through">{{ number_format($ancienPrix, 0, ',', ' ') }} FCFA</span>
    <span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-bold text-red-500">
      -{{ round((1 - $prix / $ancienPrix) * 100) }}%
    </span>
  @endif
</div>
