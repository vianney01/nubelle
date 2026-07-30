@props(['titre', 'misAJour' => null])

<x-breadcrumb :items="[
  ['label' => 'Accueil', 'url' => url('/')],
  ['label' => $titre, 'url' => null],
]" />

<section class="max-w-3xl mx-auto px-5 py-12">
  <h1 class="font-serif text-3xl font-bold text-gray-900 sm:text-4xl">{{ $titre }}</h1>
  @if ($misAJour)
    <p class="mt-2 text-xs text-gray-400">Dernière mise à jour : {{ $misAJour }}</p>
  @endif
  <div class="prose-nubelle mt-8 space-y-6 text-sm leading-relaxed text-gray-600 sm:text-base [&_h2]:mt-8 [&_h2]:font-serif [&_h2]:text-xl [&_h2]:font-bold [&_h2]:text-gray-900 [&_ul]:list-disc [&_ul]:pl-5 [&_li]:mt-1">
    {{ $slot }}
  </div>
</section>
