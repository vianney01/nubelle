@extends('layouts.app')

@section('title', 'Catalogue — NUBELLE Cosmetics')

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'Catalogue', 'url' => null],
  ]" />

  <section class="max-w-6xl mx-auto px-5 py-8">
    <div class="mb-8">
      <h1 class="font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Notre catalogue</h1>
      <p class="mt-2 text-gray-500">{{ $produits->total() }} produit{{ $produits->total() > 1 ? 's' : '' }} trouvé{{ $produits->total() > 1 ? 's' : '' }}</p>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr]">
      <aside>
        <details class="mb-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm lg:hidden">
          <summary class="cursor-pointer text-sm font-semibold text-gray-800">Filtres &amp; tri</summary>
          <div class="mt-4">
            <x-filtres-produits :categories="$categories" :categorie-active="$categorieActive" :tri="$tri" :q="$q" />
          </div>
        </details>
        <div class="hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 lg:sticky lg:top-24 lg:block">
          <x-filtres-produits :categories="$categories" :categorie-active="$categorieActive" :tri="$tri" :q="$q" />
        </div>
      </aside>

      <div>
        @if ($produits->isEmpty())
          <div class="rounded-3xl bg-cream/40 py-20 text-center">
            <p class="font-serif text-xl text-gray-700">Aucun produit ne correspond à votre recherche.</p>
            <a href="{{ url('/produits') }}" class="mt-4 inline-block text-sm font-semibold text-ember hover:text-sienna">Réinitialiser les filtres →</a>
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
