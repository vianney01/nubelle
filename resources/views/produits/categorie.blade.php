@extends('layouts.app')

@section('title', $categorie['nom'].' — NUBELLE Cosmetics')

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'Catalogue', 'url' => url('/produits')],
    ['label' => $categorie['nom'], 'url' => null],
  ]" />

  {{-- ============================== BANNIÈRE ============================ --}}
  <section class="relative mx-4 mt-4 overflow-hidden rounded-3xl sm:mx-auto sm:max-w-6xl">
    <img src="{{ $categorie->image_url ?? asset('images/accueil.jpg') }}" alt="{{ $categorie['nom'] }}"
         class="h-56 w-full object-cover sm:h-72">
    <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/40 text-center px-6">
      <p class="text-white/80 text-xs uppercase tracking-[0.3em] mb-2">Catégorie</p>
      <h1 class="font-serif text-3xl sm:text-4xl font-bold text-white">{{ $categorie['nom'] }}</h1>
      <p class="mt-3 max-w-lg text-sm text-white/85">{{ $categorie['description'] }}</p>
    </div>
  </section>

  <section class="max-w-6xl mx-auto px-5 py-10">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr]">
      <aside>
        <details class="mb-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm lg:hidden">
          <summary class="cursor-pointer text-sm font-semibold text-gray-800">Filtres &amp; tri</summary>
          <div class="mt-4">
            <x-filtres-produits :tri="$tri" :q="$q" :action="url('/categorie/'.$categorie['slug'])" :masquer-categories="true" />
          </div>
        </details>
        <div class="hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 lg:sticky lg:top-24 lg:block">
          <x-filtres-produits :tri="$tri" :q="$q" :action="url('/categorie/'.$categorie['slug'])" :masquer-categories="true" />
        </div>
      </aside>

      <div>
        @if ($produits->isEmpty())
          <div class="rounded-3xl bg-cream/40 py-20 text-center">
            <p class="font-serif text-xl text-gray-700">Aucun produit dans cette catégorie pour le moment.</p>
          </div>
        @else
          <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6">
            @foreach ($produits as $p)
              <x-product-card :produit="$p" />
            @endforeach
          </div>
          {{ $produits->links('vendor.pagination.nubelle') }}
        @endif
      </div>
    </div>
  </section>

@endsection
