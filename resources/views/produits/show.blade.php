@extends('layouts.app')

@section('title', $produit['nom'].' — NUBELLE Cosmetics')

@php
  $categorieLabel = $produit->categorie->nom ?? 'Produits';
  $categorieSlug = $produit->categorie->slug ?? null;
  $enStock = ($produit['stock'] ?? 0) > 0;
  $nombreAvis = $produit['avis_count'] ?? 0;
@endphp

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => $categorieLabel, 'url' => $categorieSlug ? url('/categorie/'.$categorieSlug) : null],
    ['label' => $produit['nom'], 'url' => null],
  ]" />

  <section class="max-w-6xl mx-auto px-5 py-8">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">

      {{-- ============================ GALERIE ============================ --}}
      <div>
        <div id="galerieZoom" class="relative flex h-80 sm:h-[26rem] w-full items-center justify-center overflow-hidden rounded-3xl bg-cream/50 cursor-zoom-in">
          @if (!empty($produit['badge']))
            <span class="absolute top-4 left-4 z-10 rounded-full bg-white/90 backdrop-blur px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-ember shadow-sm ring-1 ring-ember/10">{{ $produit['badge'] }}</span>
          @endif
          <img id="galerieImagePrincipale" src="{{ $produit['image'] }}" alt="{{ $produit['nom'] }}"
               class="h-full w-full object-contain p-6 transition-transform duration-200 ease-out">
        </div>

        @if (count($produit['images'] ?? []) > 1)
          <div class="mt-4 flex gap-3">
            @foreach ($produit['images'] as $img)
              <button type="button" onclick="changerImageGalerie('{{ asset('images/'.$img) }}', this)"
                      class="galerie-miniature h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-cream/50 ring-1 ring-black/5 transition-all {{ $loop->first ? 'ring-2 ring-ember' : '' }}">
                <img src="{{ asset('images/'.$img) }}" alt="" class="h-full w-full object-contain p-1.5">
              </button>
            @endforeach
          </div>
        @endif
      </div>

      {{-- ============================ INFOS ============================ --}}
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-tangerine">{{ $categorieLabel }}</p>
        <h1 class="mt-1 font-serif text-2xl sm:text-3xl font-bold text-gray-900">{{ $produit['nom'] }}</h1>
        <a href="{{ url('/marque/nubelle') }}" class="mt-1 inline-block text-xs text-gray-400 hover:text-ember transition-colors">Vendu par Nubelle Cosmetics</a>

        <a href="#avis" class="mt-2 inline-block">
          <x-rating :note="$produit['etoiles']" :avis="$nombreAvis.' avis'" />
        </a>

        <div class="mt-4">
          <x-price :prix="$produit['prix']" :ancien-prix="$produit['ancien_prix'] ?? null" size="lg" />
        </div>

        <div class="mt-3">
          @if ($enStock)
            @if (!empty($produit['stock_limite']))
              <span class="inline-flex items-center gap-1.5 rounded-full bg-tangerine/10 px-3 py-1 text-xs font-semibold text-ember">
                <span class="h-1.5 w-1.5 rounded-full bg-tangerine"></span> Stock limité — {{ $produit['stock'] }} restants
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> En stock
              </span>
            @endif
          @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-500">
              <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Rupture de stock
            </span>
          @endif
        </div>

        <p class="mt-5 text-gray-600 leading-relaxed">{{ $produit['description'] }}</p>

        @if (!empty($produit['nuances']))
          <div class="mt-6">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Teinte</p>
            <div class="flex gap-2">
              @foreach ($produit['nuances'] as $n)
                <button type="button" class="h-8 w-8 rounded-full ring-1 ring-gray-300 transition-transform hover:scale-110 focus:ring-2 focus:ring-ember
                  @class(['bg-white' => $n==='blanc','bg-[#fce5cd]' => $n==='beige','bg-[#8b4513]' => $n==='brun'])"></button>
              @endforeach
            </div>
          </div>
        @endif

        <div class="mt-6 flex items-center gap-4">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Quantité</p>
          <div class="flex items-center gap-3 rounded-full border border-gray-200 px-3 py-1.5">
            <button type="button" onclick="changerQuantiteProduit(-1)" class="text-lg text-gray-500 hover:text-ember">−</button>
            <span id="quantiteProduit" class="w-4 text-center text-sm font-semibold">1</span>
            <button type="button" onclick="changerQuantiteProduit(1)" class="text-lg text-gray-500 hover:text-ember">+</button>
          </div>
        </div>

        <div class="mt-7 flex flex-wrap gap-3">
          <button type="button" id="boutonAjouterPanier" onclick="ajouterAuPanier('{{ $produit->slug }}')"
                  @disabled(!$enStock)
                  class="flex-1 min-w-[180px] rounded-full bg-gray-900 px-6 py-3.5 text-sm font-semibold text-white transition-all duration-300 hover:bg-ember disabled:cursor-not-allowed disabled:bg-gray-300">
            {{ $enStock ? 'Ajouter au panier' : 'Indisponible' }}
          </button>
          <button type="button" aria-label="Ajouter aux favoris"
                  class="flex h-[52px] w-[52px] items-center justify-center rounded-full border border-gray-200 text-gray-500 transition-colors hover:border-ember hover:text-ember">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
          </button>
        </div>
        <p id="messageAjoutPanier" class="mt-2 hidden text-xs font-medium"></p>
        @if ($enStock)
          <button type="button" id="boutonAcheterMaintenant" onclick="acheterMaintenant('{{ $produit->slug }}', this)"
                  class="btn btn-accent mt-3 w-full">
            Payer maintenant
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
          </button>
        @endif
      </div>
    </div>

    {{-- ===================== DESCRIPTION / COMPOSITION / CONSEILS ==================== --}}
    <div class="mt-16 rounded-3xl bg-white p-2 shadow-sm ring-1 ring-black/5">
      <div class="flex flex-wrap gap-1 border-b border-gray-100 px-4 pt-2">
        <button type="button" onclick="afficherOnglet('description', this)" class="onglet-btn border-b-2 border-ember px-4 py-3 text-sm font-semibold text-ember">Description</button>
        <button type="button" onclick="afficherOnglet('composition', this)" class="onglet-btn border-b-2 border-transparent px-4 py-3 text-sm font-semibold text-gray-500 hover:text-gray-800">Composition</button>
        <button type="button" onclick="afficherOnglet('conseils', this)" class="onglet-btn border-b-2 border-transparent px-4 py-3 text-sm font-semibold text-gray-500 hover:text-gray-800">Conseils d'utilisation</button>
      </div>
      <div class="p-6 sm:p-8">
        <div id="onglet-description" class="onglet-contenu text-gray-600 leading-relaxed">{{ $produit['description_longue'] ?? $produit['description'] }}</div>
        <div id="onglet-composition" class="onglet-contenu hidden text-gray-600 leading-relaxed">{{ $produit['composition'] ?? 'Composition disponible sur l\'emballage du produit.' }}</div>
        <div id="onglet-conseils" class="onglet-contenu hidden text-gray-600 leading-relaxed">{{ $produit['conseils'] ?? 'Se référer à la notice fournie avec le produit.' }}</div>
      </div>
    </div>

    {{-- ============================== AVIS ============================= --}}
    <div id="avis" class="mt-16">
      <x-section-heading align="left" eyebrow="Retours clients" :title="'Avis (' . $nombreAvis . ')'" />
      @if (count($avisProduit))
        <div class="grid gap-4 sm:grid-cols-3">
          @foreach ($avisProduit as $a)
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5">
              <x-rating :note="$a['note']" />
              <p class="mt-3 text-sm font-semibold text-gray-900">{{ $a['auteur'] }}</p>
              <p class="mt-1 text-sm text-gray-500 italic">"{{ $a['texte'] }}"</p>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-sm text-gray-400">Aucun avis pour ce produit pour le moment.</p>
      @endif
    </div>

    {{-- ========================= PRODUITS SIMILAIRES ==================== --}}
    @if (count($similaires))
      <div class="mt-16">
        <x-section-heading align="left" title="Vous aimerez aussi" />
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 sm:gap-6">
          @foreach ($similaires as $p)
            <x-product-card :produit="$p" />
          @endforeach
        </div>
      </div>
    @endif

    {{-- ======================= RÉCEMMENT CONSULTÉS ======================= --}}
    <div id="recemmentConsultes" class="mt-16 hidden">
      <x-section-heading align="left" title="Récemment consultés" />
      <div id="recemmentConsultesListe" class="flex gap-4 overflow-x-auto pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"></div>
    </div>
  </section>

@endsection

@push('scripts')
<script>
  // Zoom au survol de l'image principale
  (function () {
    const zoomBox = document.getElementById('galerieZoom');
    const zoomImg = document.getElementById('galerieImagePrincipale');
    if (!zoomBox || !zoomImg) return;
    zoomBox.addEventListener('mousemove', (e) => {
      const rect = zoomBox.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      zoomImg.style.transformOrigin = `${x}% ${y}%`;
      zoomImg.style.transform = 'scale(1.8)';
    });
    zoomBox.addEventListener('mouseleave', () => { zoomImg.style.transform = 'scale(1)'; });
  })();

  function changerImageGalerie(src, btn) {
    document.getElementById('galerieImagePrincipale').src = src;
    document.querySelectorAll('.galerie-miniature').forEach(el => el.classList.remove('ring-2', 'ring-ember'));
    btn.classList.add('ring-2', 'ring-ember');
  }

  let qteProduit = 1;
  function changerQuantiteProduit(delta) {
    qteProduit = Math.max(1, qteProduit + delta);
    document.getElementById('quantiteProduit').textContent = qteProduit;
  }

  async function ajouterAuPanier(slug) {
    const bouton = document.getElementById('boutonAjouterPanier');
    const message = document.getElementById('messageAjoutPanier');
    bouton.disabled = true;

    try {
      const reponse = await fetch(`/panier/ajouter/${slug}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ quantite: qteProduit }),
      });
      const data = await reponse.json();

      message.textContent = data.message;
      message.classList.remove('hidden', 'text-red-500', 'text-green-600');
      message.classList.add(data.succes ? 'text-green-600' : 'text-red-500');

      if (data.succes) {
        document.querySelectorAll('.badge-panier, #badgePanierMobile').forEach(el => { el.textContent = data.nbPanier; });
        window.animerBadgePanier && window.animerBadgePanier();
      }
    } finally {
      bouton.disabled = false;
    }
  }

  // « Payer maintenant » : ajoute le produit (quantité choisie) puis file au
  // checkout, pour que la commande ne parte jamais sur un panier vide.
  async function acheterMaintenant(slug, bouton) {
    bouton.disabled = true;
    try {
      await fetch(`/panier/ajouter/${slug}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ quantite: qteProduit }),
      });
    } catch (e) {
      // On tente tout de même la redirection ; le checkout gère un panier vide.
    }
    window.location.href = @json(url('/checkout'));
  }

  function afficherOnglet(nom, btn) {
    document.querySelectorAll('.onglet-contenu').forEach(el => el.classList.add('hidden'));
    document.getElementById('onglet-' + nom).classList.remove('hidden');
    document.querySelectorAll('.onglet-btn').forEach(b => { b.classList.remove('border-ember', 'text-ember'); b.classList.add('border-transparent', 'text-gray-500'); });
    btn.classList.add('border-ember', 'text-ember');
    btn.classList.remove('border-transparent', 'text-gray-500');
  }

  // Produits récemment consultés (localStorage, purement côté client)
  (function () {
    const cle = 'nubelle_recemment_consultes';
    const produitBase = @json(url('/produit'));
    const produitActuel = @json(['slug' => $produit['slug'], 'nom' => $produit['nom'], 'image' => $produit['image']]);

    let liste = [];
    try { liste = JSON.parse(localStorage.getItem(cle)) || []; } catch (e) { liste = []; }
    const autres = liste.filter(p => p.slug !== produitActuel.slug);

    liste = [produitActuel, ...autres].slice(0, 6);
    localStorage.setItem(cle, JSON.stringify(liste));

    const conteneur = document.getElementById('recemmentConsultesListe');
    const section = document.getElementById('recemmentConsultes');
    if (autres.length && conteneur && section) {
      conteneur.innerHTML = autres.map(p => `
        <a href="${produitBase}/${p.slug}" class="group w-28 shrink-0 sm:w-32">
          <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-xl bg-cream/50 sm:h-32 sm:w-32">
            <img src="${p.image}" alt="${p.nom}" class="h-full w-full object-contain p-2 transition-transform duration-500 group-hover:scale-105">
          </div>
          <p class="mt-2 line-clamp-2 text-xs font-medium text-gray-700">${p.nom}</p>
        </a>
      `).join('');
      section.classList.remove('hidden');
    }
  })();
</script>
@endpush
