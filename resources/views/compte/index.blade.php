@extends('layouts.app')

@section('title', 'Mon compte — NUBELLE Cosmetics')

@php
  $statutLabels = [
    'en_attente'     => 'En attente',
    'en_preparation' => 'En préparation',
    'expediee'       => 'Expédiée',
    'livree'         => 'Livrée',
    'annulee'        => 'Annulée',
  ];
  $statutStyles = [
    'en_attente'     => 'bg-gray-100 text-gray-600',
    'en_preparation' => 'bg-blue-50 text-blue-700',
    'expediee'       => 'bg-tangerine/10 text-ember',
    'livree'         => 'bg-green-50 text-green-700',
    'annulee'        => 'bg-red-50 text-red-600',
  ];
  // Couleurs des compteurs dans la barre de filtres (façon onglets Shopify).
  $statutBadges = [
    'en_attente'     => 'bg-amber-100 text-amber-700',
    'en_preparation' => 'bg-blue-100 text-blue-700',
    'expediee'       => 'bg-tangerine/20 text-ember',
    'livree'         => 'bg-green-100 text-green-700',
    'annulee'        => 'bg-red-100 text-red-600',
  ];
@endphp

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'Mon compte', 'url' => null],
  ]" />

  <section class="max-w-6xl mx-auto px-5 py-8">
    <h1 class="font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Mon compte</h1>
    <p class="mt-2 text-gray-500">Bienvenue{{ $client ? ', '.$client->prenom : '' }} — gérez vos commandes, adresses et préférences.</p>

    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-[240px_1fr]">

      {{-- ============================ NAVIGATION ============================ --}}
      <nav class="flex gap-2 overflow-x-auto lg:flex-col lg:overflow-visible [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        @foreach ([
          ['id' => 'commandes', 'label' => 'Mes commandes', 'icone' => 'M9 14.25 15 15m-3-3v3m-6.75-3h.008v.008H8.25V12Zm0 0H21M3 6.75h18M3 6.75v12.75A2.25 2.25 0 0 0 5.25 21.75h13.5A2.25 2.25 0 0 0 21 19.5V6.75'],
          ['id' => 'adresses', 'label' => 'Mes adresses', 'icone' => 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z'],
          ['id' => 'favoris', 'label' => 'Mes favoris', 'icone' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z'],
          ['id' => 'profil', 'label' => 'Mon profil', 'icone' => 'M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
          ['id' => 'securite', 'label' => 'Sécurité', 'icone' => 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z'],
        ] as $onglet)
          <button type="button" onclick="basculerOngletCompte('{{ $onglet['id'] }}', this)"
                  class="onglet-compte-btn flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-600 transition-colors hover:bg-cream/60 lg:w-full {{ $loop->first ? 'bg-cream/70 text-ember' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="{{ $onglet['icone'] }}" />
            </svg>
            <span class="whitespace-nowrap">{{ $onglet['label'] }}</span>
          </button>
        @endforeach

        <a href="{{ url('/connexion') }}" class="mt-2 hidden items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-red-500 transition-colors hover:bg-red-50 lg:flex">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
          </svg>
          Se déconnecter
        </a>
      </nav>

      {{-- ============================ CONTENU ============================ --}}
      <div>

        {{-- Commandes --}}
        <div id="onglet-compte-commandes" class="onglet-compte-panneau space-y-3">
          @if ($commandes->isEmpty())
            <div class="rounded-2xl bg-cream/40 p-10 text-center">
              <p class="font-serif text-lg text-gray-700">Aucune commande pour le moment.</p>
              <p class="mt-1 text-sm text-gray-500">
                @if ($client)
                  Vos prochaines commandes apparaîtront ici.
                @else
                  Passez une commande pour la retrouver ici.
                @endif
              </p>
              <a href="{{ url('/produits') }}" class="mt-4 inline-block rounded-full bg-gray-900 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-ember">
                Découvrir la boutique
              </a>
            </div>
          @else
            @php $compteurs = $commandes->countBy('statut'); @endphp

            {{-- Barre de filtres par statut (façon onglets) --}}
            <div class="mb-4 flex gap-1 overflow-x-auto rounded-full bg-white p-1.5 shadow-sm ring-1 ring-black/5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
              <button type="button" onclick="filtrerCommandes('toutes', this)"
                      class="filtre-commande flex shrink-0 items-center gap-2 rounded-full bg-cream px-3.5 py-2 text-sm font-semibold text-ember transition-colors">
                Toutes
                <span class="rounded-full bg-gray-100 px-1.5 py-0.5 text-[11px] font-bold text-gray-600">{{ $commandes->count() }}</span>
              </button>
              @foreach ($statutLabels as $cle => $label)
                @php $n = $compteurs[$cle] ?? 0; @endphp
                @if ($n > 0)
                  <button type="button" onclick="filtrerCommandes('{{ $cle }}', this)"
                          class="filtre-commande flex shrink-0 items-center gap-2 rounded-full px-3.5 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-ember">
                    {{ $label }}
                    <span class="rounded-full px-1.5 py-0.5 text-[11px] font-bold {{ $statutBadges[$cle] ?? 'bg-gray-100 text-gray-600' }}">{{ $n }}</span>
                  </button>
                @endif
              @endforeach
            </div>

            {{-- Liste des commandes (filtrées côté client) --}}
            <div id="listeCommandes" class="space-y-3">
              @foreach ($commandes as $commande)
                <div data-statut="{{ $commande->statut }}" class="carte-commande flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                  <div>
                    <p class="font-semibold text-gray-900">Commande {{ $commande->numero }}</p>
                    <p class="text-xs text-gray-400">{{ $commande->created_at->translatedFormat('d M Y') }}</p>
                  </div>
                  <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statutStyles[$commande->statut] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $statutLabels[$commande->statut] ?? $commande->statut }}
                  </span>
                  <span class="font-serif font-bold text-ember">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</span>
                  <a href="{{ route('checkout.confirmation', $commande) }}" class="text-sm font-semibold text-gray-700 hover:text-ember">Détails →</a>
                </div>
              @endforeach
            </div>

            <div id="aucuneCommandeFiltre" class="hidden rounded-2xl bg-cream/40 p-8 text-center text-sm text-gray-500">
              Aucune commande avec ce statut.
            </div>
          @endif
        </div>

        {{-- Adresses --}}
        <div id="onglet-compte-adresses" class="onglet-compte-panneau hidden space-y-3">
          @if (count($adresses))
            <div class="grid gap-4 sm:grid-cols-2">
              @foreach ($adresses as $adresse)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                  <div class="flex items-center justify-between">
                    <p class="font-semibold text-gray-900">{{ $adresse['libelle'] }}</p>
                    @if ($adresse['defaut'])
                      <span class="rounded-full bg-cream px-2 py-0.5 text-[10px] font-bold uppercase text-ember">Par défaut</span>
                    @endif
                  </div>
                  <p class="mt-2 text-sm text-gray-500">{{ $adresse['nom'] }}</p>
                  <p class="text-sm text-gray-500">{{ $adresse['details'] }}</p>
                </div>
              @endforeach
            </div>
          @else
            <div class="rounded-2xl bg-cream/40 p-10 text-center">
              <p class="font-serif text-lg text-gray-700">Aucune adresse enregistrée.</p>
              <p class="mt-1 text-sm text-gray-500">Votre adresse de livraison sera enregistrée lors de votre première commande.</p>
            </div>
          @endif
        </div>

        {{-- Favoris --}}
        <div id="onglet-compte-favoris" class="onglet-compte-panneau hidden">
          @if ($favoris->isEmpty())
            <div class="rounded-2xl bg-cream/40 p-10 text-center">
              <p class="font-serif text-lg text-gray-700">Aucun produit dans vos favoris.</p>
              <p class="mt-1 text-sm text-gray-500">Parcourez la boutique pour découvrir nos produits.</p>
            </div>
          @else
            <p class="mb-4 text-sm text-gray-500">Nos produits susceptibles de vous plaire</p>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
              @foreach ($favoris as $p)
                <x-product-card :produit="$p" />
              @endforeach
            </div>
          @endif
        </div>

        {{-- Profil --}}
        <div id="onglet-compte-profil" class="onglet-compte-panneau hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
          <h2 class="font-serif text-lg font-bold text-gray-900">Informations personnelles</h2>
          @if ($client)
            <form onsubmit="event.preventDefault(); alert('Profil mis à jour (simulation).');" class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
              <input type="text" value="{{ $client->prenom }}" placeholder="Prénom" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              <input type="text" value="{{ $client->nom }}" placeholder="Nom" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              <input type="email" value="{{ $client->email }}" placeholder="E-mail" class="sm:col-span-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              <input type="tel" value="{{ $client->telephone }}" placeholder="Téléphone" class="sm:col-span-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              <button type="submit" class="sm:col-span-2 mt-2 rounded-full bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-ember sm:w-fit">Enregistrer</button>
            </form>
          @else
            <p class="mt-5 text-sm text-gray-500">Passez une commande pour créer votre fiche client et retrouver vos informations ici.</p>
          @endif
        </div>

        {{-- Sécurité --}}
        <div id="onglet-compte-securite" class="onglet-compte-panneau hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
          <h2 class="font-serif text-lg font-bold text-gray-900">Modifier le mot de passe</h2>
          <form onsubmit="event.preventDefault(); alert('Mot de passe mis à jour (simulation).');" class="mt-5 space-y-4 max-w-sm">
            <input type="password" placeholder="Mot de passe actuel" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <input type="password" placeholder="Nouveau mot de passe" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <input type="password" placeholder="Confirmer le nouveau mot de passe" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <button type="submit" class="rounded-full bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-ember">Mettre à jour</button>
          </form>
          <a href="{{ url('/connexion') }}" class="mt-6 inline-block text-sm font-semibold text-red-500 hover:text-red-600 lg:hidden">Se déconnecter</a>
        </div>
      </div>
    </div>
  </section>

@endsection

@push('scripts')
<script>
  function basculerOngletCompte(nom, btn) {
    document.querySelectorAll('.onglet-compte-panneau').forEach(p => p.classList.add('hidden'));
    document.getElementById('onglet-compte-' + nom).classList.remove('hidden');
    document.querySelectorAll('.onglet-compte-btn').forEach(b => b.classList.remove('bg-cream/70', 'text-ember'));
    if (btn) btn.classList.add('bg-cream/70', 'text-ember');
  }

  // Filtre des commandes par statut (barre d'onglets, côté client).
  function filtrerCommandes(statut, btn) {
    document.querySelectorAll('.filtre-commande').forEach(b => {
      b.classList.remove('bg-cream', 'text-ember', 'font-semibold');
      b.classList.add('text-gray-500', 'font-medium');
    });
    btn.classList.add('bg-cream', 'text-ember', 'font-semibold');
    btn.classList.remove('text-gray-500', 'font-medium');

    let visibles = 0;
    document.querySelectorAll('#listeCommandes .carte-commande').forEach(carte => {
      const ok = statut === 'toutes' || carte.dataset.statut === statut;
      carte.classList.toggle('hidden', !ok);
      if (ok) visibles++;
    });

    const vide = document.getElementById('aucuneCommandeFiltre');
    if (vide) vide.classList.toggle('hidden', visibles > 0);
  }
</script>
@endpush
