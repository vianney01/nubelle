@extends('layouts.app')

@section('title', 'Commande confirmée — NUBELLE Cosmetics')

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'Confirmation de commande', 'url' => null],
  ]" />

  <section class="max-w-3xl mx-auto px-5 py-14 text-center">
    <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-green-600">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
      </svg>
    </span>

    <p class="mt-6 text-xs font-semibold uppercase tracking-[0.25em] text-tangerine">Merci pour votre commande</p>
    <h1 class="mt-2 font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Commande confirmée</h1>
    <p class="mt-3 text-gray-500">
      Votre commande <span class="font-semibold text-gray-800">{{ $commande->numero }}</span> a bien été enregistrée.
      Un e-mail de confirmation sera envoyé à <span class="font-semibold text-gray-800">{{ $commande->client->email ?? '' }}</span>.
    </p>

    <div class="mt-10 rounded-3xl bg-white p-6 text-left shadow-sm ring-1 ring-black/5 sm:p-8">
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-5">
        <div>
          <p class="text-xs text-gray-400">Numéro de commande</p>
          <p class="font-serif text-lg font-bold text-gray-900">{{ $commande->numero }}</p>
        </div>
        <div>
          <p class="text-xs text-gray-400">Date</p>
          <p class="font-medium text-gray-800">{{ $commande->created_at->translatedFormat('d F Y') }}</p>
        </div>
        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold capitalize text-gray-600">En attente</span>
      </div>

      <div class="mt-5 space-y-3">
        @foreach ($commande->lignes as $ligne)
          <div class="flex items-center gap-3">
            <div class="relative h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-cream/50">
              @if ($ligne->produit)
                <img src="{{ $ligne->produit['image'] }}" alt="{{ $ligne->produit->nom }}" class="h-full w-full object-contain p-1">
              @endif
              <span class="absolute -right-1.5 -top-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-gray-900 text-[10px] font-bold text-white">{{ $ligne->quantite }}</span>
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-800">{{ $ligne->produit->nom ?? 'Produit indisponible' }}</p>
              <p class="text-xs text-gray-400">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA / unité</p>
            </div>
            <p class="text-sm font-semibold text-ember">{{ number_format($ligne->prix_unitaire * $ligne->quantite, 0, ',', ' ') }} FCFA</p>
          </div>
        @endforeach
      </div>

      <div class="mt-5 space-y-2 border-t border-gray-100 pt-5 text-sm">
        <div class="flex justify-between text-gray-500">
          <span>Sous-total</span>
          <span class="text-gray-800">{{ number_format($commande->total_avant_remise, 0, ',', ' ') }} FCFA</span>
        </div>
        @if ($commande->reduction_montant > 0)
          <div class="flex justify-between text-green-600">
            <span>Réduction{{ $commande->codePromo ? ' ('.$commande->codePromo->code.')' : '' }}</span>
            <span class="font-semibold">− {{ number_format($commande->reduction_montant, 0, ',', ' ') }} FCFA</span>
          </div>
        @endif
        <div class="flex justify-between text-gray-500">
          <span>Livraison</span>
          <span class="text-gray-800">{{ $commande->frais_livraison > 0 ? number_format($commande->frais_livraison, 0, ',', ' ').' FCFA' : 'Gratuite' }}</span>
        </div>
        <div class="flex items-center justify-between border-t border-gray-100 pt-3">
          <span class="font-semibold text-gray-900">Total payé</span>
          <span class="font-serif text-xl font-bold text-ember">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</span>
        </div>
      </div>

      @if ($commande->adresse_livraison)
        <div class="mt-5 border-t border-gray-100 pt-5 text-sm text-gray-500">
          <p class="font-semibold text-gray-700">Adresse de livraison</p>
          <p class="mt-1">{{ $commande->adresse_livraison }}</p>
        </div>
      @endif
    </div>

    <div class="mt-8 flex flex-wrap justify-center gap-3">
      <a href="{{ url('/compte') }}" class="rounded-full bg-gray-900 px-8 py-3 text-sm font-semibold text-white transition-colors hover:bg-ember">
        Voir mes commandes
      </a>
      <a href="{{ url('/produits') }}" class="rounded-full border border-gray-200 px-8 py-3 text-sm font-semibold text-gray-700 transition-colors hover:border-ember hover:text-ember">
        Continuer mes achats
      </a>
    </div>
  </section>

@endsection
