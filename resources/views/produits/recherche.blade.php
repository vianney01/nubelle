@extends('layouts.app')

@section('title', ($q !== '' ? 'Résultats pour « '.$q.' » — ' : 'Recherche — ').'NUBELLE Cosmetics')

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'Résultats de recherche', 'url' => null],
  ]" />

  <section class="max-w-6xl mx-auto px-5 py-10">
    <div class="mb-8 max-w-lg">
      <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Résultats de recherche</h1>
      <form method="GET" action="{{ url('/recherche') }}" class="relative mt-4">
        <input type="text" name="q" value="{{ $q }}" placeholder="Un produit, une catégorie…"
               class="w-full rounded-full border border-gray-200 bg-white py-3 pl-11 pr-4 text-sm text-gray-800 shadow-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
      </form>
      @if ($q !== '')
        <p class="mt-3 text-sm text-gray-500">
          {{ $resultats->total() }} résultat{{ $resultats->total() > 1 ? 's' : '' }} pour « {{ $q }} »
        </p>
      @endif
    </div>

    @if ($q === '')
      {{-- ============================ ÉTAT INITIAL ============================ --}}
      <div class="rounded-3xl bg-cream/40 py-20 text-center">
        <p class="font-serif text-xl text-gray-700">Que recherchez-vous aujourd'hui ?</p>
        <p class="mt-2 text-sm text-gray-500">Essayez « sérum », « corps » ou le nom d'une catégorie.</p>
      </div>
    @elseif ($resultats->isEmpty())
      {{-- ============================ AUCUN RÉSULTAT =========================== --}}
      <div class="flex flex-col items-center rounded-3xl bg-cream/40 px-6 py-16 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 160" class="h-40 w-auto">
          <circle cx="100" cy="80" r="64" fill="#FFE282" opacity="0.5" />
          <circle cx="88" cy="72" r="30" fill="none" stroke="#AA4C00" stroke-width="6" />
          <line x1="109" y1="93" x2="132" y2="116" stroke="#AA4C00" stroke-width="6" stroke-linecap="round" />
          <line x1="78" y1="72" x2="98" y2="72" stroke="#A0522D" stroke-width="5" stroke-linecap="round" />
        </svg>
        <p class="mt-6 font-serif text-xl font-bold text-gray-900">Aucun produit trouvé</p>
        <p class="mt-2 max-w-sm text-sm text-gray-500">
          Nous n'avons rien trouvé pour « {{ $q }} ». Essayez un autre mot-clé ou parcourez tout le catalogue.
        </p>
        <a href="{{ url('/produits') }}"
           class="mt-6 rounded-full bg-gray-900 px-8 py-3 text-sm font-semibold text-white transition-colors hover:bg-ember">
          Voir tous les produits
        </a>
      </div>
    @else
      {{-- ============================== RÉSULTATS ============================= --}}
      <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 sm:gap-6">
        @foreach ($resultats as $p)
          <x-product-card :produit="$p" />
        @endforeach
      </div>
      {{ $resultats->links('vendor.pagination.nubelle') }}
    @endif
  </section>

@endsection
