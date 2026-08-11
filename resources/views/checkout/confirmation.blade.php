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
      @if ($whatsappUrl)
        Dernière étape : <span class="font-semibold text-gray-800">finalisez-la sur WhatsApp</span> pour confirmer la livraison et le paiement.
      @else
        Un e-mail de confirmation sera envoyé à <span class="font-semibold text-gray-800">{{ $commande->client->email ?? '' }}</span>.
      @endif
    </p>

    @if ($whatsappUrl)
      <a id="lienWhatsapp" href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
         class="mt-6 inline-flex items-center gap-2.5 rounded-full bg-[#25D366] px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-green-500/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl">
        <svg viewBox="0 0 32 32" fill="currentColor" class="h-5 w-5" aria-hidden="true">
          <path d="M16.04 3.2c-7.1 0-12.86 5.76-12.86 12.86 0 2.27.6 4.49 1.73 6.44L3.2 28.8l6.47-1.69a12.8 12.8 0 0 0 6.37 1.62h.01c7.1 0 12.86-5.76 12.86-12.86 0-3.44-1.34-6.67-3.77-9.1a12.78 12.78 0 0 0-9.1-3.77Zm7.91 15.32c-.32-.16-1.9-.94-2.2-1.05-.3-.11-.51-.16-.72.16-.21.32-.83 1.05-1.02 1.26-.19.21-.37.24-.69.08-.32-.16-1.36-.5-2.59-1.6-.96-.85-1.6-1.91-1.79-2.23-.19-.32-.02-.5.14-.65.14-.14.32-.37.48-.56.16-.19.21-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.74-.99-2.38-.26-.62-.52-.54-.72-.55l-.61-.01c-.21 0-.56.08-.85.4-.29.32-1.11 1.09-1.11 2.66 0 1.57 1.14 3.08 1.3 3.3.16.21 2.25 3.43 5.44 4.81.76.33 1.35.52 1.81.67.76.24 1.45.21 2 .13.61-.09 1.9-.78 2.17-1.53.27-.75.27-1.39.19-1.53-.08-.13-.29-.21-.61-.37Z"/>
        </svg>
        Finaliser sur WhatsApp
      </a>
      <p class="mt-2 text-xs text-gray-400">La fenêtre WhatsApp s'ouvre automatiquement. Si ce n'est pas le cas, cliquez sur le bouton.</p>
    @endif

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
          <span>Livraison <span class="text-xs text-gray-400">({{ $commande->methodeLivraison() }})</span></span>
          <span class="text-gray-800">
            @if ($commande->mode_livraison === 'normale')
              {{ $commande->frais_livraison > 0 ? number_format($commande->frais_livraison, 0, ',', ' ').' FCFA' : 'Gratuite' }}
            @else
              À convenir sur WhatsApp
            @endif
          </span>
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

  @if ($whatsappUrl)
    @push('scripts')
    <script>
      // Ouvre WhatsApp automatiquement avec le récapitulatif pré-rempli.
      // On redirige l'onglet courant (une navigation n'est pas bloquée par le
      // navigateur, contrairement à window.open déclenché sans clic). Un court
      // délai laisse le temps de voir la confirmation de commande.
      (function () {
        var url = @json($whatsappUrl);
        setTimeout(function () { window.location.href = url; }, 1500);
      })();
    </script>
    @endpush
  @endif

@endsection
