@php
  /** Contenu dynamique du panier latéral, rendu au chargement ET renvoyé par
   *  la route panier.apercu pour se rafraîchir après un ajout AJAX. */
  $lignesApercu = $lignesApercu ?? collect();
  $sousTotalApercu = $sousTotalApercu ?? $lignesApercu->sum(fn ($l) => (float) $l['produit']->prix * $l['quantite']);
@endphp

{{-- Liste --}}
<div id="panierMobileContenu" class="flex-1 overflow-y-auto px-5">
  @forelse ($lignesApercu->take(4) as $ligne)
    <div class="flex items-center gap-3 py-4 {{ ! $loop->last ? 'border-b border-gray-50' : '' }}">
      <a href="{{ url('/produit/'.$ligne['produit']->slug) }}"
         class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-cream/50 ring-1 ring-black/5">
        <img src="{{ $ligne['produit']['image'] }}" alt="{{ $ligne['produit']->nom }}" class="h-full w-full object-contain p-1">
      </a>
      <div class="min-w-0 flex-1">
        <a href="{{ url('/produit/'.$ligne['produit']->slug) }}"
           class="line-clamp-2 text-sm font-semibold leading-snug text-gray-900 transition-colors hover:text-ember">{{ $ligne['produit']->nom }}</a>
        <p class="mt-1 text-xs text-gray-400">{{ $ligne['quantite'] }} × {{ number_format($ligne['produit']->prix, 0, ',', ' ') }} FCFA</p>
      </div>
      <span class="shrink-0 text-sm font-bold text-ember">{{ number_format((float) $ligne['produit']->prix * $ligne['quantite'], 0, ',', ' ') }} FCFA</span>
    </div>
  @empty
    <div class="flex h-full flex-col items-center justify-center py-16 text-center">
      <span class="flex h-16 w-16 items-center justify-center rounded-full bg-cream text-ember">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.973-4.8 2.499-7.42a.75.75 0 0 0-.732-.905H5.106M7.5 14.25 5.106 5.165M6 18.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
        </svg>
      </span>
      <p class="mt-4 font-serif text-base text-gray-700">Votre panier est vide.</p>
      <p class="mt-1 text-sm text-gray-400">Ajoutez des produits pour les retrouver ici.</p>
    </div>
  @endforelse
  @if ($lignesApercu->count() > 4)
    <p class="pb-4 pt-1 text-center text-xs text-gray-400">+ {{ $lignesApercu->count() - 4 }} autre(s) article(s)</p>
  @endif
</div>

{{-- Pied --}}
@if ($lignesApercu->isNotEmpty())
  <div class="border-t border-gray-100 px-5 py-4">
    <div class="mb-4 flex items-baseline justify-between">
      <span class="text-sm text-gray-500">Sous-total</span>
      <span class="font-serif text-xl font-bold text-gray-900">{{ number_format($sousTotalApercu, 0, ',', ' ') }} FCFA</span>
    </div>
    <a href="{{ url('/checkout') }}" class="block w-full rounded-full bg-gray-900 px-5 py-3 text-center text-sm font-semibold text-white transition-colors hover:bg-ember">
      Passer la commande
    </a>
    <a href="{{ url('/panier') }}" class="mt-2 block w-full rounded-full border border-gray-200 px-5 py-3 text-center text-sm font-semibold text-gray-700 transition-colors hover:border-ember hover:text-ember">
      Voir le panier
    </a>
  </div>
@endif
