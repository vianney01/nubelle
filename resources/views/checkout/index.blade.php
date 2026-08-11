@extends('layouts.app')

@section('title', 'Finaliser ma commande — NUBELLE Cosmetics')

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'Mon panier', 'url' => url('/panier')],
    ['label' => 'Commande', 'url' => null],
  ]" />

  <section class="max-w-6xl mx-auto px-5 py-8">
    <h1 class="font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Finaliser ma commande</h1>

    <form method="POST" action="{{ route('checkout.valider') }}" class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-[1fr_360px]">
      @csrf

      <div class="space-y-6">

        {{-- ============================ 1. ADRESSE ============================ --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
          <div class="mb-5 flex items-center gap-3">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-ember text-sm font-bold text-white">1</span>
            <h2 class="font-serif text-lg font-bold text-gray-900">Adresse de livraison</h2>
          </div>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <input type="text" name="prenom" value="{{ old('prenom', $client->prenom ?? '') }}" required placeholder="Prénom"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('prenom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
              <input type="text" name="nom" value="{{ old('nom', $client->nom ?? '') }}" required placeholder="Nom"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('nom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
              <input type="email" value="{{ auth()->user()->email }}" readonly title="Adresse de votre compte"
                     class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 focus:outline-none">
            </div>
            <div class="sm:col-span-2">
              <select name="commune_id" id="communeSelect" required onchange="majLivraisonCheckout()"
                      class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
                <option value="">Choisir ma commune…</option>
                @foreach ($communes as $commune)
                  <option value="{{ $commune->id }}" data-prix="{{ $commune->prix }}" {{ (string) old('commune_id') === (string) $commune->id ? 'selected' : '' }}>
                    {{ $commune->nom }}
                  </option>
                @endforeach
              </select>
              @error('commune_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
              <input type="text" name="ville" value="{{ old('ville', $client->ville ?? '') }}" required placeholder="Ville"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('ville') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <input type="text" name="code_postal" value="{{ old('code_postal', $client->code_postal ?? '') }}" placeholder="Code postal"
                   class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <div class="sm:col-span-2">
              <input type="tel" name="telephone" value="{{ old('telephone', $client->telephone ?? '') }}" required placeholder="Téléphone"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('telephone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
          </div>
        </div>

        {{-- ============================ 2. LIVRAISON ============================ --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
          <div class="mb-5 flex items-center gap-3">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-ember text-sm font-bold text-white">2</span>
            <h2 class="font-serif text-lg font-bold text-gray-900">Mode de livraison</h2>
          </div>
          <div class="space-y-3">

            {{-- Livraison express — finalisée sur WhatsApp --}}
            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-gray-200 px-4 py-3 has-[:checked]:border-ember has-[:checked]:bg-cream/40">
              <span class="flex items-center gap-3">
                <input type="radio" name="mode_livraison" value="express" onchange="majLivraisonCheckout()" class="text-ember focus:ring-ember" {{ old('mode_livraison') === 'express' ? 'checked' : '' }}>
                <span class="text-sm font-medium text-gray-800">Livraison express — 24h</span>
              </span>
              <span class="text-xs font-semibold text-green-600">Prix sur WhatsApp</span>
            </label>

            {{-- Livraison normale — prix selon la commune choisie plus haut --}}
            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-gray-200 px-4 py-3 has-[:checked]:border-ember has-[:checked]:bg-cream/40">
              <span class="flex items-center gap-3">
                <input type="radio" name="mode_livraison" value="normale" onchange="majLivraisonCheckout()" class="text-ember focus:ring-ember" {{ old('mode_livraison', 'normale') === 'normale' ? 'checked' : '' }}>
                <span class="text-sm font-medium text-gray-800">Livraison normale</span>
              </span>
              <span id="prixNormale" class="text-xs text-gray-400">selon votre commune</span>
            </label>

            {{-- Expédition — finalisée sur WhatsApp --}}
            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-gray-200 px-4 py-3 has-[:checked]:border-ember has-[:checked]:bg-cream/40">
              <span class="flex items-center gap-3">
                <input type="radio" name="mode_livraison" value="expedition" onchange="majLivraisonCheckout()" class="text-ember focus:ring-ember" {{ old('mode_livraison') === 'expedition' ? 'checked' : '' }}>
                <span class="text-sm font-medium text-gray-800">Expédition</span>
              </span>
              <span class="text-xs font-semibold text-green-600">Prix sur WhatsApp</span>
            </label>

            @error('mode_livraison') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
          </div>
          <p class="mt-3 flex items-center gap-1.5 text-xs text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.3 4.4-1.2A10 10 0 1 0 12 2Z" opacity=".15"/><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.3 4.4-1.2A10 10 0 1 0 12 2Zm0 2a8 8 0 1 1-4.2 14.8l-.3-.2-2.4.6.7-2.3-.2-.3A8 8 0 0 1 12 4Z"/></svg>
            Votre commande sera finalisée sur WhatsApp après confirmation.
          </p>
        </div>

        {{-- ============================ 3. PAIEMENT ============================ --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
          <div class="mb-5 flex items-center gap-3">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-ember text-sm font-bold text-white">3</span>
            <h2 class="font-serif text-lg font-bold text-gray-900">Paiement</h2>
          </div>
          <div class="space-y-3">
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 has-[:checked]:border-ember has-[:checked]:bg-cream/40">
              <input type="radio" name="mode_paiement" value="carte" checked onchange="document.getElementById('champsCarte').classList.remove('hidden')" class="text-ember focus:ring-ember">
              <span class="text-sm font-medium text-gray-800">Carte bancaire (Visa, Mastercard)</span>
            </label>
            <div id="champsCarte" class="grid grid-cols-1 gap-3 pl-7 sm:grid-cols-2">
              <input type="text" placeholder="Numéro de carte" class="sm:col-span-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              <input type="text" placeholder="MM/AA" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              <input type="text" placeholder="CVC" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            </div>
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 has-[:checked]:border-ember has-[:checked]:bg-cream/40">
              <input type="radio" name="mode_paiement" value="mobile_money" onchange="document.getElementById('champsCarte').classList.add('hidden')" class="text-ember focus:ring-ember">
              <span class="text-sm font-medium text-gray-800">Mobile Money</span>
            </label>
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 has-[:checked]:border-ember has-[:checked]:bg-cream/40">
              <input type="radio" name="mode_paiement" value="livraison" onchange="document.getElementById('champsCarte').classList.add('hidden')" class="text-ember focus:ring-ember">
              <span class="text-sm font-medium text-gray-800">Paiement à la livraison</span>
            </label>
            @error('mode_paiement') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      {{-- ============================ 4. RÉCAPITULATIF ============================ --}}
      <aside class="h-fit rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:sticky lg:top-24">
        <h2 class="font-serif text-lg font-bold text-gray-900">Récapitulatif</h2>

        <div class="mt-4 space-y-3">
          @foreach ($lignes as $ligne)
            @php $p = $ligne['produit']; @endphp
            <div class="flex items-center gap-3">
              <div class="relative h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-cream/50">
                <img src="{{ $p['image'] }}" alt="{{ $p->nom }}" class="h-full w-full object-contain p-1">
                <span class="absolute -right-1.5 -top-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-gray-900 text-[10px] font-bold text-white">{{ $ligne['quantite'] }}</span>
              </div>
              <div class="flex-1">
                <p class="line-clamp-1 text-sm font-medium text-gray-800">{{ $p->nom }}</p>
                <p class="text-xs text-gray-400">{{ number_format($p->prix, 0, ',', ' ') }} FCFA</p>
              </div>
              <p class="text-sm font-semibold text-ember">{{ number_format($p->prix * $ligne['quantite'], 0, ',', ' ') }} FCFA</p>
            </div>
          @endforeach
        </div>

        <div class="mt-5 space-y-2 border-t border-gray-100 pt-4 text-sm">
          <div class="flex justify-between text-gray-500">
            <span>Sous-total</span>
            <span id="sousTotalCheckout" class="font-medium text-gray-900" data-valeur="{{ $promo['sous_total'] }}">{{ number_format($promo['sous_total'], 0, ',', ' ') }} FCFA</span>
          </div>
          @if ($promo['reduction_totale'] > 0)
            <div class="flex justify-between text-green-600">
              <span>Réduction{{ $promo['code_promo'] ? ' ('.$promo['code_promo']->code.')' : '' }}</span>
              <span class="font-semibold">− {{ number_format($promo['reduction_totale'], 0, ',', ' ') }} FCFA</span>
            </div>
          @endif
          <div class="flex justify-between text-gray-500">
            <span>Livraison</span>
            <span id="livraisonCheckout" class="font-medium text-gray-900">Gratuite</span>
          </div>
        </div>

        <div class="mt-4 flex justify-between border-t border-gray-100 pt-4 text-base font-bold text-gray-900">
          <span>Total</span>
          <span id="totalCheckout" class="font-serif text-ember">{{ number_format($promo['total'], 0, ',', ' ') }} FCFA</span>
        </div>

        <button type="submit" class="mt-6 block w-full rounded-full bg-gradient-to-r from-ember to-sienna py-3.5 text-center text-sm font-semibold text-white shadow-lg shadow-ember/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl">
          Confirmer ma commande
        </button>
        <p class="mt-3 flex items-center justify-center gap-1.5 text-center text-[11px] text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.3 4.4-1.2A10 10 0 1 0 12 2Zm0 2a8 8 0 1 1-4.2 14.8l-.3-.2-2.4.6.7-2.3-.2-.3A8 8 0 0 1 12 4Z"/></svg>
          Vous serez redirigé vers WhatsApp pour finaliser.
        </p>
      </aside>
    </form>
  </section>

@endsection

@push('scripts')
<script>
  const reductionCheckout = {{ $promo['reduction_totale'] }};

  function formatFCFACheckout(n) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
  }

  function majLivraisonCheckout() {
    const mode = document.querySelector('input[name="mode_livraison"]:checked')?.value ?? 'normale';
    const select = document.getElementById('communeSelect');
    const sousTotal = parseFloat(document.getElementById('sousTotalCheckout').dataset.valeur);

    // Prix de la commune choisie dans la section « Adresse de livraison ».
    const prixCommune = (select && select.value)
      ? (parseFloat(select.options[select.selectedIndex].dataset.prix || '0') || 0)
      : null;

    // Étiquette de prix à côté de l'option « Livraison normale ».
    const prixNormale = document.getElementById('prixNormale');
    if (prixNormale) {
      prixNormale.textContent = (prixCommune === null)
        ? 'selon votre commune'
        : (prixCommune === 0 ? 'Gratuite' : formatFCFACheckout(prixCommune));
    }

    let cout = 0;
    let libelle = 'À convenir sur WhatsApp';

    if (mode === 'normale') {
      if (prixCommune === null) {
        libelle = 'Choisir une commune';
      } else {
        cout = prixCommune;
        libelle = cout === 0 ? 'Gratuite' : formatFCFACheckout(cout);
      }
    }

    const total = Math.max(0, sousTotal - reductionCheckout) + cout;
    document.getElementById('livraisonCheckout').textContent = libelle;
    document.getElementById('totalCheckout').textContent = formatFCFACheckout(total);
  }

  document.addEventListener('DOMContentLoaded', majLivraisonCheckout);
</script>
@endpush
