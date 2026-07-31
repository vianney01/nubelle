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
        @if (session()->has('url.intended'))
          <div class="mb-6 rounded-2xl bg-cream/70 p-4 text-sm font-medium text-ember ring-1 ring-ember/10">
            Un compte est nécessaire pour finaliser votre achat. Connectez-vous ou créez un compte pour continuer.
          </div>
        @endif

        {{-- Onglets --}}
        <div class="mb-8 flex gap-2 rounded-full bg-cream/60 p-1">
          <button type="button" onclick="basculerOngletAuth('connexion', this)" class="onglet-auth flex-1 rounded-full bg-gray-900 py-2.5 text-sm font-semibold text-white transition-colors">Connexion</button>
          <button type="button" onclick="basculerOngletAuth('inscription', this)" class="onglet-auth flex-1 rounded-full py-2.5 text-sm font-semibold text-gray-500 transition-colors">Inscription</button>
        </div>

        {{-- Connexion --}}
        <div id="panneau-connexion" class="panneau-auth space-y-4">
          <h1 class="font-serif text-2xl font-bold text-gray-900">Bon retour parmi nous</h1>
          <form method="POST" action="{{ route('connexion.authenticate') }}" class="space-y-4">
            @csrf
            <div>
              <input type="email" name="email" value="{{ old('email') }}" required placeholder="Adresse e-mail" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <input type="password" name="password" required placeholder="Mot de passe" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
            <div class="flex items-center justify-between">
              <label class="flex items-center gap-2 text-xs text-gray-500">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-ember focus:ring-ember"> Se souvenir de moi
              </label>
              <button type="button" onclick="basculerOngletAuth('oubli')" class="text-xs font-semibold text-ember hover:text-sienna">Mot de passe oublié ?</button>
            </div>
            <button type="submit" class="w-full rounded-full bg-gray-900 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-ember">Se connecter</button>
          </form>
        </div>

        {{-- Inscription --}}
        <div id="panneau-inscription" class="panneau-auth hidden space-y-4">
          <h1 class="font-serif text-2xl font-bold text-gray-900">Créer un compte</h1>
          <form method="POST" action="{{ route('inscription') }}" class="space-y-4">
            @csrf
            <div>
              <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nom complet" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
              <input type="email" name="email" value="{{ old('email') }}" required placeholder="Adresse e-mail" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
              <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}" required placeholder="Numéro WhatsApp" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              <p class="mt-1 text-xs text-gray-400">Exemple : 0556400246 ou +2250556400246</p>
              @error('whatsapp') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
              <input type="password" name="password" required placeholder="Mot de passe (8 caractères min.)" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
              @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <input type="password" name="password_confirmation" required placeholder="Confirmer le mot de passe" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
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

  // Si l'inscription a échoué (erreurs de validation), ouvrir cet onglet.
  @if ($errors->has('name') || $errors->has('password'))
    document.addEventListener('DOMContentLoaded', () => basculerOngletAuth('inscription',
      document.querySelectorAll('.onglet-auth')[1]));
  @endif
</script>
@endpush
