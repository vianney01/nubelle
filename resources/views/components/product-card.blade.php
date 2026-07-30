@props(['produit'])

@php
  $p = $produit;
  // Un vrai modèle Produit renvoie déjà une URL complète ; les données de
  // démo renvoient un simple nom de fichier à préfixer par asset('images/…').
  $imageUrl = str_starts_with($p['image'] ?? '', 'http') ? $p['image'] : asset('images/'.($p['image'] ?? ''));
  $stock = $p['stock'] ?? 1;
  $enStock = $stock > 0;
  $note = $p['etoiles'] ?? 0;
  $nbAvis = $p['avis_count'] ?? $p['avis'] ?? null;
  $badge = $p['badge'] ?? null;
  // La remise est affichée par <x-price> ; le coin haut-gauche ne porte que
  // les étiquettes de statut (nouveauté / meilleure vente).
  $badgeStatut = in_array($badge, ['promo', 'rupture'], true) ? null : $badge;
  $urlProduit = url('/produit/'.($p['slug'] ?? ''));
@endphp

<div class="reveal group card-surface relative flex h-full flex-col overflow-hidden p-2.5 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_18px_40px_rgba(170,76,0,0.16)] sm:p-3">

  {{-- Étiquettes de statut --}}
  @if ($badgeStatut)
    <span class="badge badge-soft absolute left-3 top-3 z-10">{{ $badgeStatut }}</span>
  @endif
  @unless ($enStock)
    <span class="badge badge-out absolute right-3 top-3 z-10">Épuisé</span>
  @endunless

  {{-- Visuel + actions rapides au survol --}}
  <div class="relative flex h-40 w-full shrink-0 items-center justify-center overflow-hidden rounded-xl bg-cream/50 sm:h-48">
    <a href="{{ $urlProduit }}" class="flex h-full w-full items-center justify-center" aria-label="{{ $p['nom'] ?? 'Produit' }}">
      <img src="{{ $imageUrl }}" alt="{{ $p['nom'] ?? '' }}" loading="lazy" decoding="async"
           class="h-full w-full object-contain p-2 transition-transform duration-500 ease-out group-hover:scale-110">
    </a>

    <div class="pointer-events-none absolute inset-x-2 bottom-2 flex translate-y-3 items-center gap-2 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
      @if ($enStock)
        <button type="button" onclick="ajoutRapidePanier('{{ $p['slug'] }}', this)"
                class="btn btn-primary btn-sm pointer-events-auto flex-1 shadow-md">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.973-4.8 2.499-7.42a.75.75 0 0 0-.732-.905H5.106M7.5 14.25 5.106 5.165M6 18.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
          </svg>
          Ajouter
        </button>
      @endif
      <a href="{{ $urlProduit }}" aria-label="Voir le produit"
         class="pointer-events-auto flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-gray-700 shadow-md ring-1 ring-black/5 transition-colors hover:text-ember">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
      </a>
    </div>
  </div>

  {{-- Informations --}}
  <div class="mt-2.5 flex flex-1 flex-col">
    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-tangerine">Nubelle</p>
    <h3 class="mt-0.5 line-clamp-2 text-sm font-semibold leading-snug text-gray-900">
      <a href="{{ $urlProduit }}" class="transition-colors hover:text-ember">{{ $p['nom'] ?? '' }}</a>
    </h3>

    @if (!empty($p['nuances']))
      <div class="mt-1.5 flex gap-1.5">
        @foreach ($p['nuances'] as $n)
          <span class="h-3.5 w-3.5 rounded-full ring-1 ring-gray-300 transition-transform hover:scale-125
            @class(['bg-white' => $n === 'blanc', 'bg-[#fce5cd]' => $n === 'beige', 'bg-[#8b4513]' => $n === 'brun'])"></span>
        @endforeach
      </div>
    @endif

    <x-rating :note="$note" :avis="$nbAvis" class="mt-1.5" />

    <x-price :prix="$p['prix']" :ancien-prix="$p['ancien_prix'] ?? null" class="mt-1.5" />

    {{-- Disponibilité --}}
    <p class="mt-1.5 flex items-center gap-1.5 text-[11px] font-medium {{ $enStock ? ($stock < 5 ? 'text-amber-600' : 'text-green-600') : 'text-gray-400' }}">
      <span class="h-1.5 w-1.5 rounded-full {{ $enStock ? ($stock < 5 ? 'bg-amber-500' : 'bg-green-500') : 'bg-gray-300' }}"></span>
      {{ ! $enStock ? 'Rupture de stock' : ($stock < 5 ? 'Plus que quelques exemplaires' : 'En stock') }}
    </p>

    <a href="{{ $urlProduit }}" class="btn btn-secondary btn-sm mt-3 w-full">
      {{ $p['cta'] ?? 'Voir le produit' }}
    </a>
  </div>
</div>
