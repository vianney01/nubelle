@extends('layouts.app')

@section('title', 'À propos — NUBELLE Cosmetics')

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'À propos', 'url' => null],
  ]" />

  {{-- ============================== HERO ============================== --}}
  <section class="relative mx-4 mt-4 overflow-hidden rounded-3xl sm:mx-auto sm:max-w-6xl">
    <img src="{{ asset('images/accueil.jpg') }}" alt="Nubelle Cosmetics" class="h-64 w-full object-cover sm:h-80">
    <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/45 text-center px-6">
      <p class="text-white/80 text-xs uppercase tracking-[0.3em] mb-2">Notre histoire</p>
      <h1 class="font-serif text-3xl sm:text-5xl font-bold text-white">Nubelle Cosmetics</h1>
    </div>
  </section>

  {{-- ============================ HISTOIRE ============================= --}}
  <section class="max-w-6xl mx-auto my-16 px-5 flex flex-wrap items-center gap-10">
    <div class="flex-1 min-w-[280px]">
      <img src="{{ asset('images/createur.jpg') }}" alt="Fondatrice de Nubelle" class="w-full rounded-[2rem] object-cover shadow-2xl shadow-black/10">
    </div>
    <div class="flex-1 min-w-[320px]">
      <p class="text-tangerine font-semibold tracking-wide uppercase text-sm mb-2">Depuis Abidjan</p>
      <h2 class="font-serif text-3xl sm:text-4xl font-bold text-ember mb-5">Une passion devenue une marque</h2>
      <p class="text-gray-600 leading-8">
        Nubelle Cosmetics est née de la passion d'une femme ivoirienne déterminée à sublimer la beauté naturelle
        de toutes les peaux. À travers des produits de qualité, naturels et adaptés, notre fondatrice souhaite
        offrir à chaque femme la confiance et l'éclat qu'elle mérite.
      </p>
      <p class="mt-4 text-gray-600 leading-8">
        Chaque soin est conçu avec amour, précision et un profond respect de la peau — pensé pour révéler
        l'éclat naturel, sans compromis sur la qualité des ingrédients.
      </p>
    </div>
  </section>

  {{-- ============================= VALEURS ============================= --}}
  <section class="max-w-6xl mx-auto px-5 py-16">
    <x-section-heading eyebrow="Ce qui nous anime" title="Nos valeurs" />
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      @php
        $valeurs = [
          ['titre' => 'Naturalité', 'texte' => 'Des formules pensées avec des ingrédients naturels choisis avec soin.'],
          ['titre' => 'Excellence', 'texte' => "Chaque produit est testé et pensé pour offrir un résultat visible et durable."],
          ['titre' => 'Éthique', 'texte' => 'Une production responsable, respectueuse de la peau comme de l\'environnement.'],
          ['titre' => 'Fierté ivoirienne', 'texte' => "Une marque née à Abidjan, fière de ses racines et de son savoir-faire."],
        ];
      @endphp
      @foreach ($valeurs as $v)
        <div class="rounded-2xl bg-white p-6 text-center shadow-sm ring-1 ring-black/5 transition-transform duration-300 hover:-translate-y-1">
          <p class="font-serif text-lg font-bold text-ember">{{ $v['titre'] }}</p>
          <p class="mt-2 text-sm text-gray-500">{{ $v['texte'] }}</p>
        </div>
      @endforeach
    </div>
  </section>

  {{-- =============================== CTA =============================== --}}
  <section class="reveal mx-4 sm:mx-auto sm:max-w-5xl my-16 rounded-3xl bg-gradient-to-r from-ember to-sienna px-6 sm:px-14 py-12 text-center shadow-xl shadow-ember/20">
    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-white mb-3">Découvrez nos soins</h2>
    <p class="text-white/85 text-sm mb-6 max-w-md mx-auto">Explorez notre collection pensée pour révéler l'éclat naturel de chaque peau.</p>
    <a href="{{ url('/produits') }}" class="inline-block rounded-full bg-white px-8 py-3 text-sm font-semibold text-ember transition-transform duration-300 hover:scale-105">
      Voir la boutique
    </a>
  </section>

@endsection
