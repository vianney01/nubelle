@props(['eyebrow' => null, 'title', 'subtitle' => null, 'align' => 'center'])

@php $centre = $align !== 'left'; @endphp

<div {{ $attributes->merge(['class' => ($centre ? 'text-center' : 'text-left') . ' mb-10']) }}>
  @if ($eyebrow)
    <p class="mb-2 text-xs sm:text-sm font-semibold uppercase tracking-wide text-tangerine">{{ $eyebrow }}</p>
  @endif
  <h2 class="font-serif text-3xl font-bold text-gray-900 sm:text-4xl {{ $centre ? 'title-underline' : '' }}">{{ $title }}</h2>
  @if ($subtitle)
    <p class="mt-3 text-gray-500 {{ $centre ? 'mx-auto max-w-xl' : 'max-w-xl' }}">{{ $subtitle }}</p>
  @endif
</div>
