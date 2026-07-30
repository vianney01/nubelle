@extends('layouts.app')

@section('title', 'Page introuvable — NUBELLE Cosmetics')

@section('content')

  <section class="max-w-2xl mx-auto px-5 py-20 text-center">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 160" class="mx-auto h-44 w-auto">
      <ellipse cx="100" cy="140" rx="70" ry="10" fill="#FFF8F0" />
      <circle cx="100" cy="80" r="64" fill="#FFE282" opacity="0.5" />
      <circle cx="78" cy="72" r="30" fill="none" stroke="#AA4C00" stroke-width="6" />
      <line x1="99" y1="93" x2="122" y2="116" stroke="#AA4C00" stroke-width="6" stroke-linecap="round" />
      <text x="100" y="80" text-anchor="middle" font-family="Georgia, serif" font-size="28" font-weight="700" fill="#A0522D">404</text>
    </svg>

    <p class="mt-8 text-xs font-semibold uppercase tracking-[0.25em] text-tangerine">Oups</p>
    <h1 class="mt-2 font-serif text-3xl sm:text-4xl font-bold text-gray-900">Cette page s'est égarée</h1>
    <p class="mt-4 text-gray-500">La page que vous recherchez n'existe pas ou a été déplacée. Retournez à l'accueil pour continuer votre exploration.</p>

    <div class="mt-8 flex flex-wrap justify-center gap-3">
      <a href="{{ url('/') }}" class="rounded-full bg-gradient-to-r from-tangerine to-ember px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-ember/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl">
        Retour à l'accueil
      </a>
      <a href="{{ url('/produits') }}" class="rounded-full border border-gray-200 px-8 py-3 text-sm font-semibold text-gray-700 transition-colors hover:border-ember hover:text-ember">
        Voir la boutique
      </a>
    </div>
  </section>

@endsection
