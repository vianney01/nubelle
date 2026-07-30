@extends('layouts.app')

@section('title', 'Connexion — NUBELLE Cosmetics')

@section('content')

  <section class="max-w-5xl mx-auto px-5 py-12">
    <div class="grid grid-cols-1 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5 lg:grid-cols-2">

      <div class="relative hidden lg:block">
        <img src="{{ asset('images/createur.jpg') }}" alt="Nubelle Cosmetics" class="h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        <div class="absolute bottom-8 left-8 right-8 text-white">
          <p class="font-serif text-2xl font-bold">Votre beauté commence ici.</p>
          <p class="mt-2 text-sm text-white/80">Rejoignez la communauté Nubelle et suivez vos commandes, favoris et offres exclusives.</p>
        </div>
      </div>

      <div class="p-6 sm:p-10">
        {{-- Onglets --}}
        <div class="mb-8 flex gap-2 rounded-full bg-cream/60 p-1">
          <button type="button" onclick="basculerOngletAuth('connexion', this)" class="onglet-auth flex-1 rounded-full bg-gray-900 py-2.5 text-sm font-semibold text-white transition-colors">Connexion</button>
          <button type="button" onclick="basculerOngletAuth('inscription', this)" class="onglet-auth flex-1 rounded-full py-2.5 text-sm font-semibold text-gray-500 transition-colors">Inscription</button>
        </div>

        {{-- Connexion --}}
        <div id="panneau-connexion" class="panneau-auth space-y-4">
          <h1 class="font-serif text-2xl font-bold text-gray-900">Bon retour parmi nous</h1>
          <form onsubmit="event.preventDefault(); alert('Connexion simulée — authentification à venir.');" class="space-y-4">
            <input type="email" required placeholder="Adresse e-mail" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <input type="password" required placeholder="Mot de passe" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <div class="text-right">
              <button type="button" onclick="basculerOngletAuth('oubli')" class="text-xs font-semibold text-ember hover:text-sienna">Mot de passe oublié ?</button>
            </div>
            <button type="submit" class="w-full rounded-full bg-gray-900 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-ember">Se connecter</button>
          </form>
        </div>

        {{-- Inscription --}}
        <div id="panneau-inscription" class="panneau-auth hidden space-y-4">
          <h1 class="font-serif text-2xl font-bold text-gray-900">Créer un compte</h1>
          <form onsubmit="event.preventDefault(); alert('Inscription simulée — authentification à venir.');" class="space-y-4">
            <input type="text" required placeholder="Nom complet" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <input type="email" required placeholder="Adresse e-mail" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <input type="password" required placeholder="Mot de passe" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <input type="password" required placeholder="Confirmer le mot de passe" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <button type="submit" class="w-full rounded-full bg-gray-900 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-ember">Créer mon compte</button>
          </form>
        </div>

        {{-- Mot de passe oublié --}}
        <div id="panneau-oubli" class="panneau-auth hidden space-y-4">
          <button type="button" onclick="basculerOngletAuth('connexion')" class="text-xs font-semibold text-gray-400 hover:text-gray-700">← Retour à la connexion</button>
          <h1 class="font-serif text-2xl font-bold text-gray-900">Mot de passe oublié</h1>
          <p class="text-sm text-gray-500">Indiquez votre e-mail, nous vous enverrons un lien de réinitialisation.</p>
          <form onsubmit="event.preventDefault(); alert('Un e-mail de réinitialisation vous sera envoyé (simulation).');" class="space-y-4">
            <input type="email" required placeholder="Adresse e-mail" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <button type="submit" class="w-full rounded-full bg-gray-900 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-ember">Envoyer le lien</button>
          </form>
        </div>

        <div class="mt-8 flex items-center gap-3">
          <span class="h-px flex-1 bg-gray-100"></span>
          <span class="text-[11px] uppercase tracking-wide text-gray-400">ou</span>
          <span class="h-px flex-1 bg-gray-100"></span>
        </div>

        <a href="{{ url('/checkout') }}" class="mt-5 block rounded-full border border-gray-200 py-3.5 text-center text-sm font-semibold text-gray-700 transition-colors hover:border-ember hover:text-ember">
          Continuer comme invité
        </a>
      </div>
    </div>
  </section>

@endsection

@push('scripts')
<script>
  function basculerOngletAuth(nom, btn) {
    document.querySelectorAll('.panneau-auth').forEach(p => p.classList.add('hidden'));
    document.getElementById('panneau-' + nom).classList.remove('hidden');

    if (nom === 'connexion' || nom === 'inscription') {
      document.querySelectorAll('.onglet-auth').forEach(b => { b.classList.remove('bg-gray-900', 'text-white'); b.classList.add('text-gray-500'); });
      const cible = btn || document.querySelector(`.onglet-auth:nth-child(${nom === 'connexion' ? 1 : 2})`);
      cible.classList.add('bg-gray-900', 'text-white');
      cible.classList.remove('text-gray-500');
    }
  }
</script>
@endpush
