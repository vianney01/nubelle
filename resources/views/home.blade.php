@extends('layouts.app')

@section('title', 'NUBELLE Cosmetics — Une peau naturellement neuve')

@section('content')

  {{-- ============================== HERO ============================== --}}
  <section class="relative w-full overflow-hidden">
    <img src="{{ $accueil->hero_image_url ?? asset('images/accueil.jpg') }}" alt="Nubelle Cosmetics"
         class="w-full max-h-[72vh] object-cover object-center scale-105 transition-transform duration-[3000ms] ease-out">
    <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-black/10 to-transparent"></div>
    <div class="absolute inset-x-0 bottom-0 p-6 sm:p-12 text-center">
      <p class="text-white/80 tracking-[0.3em] text-xs sm:text-sm uppercase mb-3">{{ $accueil->hero_sous_titre }}</p>
      <h1 class="font-serif text-white text-3xl sm:text-5xl font-bold drop-shadow-lg">{{ $accueil->hero_titre }}</h1>
      <a href="{{ $accueil->hero_bouton_lien }}"
         class="inline-block mt-6 rounded-full bg-gradient-to-r from-tangerine to-ember px-8 py-3 text-white font-semibold shadow-lg shadow-ember/30 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-ember/40">
        {{ $accueil->hero_bouton_texte }}
      </a>
    </div>
  </section>

  {{-- ============================ CATÉGORIES ========================== --}}
  <section class="max-w-6xl mx-auto px-5 mt-12">
    <p class="text-sm text-gray-400 mb-5">accueil / nos catégories</p>
    @if (count($categories))
      <div class="flex justify-start sm:justify-center gap-6 overflow-x-auto pb-3 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
        @foreach ($categories as $c)
          <a href="{{ url('/categorie/'.$c['slug']) }}" class="group shrink-0 w-20 text-center">
            <div class="relative w-20 h-20 rounded-2xl overflow-hidden ring-2 ring-transparent shadow-md transition-all duration-300 group-hover:ring-tangerine group-hover:-translate-y-1 group-hover:shadow-xl">
              <img src="{{ $c['image_url'] ?? asset('images/logo.png') }}" alt="{{ $c['nom'] }}"
                   class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
            </div>
            <span class="mt-2 block text-xs font-semibold text-tangerine group-hover:text-ember transition-colors">{{ $c['nom'] }}</span>
          </a>
        @endforeach
      </div>
    @else
      <p class="text-center text-sm text-gray-400 py-6">Aucune catégorie disponible pour le moment.</p>
    @endif
  </section>

  {{-- ============================ NOUVEAUTÉS =========================== --}}
  @if (count($nouveautes))
    <section class="max-w-6xl mx-auto px-5 mt-16">
      <x-section-heading eyebrow="Fraîchement arrivé" title="Nouveautés" subtitle="Les dernières formules à découvrir en premier." />
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach ($nouveautes as $p)
          <x-product-card :produit="$p" />
        @endforeach
      </div>
    </section>
  @endif



 

  {{-- ========================= PRODUITS VEDETTES ======================= --}}
  <section class="max-w-6xl mx-auto px-5 mt-16 mb-4">
    <x-section-heading eyebrow="Sélection Nubelle" title="Produits vedettes" subtitle="Une sélection soignée pour sublimer votre beauté naturelle." />
    @if (count($produits))
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach ($produits as $p)
          <x-product-card :produit="$p" />
        @endforeach
      </div>
    @else
      <p class="text-center text-sm text-gray-400 py-10">Aucun produit disponible pour le moment.</p>
    @endif
    <div class="mt-8 text-center">
      <a href="{{ url('/produits') }}"
         class="inline-flex items-center justify-center rounded-full border border-gray-900 px-8 py-3 text-sm font-semibold text-gray-900 transition-all duration-300 hover:bg-gray-900 hover:text-white">
        Voir tout le catalogue
      </a>
    </div>
  </section>

  {{-- ========================= MISE EN AVANT ========================= --}}
  <section class="reveal flex flex-col items-center text-center px-5 py-16 bg-gradient-to-b from-cream/60 to-white">
    <img src="{{ $accueil->pourquoi_image_url ?? asset('images/parfum.png') }}" alt="Mise en avant"
         class="w-full max-w-md rounded-3xl shadow-2xl shadow-black/10 animate-float">
    <a href="{{ $accueil->pourquoi_bouton_lien }}"
       class="mt-8 rounded-full bg-gradient-to-r from-ember to-sienna px-8 py-3 font-semibold text-white shadow-lg shadow-ember/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl">
      {{ $accueil->pourquoi_bouton_texte }}
    </a>
  </section>

  {{-- ===================== POURQUOI CHOISIR NUBELLE ==================== --}}
  <section class="max-w-6xl mx-auto px-5 py-16">
    <x-section-heading :eyebrow="$accueil->pourquoi_eyebrow" :title="$accueil->pourquoi_titre" />
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
      @php
        $atouts = [
          ['titre' => 'Ingrédients naturels', 'texte' => 'Formules pensées avec des extraits botaniques choisis avec soin.', 'icone' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z'],
          ['titre' => 'Livraison rapide', 'texte' => 'Expédition sous 24 à 72h partout en Côte d\'Ivoire.', 'icone' => 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v11.177m0-11.177L12.75 4.5m3.5 3.073L19.5 12h-3'],
          ['titre' => 'Paiement sécurisé', 'texte' => 'Carte bancaire, Mobile Money ou paiement à la livraison.', 'icone' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
          ['titre' => 'Fièrement ivoirien', 'texte' => 'Une marque née à Abidjan, pensée pour toutes les peaux.', 'icone' => 'M11.25 3.75v11.25m0 0-3.75-3.75m3.75 3.75 3.75-3.75M4.5 19.5h15'],
        ];
      @endphp
      @foreach ($atouts as $a)
        <div class="reveal rounded-2xl bg-white p-5 text-center shadow-[0_2px_10px_rgba(0,0,0,0.05)] ring-1 ring-black/5 transition-transform duration-300 hover:-translate-y-1">
          <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-cream text-ember">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="{{ $a['icone'] }}" />
            </svg>
          </span>
          <p class="mt-3 font-semibold text-gray-900">{{ $a['titre'] }}</p>
          <p class="mt-1 text-xs text-gray-500">{{ $a['texte'] }}</p>
        </div>
      @endforeach
    </div>
  </section>

  {{-- ============================ À PROPOS =========================== --}}
  <section id="apropos" class="reveal max-w-6xl mx-auto my-20 px-5 flex flex-wrap items-center gap-10">
    <div class="flex-1 min-w-[280px]">
      <img src="{{ $accueil->apropos_image_url ?? asset('images/createur.jpg') }}" alt="Fondatrice de Nubelle"
           class="w-full rounded-[2rem] object-cover shadow-2xl shadow-black/10 transition-transform duration-500 hover:scale-[1.02]">
    </div>
    <div class="flex-1 min-w-[320px]">
      <p class="text-tangerine font-semibold tracking-wide uppercase text-sm mb-2">{{ $accueil->apropos_sous_titre }}</p>
      <h2 class="font-serif text-3xl sm:text-4xl font-bold text-ember mb-5">{{ $accueil->apropos_titre }}</h2>
      <p class="text-gray-600 leading-8">{{ $accueil->apropos_texte }}</p>
      <a href="{{ $accueil->apropos_bouton_lien }}" class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-ember hover:text-sienna transition-colors">
        {{ $accueil->apropos_bouton_texte }}
      </a>
    </div>
  </section>

  {{-- ========================== AVIS CLIENTS ========================= --}}
  <section id="avis" class="py-16 bg-cream/40">
    <x-section-heading eyebrow="Ils nous font confiance" title="Avis de nos clients" />

    @if (count($avis))
      <div class="relative max-w-lg mx-auto h-48 px-5" id="carouselAvis">
        @foreach ($avis as $a)
          <div class="avis-item absolute inset-x-5 opacity-0 transition-opacity duration-1000">
            <div class="rounded-3xl bg-white p-7 shadow-xl shadow-black/5 ring-1 ring-black/5">
              <div class="text-tangerine text-lg tracking-widest">{{ str_repeat('★', $a['note']).str_repeat('☆', 5 - $a['note']) }}</div>
              <p class="mt-3 font-semibold text-gray-900">{{ $a['auteur'] }}</p>
              <p class="mt-1 text-sm text-gray-500 italic">"{{ $a['texte'] }}"</p>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-10">
        <h3 class="text-center text-lg font-medium text-gray-700 mb-6">Ils partagent leur expérience en photo</h3>
        <div class="flex flex-wrap justify-center gap-4 px-5">
          @foreach ($avis as $a)
            @if ($a['image'])
              <img src="{{ $a['image'] }}" data-text="{{ $a['texte'] }}" onclick="ouvrirPopupAvis(this)"
                   class="h-28 w-28 cursor-pointer rounded-2xl object-cover shadow-lg shadow-black/10 ring-1 ring-black/5 transition-transform duration-300 hover:scale-110 hover:-rotate-2" alt="Avis client">
            @endif
          @endforeach
        </div>
      </div>
    @else
      <p class="text-center text-sm text-gray-400 py-10">Aucun avis disponible pour le moment.</p>
    @endif
  </section>

  <div id="popupAvis" class="popup-avis">
    <div class="popup-avis-contenu">
      <span class="fermer-popup-avis" onclick="fermerPopupAvis()">&times;</span>
      <img id="popupImageAvis" src="" alt="Avis client">
      <div class="texte-avis-client" id="popupTexteAvis"></div>
    </div>
  </div>

  {{-- =========================== NEWSLETTER BAND ======================= --}}
  <section class="reveal mx-4 sm:mx-auto sm:max-w-5xl my-16 rounded-3xl bg-gradient-to-r from-ember to-sienna px-6 sm:px-14 py-12 text-center shadow-xl shadow-ember/20">
    <p class="text-white/80 text-xs sm:text-sm uppercase tracking-[0.25em] mb-2">Restez informée</p>
    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-white mb-3">-10% sur votre première commande</h2>
    <p class="text-white/85 text-sm mb-6 max-w-md mx-auto">Inscrivez-vous à notre newsletter pour recevoir nos nouveautés et offres exclusives en avant-première.</p>
    <form onsubmit="event.preventDefault(); alert('Merci pour votre inscription !');" class="flex flex-wrap justify-center gap-3 max-w-md mx-auto">
      <input type="email" required placeholder="Votre adresse email"
             class="flex-1 min-w-[200px] rounded-full border-0 px-5 py-3 text-sm text-gray-800 shadow-inner focus:outline-none focus:ring-2 focus:ring-white">
      <button type="submit"
              class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-ember transition-transform duration-300 hover:scale-105">
        Je m'inscris
      </button>
    </form>
  </section>

  {{-- ============================ INSTAGRAM FEED ======================= --}}
  <section class="max-w-6xl mx-auto px-5 py-16">
    <x-section-heading eyebrow="@nubellecosmetics" title="Suivez-nous sur Instagram" />
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 sm:gap-3">
      @foreach (['produit.jpeg','Produit2.jpeg','produit3.jpeg','produit4.jpeg','accueil.jpg','createur.jpg'] as $img)
        <a href="#" class="group relative block aspect-square overflow-hidden rounded-xl">
          <img src="{{ asset('images/'.$img) }}" alt="Publication Instagram Nubelle"
               class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
          <span class="absolute inset-0 flex items-center justify-center bg-black/0 transition-colors duration-300 group-hover:bg-black/40">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
              <rect x="3.5" y="3.5" width="17" height="17" rx="5" />
              <circle cx="12" cy="12" r="4" />
              <circle cx="17" cy="7" r="0.8" fill="currentColor" stroke="none" />
            </svg>
          </span>
        </a>
      @endforeach
    </div>
  </section>

  {{-- =============================== FAQ ============================= --}}
  <section id="faq" class="max-w-3xl mx-auto my-20 px-5">
    <x-section-heading title="Questions fréquentes" />
    <div class="divide-y divide-gray-200 rounded-3xl bg-white ring-1 ring-black/5 shadow-sm overflow-hidden">
      @foreach ($faqs as $f)
        <x-faq-item :question="$f['q']" :reponse="$f['r']" />
      @endforeach
    </div>
    <div class="mt-6 text-center">
      <a href="{{ url('/faq') }}" class="text-sm font-semibold text-ember hover:text-sienna transition-colors">Voir toute la FAQ →</a>
    </div>
  </section>

@endsection

@section('popup')
  @if (! empty($popupVisible))
    <div class="popup-promo" id="popupPromo">
      <div class="popup-contenu">
        <span class="fermer-popup" id="fermerPopupPromo">&times;</span>
        <img src="{{ $accueil->popup_image_url ?? asset('images/promo.jpg') }}" alt="Offre Nubelle">
        <div class="texte-promo">
          @if ($accueil->popup_badge)
            <span class="promo-badge">{{ $accueil->popup_badge }}</span>
          @endif
          <h2>{{ $accueil->popup_titre }}</h2>
          <p>{{ $accueil->popup_sous_titre }}</p>

          @if ($promoPopup)
            <div class="promo-code-bloc">
              <p class="promo-reduction">{{ $promoPopup->libelleReduction() }} de réduction</p>
              <span class="promo-code-label">Votre code</span>
              <div class="promo-code-ligne">
                <span class="promo-code" id="popupCodePromo">{{ $promoPopup->code }}</span>
                <button type="button" class="promo-copier" id="popupCopierCode" onclick="copierCodePromo(this)">Copier</button>
              </div>
              <p class="promo-conditions">{{ $promoPopup->conditionsTexte() }}</p>
            </div>
          @endif

          <a href="{{ $accueil->popup_bouton_lien }}" class="bouton-promo">{{ $accueil->popup_bouton_texte }}</a>
        </div>
      </div>
    </div>
  @endif
@endsection

@push('scripts')
<script>
  // Carrousel avis
  (function () {
    const items = document.querySelectorAll('.avis-item');
    let i = 0;
    const show = () => { items.forEach((el, k) => { el.style.opacity = k === i ? '1' : '0'; el.style.zIndex = k === i ? '1' : '0'; }); i = (i + 1) % items.length; };
    if (items.length) { show(); setInterval(show, 4000); }
  })();

  // Popup avis image
  function ouvrirPopupAvis(el) {
    document.getElementById('popupImageAvis').src = el.getAttribute('src');
    document.getElementById('popupTexteAvis').innerText = el.getAttribute('data-text');
    document.getElementById('popupAvis').classList.add('active');
  }
  function fermerPopupAvis() { document.getElementById('popupAvis').classList.remove('active'); }

  // Popup promo (n'existe que si elle est éligible/affichable)
  window.addEventListener('load', () => {
    const popup = document.getElementById('popupPromo');
    if (!popup) return;
    setTimeout(() => popup.classList.add('active'), 800);
    const fermer = document.getElementById('fermerPopupPromo');
    if (fermer) fermer.addEventListener('click', () => popup.classList.remove('active'));
    popup.addEventListener('click', (e) => { if (e.target === popup) popup.classList.remove('active'); });
  });

  // Copie du code promo en un clic + confirmation visuelle
  function copierCodePromo(btn) {
    const code = (document.getElementById('popupCodePromo')?.textContent || '').trim();
    if (!code) return;
    const confirmer = () => {
      const initial = btn.textContent;
      btn.textContent = 'Copié !';
      btn.classList.add('copie');
      setTimeout(() => { btn.textContent = initial; btn.classList.remove('copie'); }, 1800);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(code).then(confirmer).catch(confirmer);
    } else {
      const zone = document.createElement('textarea');
      zone.value = code; document.body.appendChild(zone); zone.select();
      try { document.execCommand('copy'); } catch (e) {}
      document.body.removeChild(zone); confirmer();
    }
  }
</script>
@endpush
