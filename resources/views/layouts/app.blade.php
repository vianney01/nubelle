<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'NUBELLE Cosmetics — Une peau naturellement neuve')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css'])
  @stack('styles')
</head>
<body>

  {{-- ==================== BANDEAU + HEADER — DESKTOP (inchangé) ========== --}}
  <div class="hidden sm:block">
    <div class="bandeau-haut">
      <div class="contenu-bandeau">
        <div class="bouton-menu" onclick="toggleMenu()">&#9776;</div>
        <div class="carousel-textes" id="carouselTextes">
          <span class="texte-carousel active">Profitez de -10% sur votre 1er achat</span>
          <span class="texte-carousel">Nubelle Cosmetics • Une peau naturellement neuve</span>
          <span class="texte-carousel">Livraison rapide en Côte d'Ivoire</span>
        </div>
        <div class="icones-droits">
          <button type="button" onclick="ouvrirRecherche()" aria-label="Rechercher" class="text-white/90 transition-colors hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
          </button>
          <div class="compte" onclick="toggleDropdown(event)">
            <img src="{{ asset('images/utilisateur.png') }}" alt="Compte">
            <div class="dropdown-menu" id="dropdownCompte">
              <button class="btn-auth" onclick="ouvrirPopup('connexion')">Se connecter</button>
              <button class="btn-auth" onclick="ouvrirPopup('inscription')">S'inscrire</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <header class="entete">
      <a href="{{ url('/') }}" class="logo"><img src="{{ asset('images/logo.png') }}" alt="Nubelle Cosmetics"></a>
      <div class="panier" onclick="togglePanierMobile()">
        <img src="{{ asset('images/panier.png') }}" alt="Panier">
        <span class="badge-panier">{{ $nbPanier ?? 0 }}</span>
      </div>
    </header>
  </div>

  {{-- ======================= HEADER MOBILE — PREMIUM ====================== --}}
  <div class="sm:hidden sticky top-0 z-30">
    {{-- Bandeau promo défilant --}}
    <div class="overflow-hidden bg-gradient-to-r from-tangerine to-ember py-1.5">
      @php
        $annoncesBandeau = [
          ['svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>', 'texte' => '-10% sur votre 1ère commande'],
          ['svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v11.177m0-11.177L12.75 4.5m3.75 3.073L19.5 12h-3"/>', 'texte' => "Livraison rapide en Côte d'Ivoire"],
          ['svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>', 'texte' => 'Des soins naturels, pensés pour vous'],
        ];
      @endphp
      <div class="flex w-max animate-marquee items-center gap-10 whitespace-nowrap px-4 text-[11px] font-medium tracking-wide text-white">
        @foreach (array_merge($annoncesBandeau, $annoncesBandeau) as $i => $a)
          <span class="inline-flex items-center gap-1.5" @if ($i >= 3) aria-hidden="true" @endif>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">{!! $a['svg'] !!}</svg>
            {{ $a['texte'] }}
          </span>
        @endforeach
      </div>
    </div>

    {{-- Barre principale --}}
    <div class="relative flex h-14 items-center justify-between bg-white/95 px-4 backdrop-blur-md shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
      <button type="button" onclick="toggleMenu()" aria-label="Ouvrir le menu"
              class="flex h-9 w-9 items-center justify-center rounded-full text-gray-800 transition-colors active:bg-cream">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
        </svg>
      </button>

      <a href="{{ url('/') }}" class="absolute left-1/2 -translate-x-1/2">
        <img src="{{ asset('images/logo.png') }}" alt="Nubelle Cosmetics" class="h-9 object-contain">
      </a>

      <div class="flex items-center gap-1">
        <button type="button" onclick="ouvrirRecherche()" aria-label="Rechercher"
                class="flex h-9 w-9 items-center justify-center rounded-full text-gray-800 transition-colors active:bg-cream">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </button>
        <div class="compte relative" onclick="toggleDropdown(event)">
          <button type="button" aria-label="Mon compte"
                  class="flex h-9 w-9 items-center justify-center rounded-full text-gray-800 transition-colors active:bg-cream">
            @auth
              <span class="flex h-6 w-6 items-center justify-center rounded-full bg-ember text-[11px] font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'N', 0, 1)) }}
              </span>
            @else
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>
            @endauth
          </button>
          <div class="dropdown-menu" id="dropdownCompteMobile">
            @guest
              <button class="btn-auth" onclick="ouvrirPopup('connexion')">Se connecter</button>
              <button class="btn-auth" onclick="ouvrirPopup('inscription')">S'inscrire</button>
            @else
              <a href="{{ url('/compte') }}" class="btn-auth" style="text-decoration:none;display:block;text-align:center;">Mon compte</a>
            @endguest
          </div>
        </div>

        <button type="button" onclick="togglePanierMobile()" aria-label="Voir le panier"
                class="relative flex h-9 w-9 items-center justify-center rounded-full text-gray-800 transition-colors active:bg-cream">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.973-4.8 2.499-7.42a.75.75 0 0 0-.732-.905H5.106M7.5 14.25 5.106 5.165M6 18.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
          </svg>
          <span id="badgePanierMobile"
                class="absolute -top-1 -right-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white shadow-sm">
            {{ $nbPanier ?? 0 }}
          </span>
        </button>
      </div>
    </div>
  </div>

  {{-- =================== RIDEAU (backdrop) DU DRAWER MOBILE =============== --}}
  <div class="drawer-backdrop" id="drawerBackdrop" onclick="toggleMenu()"></div>

  {{-- ==================== MENU LATERAL — DESKTOP (premium) =============== --}}
  <div class="hidden sm:block">
    @php
      $q = request()->query('filtre');
      $groupesMenu = [
        'Boutique' => [
          ['label' => 'Accueil', 'url' => url('/'), 'actif' => request()->is('/'),
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.5 1.5 0 0 1 2.122 0l8.955 8.955M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>'],
          ['label' => 'Produits', 'url' => url('/produits'), 'actif' => request()->is('produits') && ! $q,
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m-.75 11.25h9a2.25 2.25 0 0 0 2.25-2.25L18 8.25A2.25 2.25 0 0 0 15.75 6H8.25A2.25 2.25 0 0 0 6 8.25L5.25 18a2.25 2.25 0 0 0 2.25 2.25Z"/>'],
          ['label' => 'Nouveautés', 'url' => url('/produits?filtre=nouveaute'), 'actif' => $q === 'nouveaute',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>'],
          ['label' => 'Promotions', 'url' => url('/produits?filtre=promotions'), 'actif' => $q === 'promotions',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/>'],
          ['label' => 'Meilleures ventes', 'url' => url('/produits?filtre=best_seller'), 'actif' => $q === 'best_seller',
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 21.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>'],
        ],
        'Mon espace' => [
          ['label' => 'Mon compte', 'url' => url('/compte'), 'actif' => request()->is('compte'), 'auth' => true,
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>'],
          ['label' => 'Mes commandes', 'url' => url('/compte'), 'actif' => false, 'auth' => true,
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>'],
          ['label' => 'Favoris', 'url' => url('/compte'), 'actif' => false, 'auth' => true,
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>'],
          ['label' => 'Mon panier', 'url' => url('/panier'), 'actif' => request()->is('panier'),
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.973-4.8 2.499-7.42a.75.75 0 0 0-.732-.905H5.106M7.5 14.25 5.106 5.165M6 18.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/>'],
        ],
        'Informations' => [
          ['label' => 'À propos', 'url' => url('/a-propos'), 'actif' => request()->is('a-propos'),
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>'],
          ['label' => 'Contact', 'url' => url('/contact'), 'actif' => request()->is('contact'),
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.007 1.872l-7.5 5.007a2.25 2.25 0 0 1-2.486 0L3.257 8.865A2.25 2.25 0 0 1 2.25 6.993V6.75"/>'],
          ['label' => 'FAQ', 'url' => url('/faq'), 'actif' => request()->is('faq'),
            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"/>'],
        ],
      ];
      $menuBase = 'group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200';
      $menuActif = 'bg-cream text-ember font-semibold ring-1 ring-ember/10';
      $menuDefaut = 'text-gray-600 hover:bg-cream/60 hover:text-ember';
    @endphp

    <aside class="menu-lateral flex flex-col" id="menuLateral" role="dialog" aria-label="Menu de navigation">

      {{-- En-tête --}}
      <div class="flex items-start justify-between border-b border-gray-100 px-6 pb-5 pt-6">
        <div>
          <img src="{{ asset('images/logo.png') }}" alt="Nubelle Cosmetics" class="h-9 object-contain">
          <p class="mt-2 text-[13px] font-medium text-bordeaux/80">Votre beauté commence ici.</p>
          <span class="mt-2.5 block h-[2px] w-12 rounded-full bg-gradient-to-r from-tangerine to-ember"></span>
        </div>
        <button type="button" onclick="toggleMenu()" aria-label="Fermer le menu"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-black/5 text-gray-500 transition-colors hover:bg-black/10 hover:text-gray-900 active:scale-90">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      {{-- Navigation --}}
      <nav class="flex-1 overflow-y-auto px-4 py-5">
        @foreach ($groupesMenu as $titre => $liens)
          <p class="px-2 text-[11px] font-bold uppercase tracking-[0.14em] text-gray-400 {{ ! $loop->first ? 'mt-6' : '' }}">{{ $titre }}</p>
          <ul class="mt-2 space-y-1">
            @foreach ($liens as $lien)
              <li>
                @php $estBouton = ($lien['auth'] ?? false) && ! auth()->check(); @endphp
                <{{ $estBouton ? 'button' : 'a' }}
                  @if ($estBouton) type="button" onclick="toggleMenu(); ouvrirPopup('connexion')"
                  @else href="{{ $lien['url'] }}" onclick="toggleMenu()" @if ($lien['actif']) aria-current="page" @endif @endif
                  class="{{ $menuBase }} {{ (! $estBouton && $lien['actif']) ? $menuActif : $menuDefaut }} w-full text-left">
                  @if (! $estBouton && $lien['actif'])
                    <span class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r-full bg-ember"></span>
                  @endif
                  <span class="flex h-5 w-5 shrink-0 items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">{!! $lien['svg'] !!}</svg>
                  </span>
                  <span class="flex-1">{{ $lien['label'] }}</span>
                  <svg class="h-4 w-4 -translate-x-1 opacity-0 transition-all duration-200 group-hover:translate-x-0 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                  </svg>
                </{{ $estBouton ? 'button' : 'a' }}>
              </li>
            @endforeach
          </ul>
        @endforeach
      </nav>

      {{-- Pied --}}
      <div class="border-t border-gray-100 px-6 py-5">
        <a href="{{ url('/produits?filtre=nouveaute') }}" onclick="toggleMenu()" class="btn btn-accent w-full">Découvrir les nouveautés</a>
        <div class="mt-4 flex items-center justify-center gap-3">
          <a href="#" aria-label="Instagram" class="flex h-9 w-9 items-center justify-center rounded-full text-gray-400 ring-1 ring-gray-200 transition-colors hover:text-ember hover:ring-ember/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
              <rect x="3.5" y="3.5" width="17" height="17" rx="5" />
              <circle cx="12" cy="12" r="4" />
              <circle cx="17" cy="7" r="0.8" fill="currentColor" stroke="none" />
            </svg>
          </a>
          <a href="#" aria-label="Facebook" class="flex h-9 w-9 items-center justify-center rounded-full text-gray-400 ring-1 ring-gray-200 transition-colors hover:text-ember hover:ring-ember/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.5 21v-7.5h2.5l.5-3.25h-3V8.1c0-.94.26-1.6 1.62-1.6H17.5V3.6C17.2 3.56 16.2 3.47 15 3.47c-2.4 0-4 1.46-4 4.15v2.63H8.5v3.25H11V21" />
            </svg>
          </a>
        </div>
      </div>
    </aside>
  </div>

  {{-- ===================== MENU LATERAL — MOBILE (premium) ================= --}}
  <div class="sm:hidden">
    <div class="drawer-mobile" id="menuLateralMobile">

      {{-- En-tête --}}
      <div class="drawer-row flex items-start justify-between border-b border-black/5 px-5 pb-5 pt-6">
        <div>
          <img src="{{ asset('images/logo.png') }}" alt="Nubelle Cosmetics" class="h-8 object-contain">
          <p class="mt-2 text-[13px] font-medium text-bordeaux/80">Votre beauté commence ici.</p>
          <span class="mt-2 block h-[2px] w-10 rounded-full bg-gold"></span>
        </div>
        <button type="button" onclick="toggleMenu()" aria-label="Fermer le menu"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-black/5 text-gray-600 transition-colors hover:bg-black/10 active:scale-90">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <nav class="px-4 py-5">
        {{-- Boutique --}}
        <p class="drawer-row drawer-eyebrow px-2 text-[11px] font-bold uppercase">Boutique</p>
        <ul class="drawer-list mt-2">
          <li class="drawer-row">
            <a href="{{ url('/') }}" onclick="toggleMenu()" class="drawer-link">
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.5 1.5 0 0 1 2.122 0l8.955 8.955M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                </svg>
              </span>
              Accueil
            </a>
          </li>
          <li class="drawer-row">
            <a href="{{ url('/produits') }}" onclick="toggleMenu()" class="drawer-link">
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m-.75 11.25h9a2.25 2.25 0 0 0 2.25-2.25L18 8.25A2.25 2.25 0 0 0 15.75 6H8.25A2.25 2.25 0 0 0 6 8.25L5.25 18a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
              </span>
              Produits
            </a>
          </li>
          <li class="drawer-row">
            <a href="{{ url('/produits?filtre=nouveaute') }}" onclick="toggleMenu()" class="drawer-link">
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                </svg>
              </span>
              Nouveautés
            </a>
          </li>
          <li class="drawer-row">
            <a href="{{ url('/produits?filtre=promotions') }}" onclick="toggleMenu()" class="drawer-link">
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>
              </span>
              Promotions
            </a>
          </li>
          <li class="drawer-row">
            <a href="{{ url('/produits?filtre=best_seller') }}" onclick="toggleMenu()" class="drawer-link">
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 21.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                </svg>
              </span>
              Meilleures ventes
            </a>
          </li>
        </ul>

        <div class="my-5 h-px bg-black/5"></div>

        {{-- Mon espace --}}
        <p class="drawer-row drawer-eyebrow px-2 text-[11px] font-bold uppercase">Mon espace</p>
        <ul class="drawer-list mt-2">
          <li class="drawer-row">
            @auth
              <a href="{{ url('/compte') }}" onclick="toggleMenu()" class="drawer-link">
            @else
              <button type="button" onclick="toggleMenu(); ouvrirPopup('connexion')" class="drawer-link">
            @endauth
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
              </span>
              Mon compte
            @auth
              </a>
            @else
              </button>
            @endauth
          </li>
          <li class="drawer-row">
            @auth
              <a href="{{ url('/compte') }}" onclick="toggleMenu()" class="drawer-link">
            @else
              <button type="button" onclick="toggleMenu(); ouvrirPopup('connexion')" class="drawer-link">
            @endauth
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
              </span>
              Mes commandes
            @auth
              </a>
            @else
              </button>
            @endauth
          </li>
          <li class="drawer-row">
            @auth
              <a href="{{ url('/compte') }}" onclick="toggleMenu()" class="drawer-link">
            @else
              <button type="button" onclick="toggleMenu(); ouvrirPopup('connexion')" class="drawer-link">
            @endauth
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
              </span>
              Favoris
            @auth
              </a>
            @else
              </button>
            @endauth
          </li>
          <li class="drawer-row">
            <a href="{{ url('/panier') }}" onclick="toggleMenu()" class="drawer-link">
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.973-4.8 2.499-7.42a.75.75 0 0 0-.732-.905H5.106M7.5 14.25 5.106 5.165M6 18.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
              </span>
              Mon panier
            </a>
          </li>
        </ul>

        <div class="my-5 h-px bg-black/5"></div>

        {{-- Informations --}}
        <p class="drawer-row drawer-eyebrow px-2 text-[11px] font-bold uppercase">Informations</p>
        <ul class="drawer-list mt-2">
          <li class="drawer-row">
            <a href="{{ url('/a-propos') }}" onclick="toggleMenu()" class="drawer-link">
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
              </span>
              À propos
            </a>
          </li>
          <li class="drawer-row">
            <a href="{{ url('/contact') }}" onclick="toggleMenu()" class="drawer-link">
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.007 1.872l-7.5 5.007a2.25 2.25 0 0 1-2.486 0L3.257 8.865A2.25 2.25 0 0 1 2.25 6.993V6.75" />
                </svg>
              </span>
              Contact
            </a>
          </li>
          <li class="drawer-row">
            <a href="{{ url('/faq') }}" onclick="toggleMenu()" class="drawer-link">
              <span class="drawer-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                </svg>
              </span>
              FAQ
            </a>
          </li>
        </ul>
      </nav>

      {{-- Pied du menu --}}
      <div class="drawer-row px-5 pb-6 pt-2">
        <div class="flex items-center justify-center gap-3">
          <a href="#" aria-label="Instagram" class="drawer-social">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
              <rect x="3.5" y="3.5" width="17" height="17" rx="5" />
              <circle cx="12" cy="12" r="4" />
              <circle cx="17" cy="7" r="0.8" fill="currentColor" stroke="none" />
            </svg>
          </a>
          <a href="#" aria-label="Facebook" class="drawer-social">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.5 21v-7.5h2.5l.5-3.25h-3V8.1c0-.94.26-1.6 1.62-1.6H17.5V3.6C17.2 3.56 16.2 3.47 15 3.47c-2.4 0-4 1.46-4 4.15v2.63H8.5v3.25H11V21" />
            </svg>
          </a>
          <a href="#" aria-label="TikTok" class="drawer-social">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 3.5c.45 2.1 1.85 3.4 4 3.55V10c-1.45-.05-2.8-.5-4-1.35v6.1a5.15 5.15 0 1 1-4.45-5.1v3.05a2.1 2.1 0 1 0 1.45 2v-11.2Z" />
            </svg>
          </a>
          <a href="#" aria-label="WhatsApp" class="drawer-social">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 12a8.25 8.25 0 1 1-12.6-7.02L4.5 4.2l1.06 3.4A8.2 8.2 0 0 1 12 3.75a8.25 8.25 0 0 1 8.25 8.25Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 9.5c.3 2.6 2.1 4.4 4.7 4.7.8.1 1.3-.7 1-1.3l-.6-1-1.6.4a4 4 0 0 1-2-2l.4-1.6-1-.6c-.6-.3-1.4.2-1.3 1Z" />
            </svg>
          </a>
        </div>
        <p class="mt-4 text-center text-[11px] text-gray-400">© {{ date('Y') }} Nubelle Cosmetics</p>
      </div>
    </div>
  </div>

  {{-- ========================= POPUP CONNEXION ========================== --}}
  <div class="overlay" id="popupConnexion">
    <div class="popup">
      <div class="close-btn" onclick="fermerPopup('connexion')">&times;</div>
      <h2>Connexion à votre compte</h2>
      <form method="POST" action="{{ url('/connexion') }}">
        @csrf
        <input type="email" name="email" placeholder="Adresse e-mail" required />
        <input type="password" name="password" placeholder="Mot de passe" required />
        <button type="submit" class="btn-submit">Se connecter</button>
      </form>
      <p>Vous n'avez pas encore de compte ? <a href="#" onclick="switchTo('inscription'); return false;">Créer un compte</a></p>
    </div>
  </div>

  {{-- ========================= POPUP INSCRIPTION ======================== --}}
  <div class="overlay" id="popupInscription">
    <div class="popup">
      <div class="close-btn" onclick="fermerPopup('inscription')">&times;</div>
      <h2>Créer un compte</h2>
      <form method="POST" action="{{ url('/inscription') }}">
        @csrf
        <input type="text" name="name" placeholder="Nom complet" required />
        <input type="email" name="email" placeholder="Adresse e-mail" required />
        <input type="password" name="password" placeholder="Mot de passe" required />
        <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" required />
        <button type="submit" class="btn-submit">S'inscrire</button>
      </form>
      <p>Vous avez déjà un compte ? <a href="#" onclick="switchTo('connexion'); return false;">Se connecter</a></p>
    </div>
  </div>

  {{-- ============================ RECHERCHE ============================== --}}
  <div class="overlay" id="rechercheOverlay">
    <div class="popup sm:max-w-lg">
      <div class="close-btn" onclick="fermerRecherche()">&times;</div>
      <h2>Rechercher un produit</h2>
      <form action="{{ url('/recherche') }}" method="GET">
        <div class="relative">
          <input type="text" name="q" id="rechercheInput" autocomplete="off" placeholder="Nom, catégorie…"
                 class="w-full rounded-full border border-gray-200 py-3 pl-11 pr-4 text-sm text-gray-800 focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
          <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </div>
      </form>
      <div id="rechercheResultats" class="hidden mt-2 max-h-80 divide-y divide-gray-100 overflow-y-auto rounded-2xl"></div>
    </div>
  </div>

  {{-- =========================== PANIER (drawer) ======================== --}}
  @php
    $lignesApercu = $panierApercu ?? collect();
    $sousTotalApercu = $lignesApercu->sum(fn ($l) => (float) $l['produit']->prix * $l['quantite']);
  @endphp
  <div class="drawer-panier-backdrop" id="panierBackdrop" onclick="togglePanierMobile()"></div>
  <aside class="panier-mobile flex flex-col" id="panierMobile" role="dialog" aria-label="Mon panier">

    {{-- En-tête --}}
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
      <div>
        <h3 class="font-serif text-lg font-bold text-gray-900">Mon panier</h3>
        <p class="text-xs text-gray-400">{{ $nbPanier ?? 0 }} article{{ ($nbPanier ?? 0) > 1 ? 's' : '' }}</p>
      </div>
      <button type="button" onclick="togglePanierMobile()" aria-label="Fermer"
              class="flex h-9 w-9 items-center justify-center rounded-full text-gray-400 transition-colors hover:bg-cream hover:text-gray-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    {{-- Contenu --}}
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
  </aside>

  {{-- ============================ MESSAGES FLASH ========================= --}}
  @if (session('succes') || session('erreur'))
    <div class="max-w-6xl mx-auto px-5 pt-5">
      @if (session('succes'))
        <div class="flex items-center gap-3 rounded-2xl bg-green-50 px-5 py-4 text-sm font-medium text-green-700 ring-1 ring-green-100">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
          </svg>
          {{ session('succes') }}
        </div>
      @endif
      @if (session('erreur'))
        <div class="flex items-center gap-3 rounded-2xl bg-red-50 px-5 py-4 text-sm font-medium text-red-600 ring-1 ring-red-100">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
          </svg>
          {{ session('erreur') }}
        </div>
      @endif
    </div>
  @endif

  {{-- ============================== CONTENU ============================= --}}
  @yield('content')

  {{-- =============================== FOOTER ============================= --}}
  <footer class="footer">
    <div class="newsletter">
      <h3>Reste informé·e des nouveautés</h3>
      <p>Rejoins notre communauté et reçois nos offres en avant-première.</p>
      <form onsubmit="event.preventDefault(); alert('Merci pour votre inscription !');">
        <input type="email" placeholder="Ton adresse email" required>
        <button type="submit">Je m'inscris</button>
      </form>
    </div>
    <div class="reseaux">
      <a href="#"><img src="{{ asset('images/facebook.png') }}" alt="Facebook"></a>
      <a href="#"><img src="{{ asset('images/instagram.png') }}" alt="Instagram"></a>
    </div>
    <div class="footer-liens">
      <div class="liens">
        <a href="{{ url('/contact') }}">Contact</a>
        <a href="{{ url('/faq') }}">FAQ</a>
        <a href="{{ url('/livraison') }}">Livraison</a>
        <a href="{{ url('/retours') }}">Retours</a>
        <a href="{{ url('/compte') }}">Suivi commande</a>
        <a href="{{ url('/a-propos') }}">À propos</a>
      </div>
      <div class="legal">
        <a href="{{ url('/confidentialite') }}">Politique de confidentialité</a>
        <a href="{{ url('/conditions') }}">Conditions</a>
        <a href="#">Accessibilité</a>
        <a href="#">Cookies</a>
      </div>
      <p class="copyright">© {{ date('Y') }} NUBELLE Cosmetics</p>
    </div>
  </footer>

  @yield('popup')

  {{-- =========================== SCRIPTS GLOBAUX ======================== --}}
  <script>
    function toggleMenu() {
      const menu = document.getElementById("menuLateral");
      const menuMobile = document.getElementById("menuLateralMobile");
      const backdrop = document.getElementById("drawerBackdrop");
      if (menu) menu.classList.toggle("actif");
      if (menuMobile) menuMobile.classList.toggle("actif");
      if (backdrop) backdrop.classList.toggle("actif");
    }
    // Généralisé pour gérer les deux déclencheurs "compte" (desktop + mobile)
    // sans changer le comportement existant : ouvre/ferme le dropdown le plus proche.
    function toggleDropdown(event) {
      if (event) event.stopPropagation();
      const trigger = event ? event.currentTarget : null;
      const dropdown = trigger ? trigger.querySelector('.dropdown-menu') : document.getElementById("dropdownCompte");
      if (!dropdown) return;
      dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }
    function togglePanierMobile() {
      const panier = document.getElementById("panierMobile");
      const backdrop = document.getElementById("panierBackdrop");
      if (!panier) return;
      const ouvert = panier.classList.toggle("actif");
      if (backdrop) backdrop.classList.toggle("actif", ouvert);
      document.body.classList.toggle("overflow-hidden", ouvert);
    }
    document.addEventListener('click', function (event) {
      document.querySelectorAll('.compte').forEach(function (compte) {
        const dropdown = compte.querySelector('.dropdown-menu');
        if (dropdown && !compte.contains(event.target)) dropdown.style.display = 'none';
      });
    });

    // Petite pulsation du badge panier mobile — prête à être appelée
    // (ex : window.animerBadgePanier()) lors d'un futur ajout au panier.
    function animerBadgePanier() {
      const badge = document.getElementById('badgePanierMobile');
      if (!badge) return;
      badge.classList.remove('animate-badge-pop');
      void badge.offsetWidth;
      badge.classList.add('animate-badge-pop');
    }
    window.animerBadgePanier = animerBadgePanier;

    // ===================== Notifications (toasts) réutilisables =====================
    function notifierNubelle(message, type = 'succes') {
      let conteneur = document.getElementById('toastNubelle');
      if (!conteneur) {
        conteneur = document.createElement('div');
        conteneur.id = 'toastNubelle';
        conteneur.className = 'fixed bottom-5 right-5 z-[1100] flex flex-col gap-2';
        document.body.appendChild(conteneur);
      }
      const toast = document.createElement('div');
      toast.className = 'toast-nubelle';
      if (type === 'erreur') toast.classList.add('!bg-red-600');
      toast.textContent = message;
      conteneur.appendChild(toast);
      setTimeout(() => {
        toast.style.transition = 'opacity .3s ease, transform .3s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(8px)';
        setTimeout(() => toast.remove(), 300);
      }, 2600);
    }
    window.notifierNubelle = notifierNubelle;

    // ============ Ajout rapide au panier depuis les cartes produits ============
    // Réutilise l'endpoint existant /panier/ajouter/{slug} (réponse JSON) et
    // met à jour les badges + le mini-panier, sans quitter la page.
    async function ajoutRapidePanier(slug, bouton) {
      if (!slug || !bouton || bouton.disabled) return;
      bouton.disabled = true;
      bouton.classList.add('opacity-70');
      try {
        const reponse = await fetch(`/panier/ajouter/${slug}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          },
          body: JSON.stringify({ quantite: 1 }),
        });
        const data = await reponse.json();
        if (data.succes) {
          document.querySelectorAll('.badge-panier, #badgePanierMobile').forEach(el => { el.textContent = data.nbPanier; });
          animerBadgePanier();
          notifierNubelle(data.message || 'Produit ajouté au panier.', 'succes');
        } else {
          notifierNubelle(data.message || 'Produit indisponible.', 'erreur');
        }
      } catch (e) {
        notifierNubelle('Une erreur est survenue.', 'erreur');
      } finally {
        bouton.disabled = false;
        bouton.classList.remove('opacity-70');
      }
    }
    window.ajoutRapidePanier = ajoutRapidePanier;

    // Accordéon FAQ réutilisable (accueil, page FAQ complète…)
    function toggleFAQ(btn) {
      const answer = btn.nextElementSibling;
      const icon = btn.querySelector('.icon');
      const open = answer.style.maxHeight && answer.style.maxHeight !== '0px';
      document.querySelectorAll('.faq-answer').forEach(a => { a.style.maxHeight = '0px'; });
      document.querySelectorAll('.faq-btn .icon').forEach(i => { i.textContent = '+'; i.style.transform = 'rotate(0deg)'; });
      if (!open) {
        answer.style.maxHeight = answer.scrollHeight + 'px';
        icon.textContent = '–';
        icon.style.transform = 'rotate(180deg)';
      }
    }

    // Popups connexion / inscription
    function ouvrirPopup(type) {
      const id = type === 'connexion' ? 'popupConnexion' : 'popupInscription';
      document.getElementById(id).classList.add('actif');
    }
    function fermerPopup(type) {
      const id = type === 'connexion' ? 'popupConnexion' : 'popupInscription';
      document.getElementById(id).classList.remove('actif');
    }
    function switchTo(type) {
      fermerPopup(type === 'connexion' ? 'inscription' : 'connexion');
      ouvrirPopup(type);
    }

    // ====================== Recherche produits (header) ======================
    function ouvrirRecherche() {
      const overlay = document.getElementById('rechercheOverlay');
      overlay.classList.add('actif');
      document.getElementById('rechercheInput').focus();
    }
    function fermerRecherche() {
      document.getElementById('rechercheOverlay').classList.remove('actif');
    }
    document.getElementById('rechercheOverlay')?.addEventListener('click', (e) => {
      if (e.target.id === 'rechercheOverlay') fermerRecherche();
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') fermerRecherche();
    });

    (function () {
      const input = document.getElementById('rechercheInput');
      const boiteResultats = document.getElementById('rechercheResultats');
      if (!input || !boiteResultats) return;

      const urlSuggestions = @json(url('/recherche/suggestions'));
      let minuteur = null;
      let requeteEnCours = null;

      function echapper(texte) {
        const div = document.createElement('div');
        div.textContent = texte ?? '';
        return div.innerHTML;
      }

      function afficherSuggestions(produits, terme) {
        if (!produits.length) {
          boiteResultats.innerHTML = `<p class="p-4 text-sm text-gray-400">Aucun produit trouvé pour « ${echapper(terme)} ».</p>`;
          boiteResultats.classList.remove('hidden');
          return;
        }
        boiteResultats.innerHTML = produits.map(p => `
          <a href="${p.url}" class="flex items-center gap-3 p-3 transition-colors hover:bg-cream/60">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-cream/50">
              <img src="${p.image ?? ''}" alt="" class="h-full w-full object-contain p-1">
            </span>
            <span class="min-w-0 flex-1">
              <span class="block truncate text-sm font-medium text-gray-900">${echapper(p.nom)}</span>
              <span class="block text-xs text-gray-400">${echapper(p.categorie ?? '')}</span>
            </span>
            <span class="shrink-0 text-sm font-semibold text-ember">${echapper(p.prixFormate)}</span>
          </a>
        `).join('');
        boiteResultats.classList.remove('hidden');
      }

      function chercher(terme) {
        if (requeteEnCours) requeteEnCours.abort();
        requeteEnCours = new AbortController();
        fetch(urlSuggestions + '?q=' + encodeURIComponent(terme), { signal: requeteEnCours.signal })
          .then(r => r.json())
          .then(produits => afficherSuggestions(produits, terme))
          .catch(e => { if (e.name !== 'AbortError') console.error(e); });
      }

      input.addEventListener('input', () => {
        clearTimeout(minuteur);
        const terme = input.value.trim();
        if (terme.length < 2) {
          boiteResultats.innerHTML = '';
          boiteResultats.classList.add('hidden');
          return;
        }
        minuteur = setTimeout(() => chercher(terme), 300);
      });
    })();

    // Carrousel du bandeau
    (function () {
      const textes = document.querySelectorAll('.texte-carousel');
      if (textes.length > 1) {
        let i = 0;
        setInterval(() => {
          textes[i].classList.remove('active');
          i = (i + 1) % textes.length;
          textes[i].classList.add('active');
        }, 4000);
      }
    })();

    // Apparition au scroll (.reveal) — global car x-product-card et d'autres
    // sections l'utilisent sur toutes les pages, pas seulement l'accueil.
    (function () {
      const elements = document.querySelectorAll('.reveal');
      if (!elements.length) return;
      const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
      }, { threshold: 0.12 });
      elements.forEach(el => obs.observe(el));
    })();
  </script>

  @stack('scripts')
</body>
</html>
