@extends('layouts.app')

@section('title', 'Mon panier — NUBELLE Cosmetics')

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'Mon panier', 'url' => null],
  ]" />

  <section class="max-w-6xl mx-auto px-5 py-8">
    <h1 class="font-serif text-3xl font-bold text-gray-900 sm:text-4xl">Mon panier</h1>

    @if ($lignes->isEmpty())
      <div class="mt-10 rounded-3xl bg-cream/40 py-20 text-center">
        <p class="font-serif text-xl text-gray-700">Votre panier est vide.</p>
        <a href="{{ url('/produits') }}" class="mt-4 inline-block rounded-full bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-ember">
          Découvrir la boutique
        </a>
      </div>
    @else
      <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-[1fr_360px]">

        {{-- ============================ ARTICLES ============================ --}}
        <div class="space-y-4" id="lignesPanier">
          @foreach ($lignes as $ligne)
            @php $p = $ligne['produit']; @endphp
            <div class="ligne-panier flex flex-wrap items-center gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-black/5 sm:flex-nowrap"
                 data-slug="{{ $p->slug }}" data-prix="{{ $p->prix }}">
              <a href="{{ url('/produit/'.$p->slug) }}" class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-cream/50">
                <img src="{{ $p['image'] }}" alt="{{ $p->nom }}" class="h-full w-full object-contain p-1.5">
              </a>

              <div class="min-w-[140px] flex-1">
                <a href="{{ url('/produit/'.$p->slug) }}" class="font-semibold text-gray-900 hover:text-ember transition-colors">{{ $p->nom }}</a>
                <p class="mt-0.5 text-xs text-gray-400">{{ $p->categorie->nom ?? '—' }}</p>
                <p class="ligne-prix-unitaire mt-1 text-sm font-medium text-ember">{{ number_format($p->prix, 0, ',', ' ') }} FCFA</p>
              </div>

              <div class="flex items-center gap-3 rounded-full border border-gray-200 px-3 py-1.5">
                <button type="button" onclick="changerQuantiteLigne(this, -1)" class="text-lg text-gray-500 hover:text-ember">−</button>
                <span class="qte w-4 text-center text-sm font-semibold">{{ $ligne['quantite'] }}</span>
                <button type="button" onclick="changerQuantiteLigne(this, 1)" class="text-lg text-gray-500 hover:text-ember">+</button>
              </div>

              <span class="ligne-total w-24 text-right font-serif font-bold text-ember">{{ number_format($p->prix * $ligne['quantite'], 0, ',', ' ') }} FCFA</span>

              <button type="button" onclick="supprimerLigne(this)" aria-label="Retirer" class="text-gray-300 transition-colors hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          @endforeach

          <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ url('/produits') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-ember hover:text-sienna transition-colors">
              ← Continuer mes achats
            </a>
            <form method="POST" action="{{ route('panier.vider') }}" onsubmit="return confirm('Vider entièrement votre panier ?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-sm font-medium text-gray-400 transition-colors hover:text-red-500">
                Vider le panier
              </button>
            </form>
          </div>
        </div>

        {{-- ============================ RÉSUMÉ ============================ --}}
        <aside class="h-fit rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:sticky lg:top-24">
          <h2 class="font-serif text-xl font-bold text-gray-900">Résumé de la commande</h2>

          <div class="mt-5 space-y-3 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-500">Sous-total</span>
              <span id="sousTotalAffiche" class="font-medium text-gray-900">{{ number_format($promo['sous_total'], 0, ',', ' ') }} FCFA</span>
            </div>

            {{-- Réduction (code promo + remises membres automatiques) --}}
            <div id="ligneReduction" class="flex justify-between {{ $promo['reduction_totale'] > 0 ? '' : 'hidden' }}">
              <span class="text-gray-500">Réduction</span>
              <span id="reductionAffiche" class="font-semibold text-green-600">− {{ number_format($promo['reduction_totale'], 0, ',', ' ') }} FCFA</span>
            </div>

            <div class="flex justify-between items-center">
              <span class="text-gray-500">Livraison</span>
              <select id="selectLivraison" onchange="recalculerLivraison()" class="rounded-lg border border-gray-200 px-2 py-1 text-xs focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
                <option value="gratuite" data-cout="0">Point relais — Gratuite</option>
                <option value="express" data-cout="2500">Express (24h) — 2 500 FCFA</option>
              </select>
            </div>
          </div>

          {{-- Code promo : appliqué (retirable) ou champ de saisie --}}
          @if ($promo['code_promo'] && $promo['reduction_code'] > 0)
            <div class="mt-5 flex items-center justify-between rounded-xl bg-green-50 px-3.5 py-3 ring-1 ring-green-100">
              <div class="text-xs">
                <p class="font-semibold text-green-700">Code « {{ $promo['code_promo']->code }} » appliqué</p>
                <p class="text-green-600">−{{ number_format($promo['reduction_code'], 0, ',', ' ') }} FCFA sur votre commande</p>
              </div>
              <form method="POST" action="{{ route('panier.code.retirer') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-semibold text-gray-400 transition-colors hover:text-red-500">Retirer</button>
              </form>
            </div>
          @else
            <form method="POST" action="{{ route('panier.code.appliquer') }}" class="mt-5">
              @csrf
              <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Code promo</label>
              <div class="flex gap-2">
                <input type="text" name="code" value="{{ old('code') }}" placeholder="Ex : BIENVENUE10"
                       class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm uppercase focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
                <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-ember">Appliquer</button>
              </div>
            </form>
          @endif

          @if ($promo['reduction_membre'] > 0)
            <p class="mt-3 flex items-center gap-1.5 text-xs font-medium text-ember">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
              Remise membre appliquée : −{{ number_format($promo['reduction_membre'], 0, ',', ' ') }} FCFA
            </p>
          @endif

          <div class="mt-5 flex justify-between border-t border-gray-100 pt-4 text-base font-bold text-gray-900">
            <span>Total</span>
            <span id="totalAffiche" class="font-serif text-ember">{{ number_format($promo['total'], 0, ',', ' ') }} FCFA</span>
          </div>

          <a href="{{ url('/checkout') }}" class="mt-6 block rounded-full bg-gradient-to-r from-ember to-sienna py-3.5 text-center text-sm font-semibold text-white shadow-lg shadow-ember/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl">
            Commander
          </a>

          <div class="mt-5 flex items-center justify-center gap-3 text-gray-300">
            <span class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Paiement sécurisé</span>
          </div>
        </aside>
      </div>
    @endif
  </section>

@endsection

@push('scripts')
<script>
  function formatFCFA(n) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
  }
  function nombreDepuisFCFA(texte) {
    return parseFloat((texte || '').replace(/[^\d]/g, '')) || 0;
  }

  async function appelPanier(url, method, corps) {
    const reponse = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: corps ? JSON.stringify(corps) : undefined,
    });
    return reponse.json();
  }

  function mettreAJourBadgePanier(n) {
    document.querySelectorAll('.badge-panier, #badgePanierMobile').forEach(el => { el.textContent = n; });
  }

  // Réduction courante (code promo + remises membres), tenue à jour par le serveur.
  let reductionCourante = {{ $promo['reduction_totale'] }};

  function majReduction(montant) {
    reductionCourante = Math.max(0, montant || 0);
    const ligne = document.getElementById('ligneReduction');
    const affiche = document.getElementById('reductionAffiche');
    if (reductionCourante > 0) {
      ligne.classList.remove('hidden');
      affiche.textContent = '− ' + formatFCFA(reductionCourante);
    } else {
      ligne.classList.add('hidden');
    }
  }

  function recalculerLivraison() {
    const sousTotal = nombreDepuisFCFA(document.getElementById('sousTotalAffiche').textContent);
    const select = document.getElementById('selectLivraison');
    const cout = select ? parseFloat(select.selectedOptions[0].dataset.cout || 0) : 0;
    document.getElementById('totalAffiche').textContent = formatFCFA(Math.max(0, sousTotal - reductionCourante) + cout);
  }

  async function changerQuantiteLigne(btn, delta) {
    const ligne = btn.closest('.ligne-panier');
    const span = ligne.querySelector('.qte');
    const nouvelleQuantite = Math.max(1, parseInt(span.textContent, 10) + delta);

    const data = await appelPanier(`/panier/${ligne.dataset.slug}`, 'PATCH', { quantite: nouvelleQuantite });
    if (!data.succes) return;

    if (data.estVide) { window.location.reload(); return; }

    span.textContent = data.quantite;
    ligne.querySelector('.ligne-total').textContent = formatFCFA(data.ligneTotal);
    document.getElementById('sousTotalAffiche').textContent = formatFCFA(data.sousTotal);
    majReduction(data.reduction);
    recalculerLivraison();
    mettreAJourBadgePanier(data.nbPanier);
  }

  async function supprimerLigne(btn) {
    const ligne = btn.closest('.ligne-panier');
    const data = await appelPanier(`/panier/${ligne.dataset.slug}`, 'DELETE');
    if (!data.succes) return;

    ligne.style.transition = 'opacity .3s ease, transform .3s ease';
    ligne.style.opacity = '0';
    ligne.style.transform = 'translateX(20px)';
    setTimeout(() => {
      if (data.estVide) { window.location.reload(); return; }
      ligne.remove();
      document.getElementById('sousTotalAffiche').textContent = formatFCFA(data.sousTotal);
      majReduction(data.reduction);
      recalculerLivraison();
      mettreAJourBadgePanier(data.nbPanier);
    }, 300);
  }
</script>
@endpush
