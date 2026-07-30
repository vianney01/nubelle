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
              <input type="text" name="prenom" value="{{ old('prenom') }}" required placeholder="Prénom"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('prenom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
              <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="Nom"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('nom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
              <input type="email" name="email" value="{{ old('email') }}" required placeholder="Adresse e-mail"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
              <input type="text" name="adresse" value="{{ old('adresse') }}" required placeholder="Adresse"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('adresse') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
              <input type="text" name="ville" value="{{ old('ville') }}" required placeholder="Ville"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('ville') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <input type="text" name="code_postal" value="{{ old('code_postal') }}" placeholder="Code postal"
                   class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <div class="sm:col-span-2">
              <input type="tel" name="telephone" value="{{ old('telephone') }}" required placeholder="Téléphone"
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
            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-gray-200 px-4 py-3 has-[:checked]:border-ember has-[:checked]:bg-cream/40">
              <span class="flex items-center gap-3">
                <input type="radio" name="mode_livraison" value="gratuite" checked onchange="recalculerLivraisonCheckout()" class="text-ember focus:ring-ember">
                <span class="text-sm font-medium text-gray-800">Point relais — 24 à 72h</span>
              </span>
              <span class="text-sm font-semibold text-green-600">Gratuite</span>
            </label>
            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-gray-200 px-4 py-3 has-[:checked]:border-ember has-[:checked]:bg-cream/40">
              <span class="flex items-center gap-3">
                <input type="radio" name="mode_livraison" value="express" onchange="recalculerLivraisonCheckout()" class="text-ember focus:ring-ember">
                <span class="text-sm font-medium text-gray-800">Livraison express — 24h</span>
              </span>
              <span class="text-sm font-semibold text-gray-800">2 500 FCFA</span>
            </label>
            @error('mode_livraison') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
          </div>
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
        <p class="mt-3 text-center text-[11px] text-gray-400">Paiement sécurisé — vos données ne sont jamais partagées.</p>
      </aside>
    </form>
  </section>

@endsection

@push('scripts')
<script>
  const coutsLivraisonCheckout = @json($coutsLivraison);
  const reductionCheckout = {{ $promo['reduction_totale'] }};

  function formatFCFACheckout(n) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
  }

  function recalculerLivraisonCheckout() {
    const mode = document.querySelector('input[name="mode_livraison"]:checked')?.value ?? 'gratuite';
    const cout = coutsLivraisonCheckout[mode] ?? 0;
    const sousTotal = parseFloat(document.getElementById('sousTotalCheckout').dataset.valeur);
    const total = Math.max(0, sousTotal - reductionCheckout) + cout;

    document.getElementById('livraisonCheckout').textContent = cout === 0 ? 'Gratuite' : formatFCFACheckout(cout);
    document.getElementById('totalCheckout').textContent = formatFCFACheckout(total);
  }
</script>
@endpush
