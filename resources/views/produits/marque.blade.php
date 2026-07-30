@extends('layouts.app')

@section('title', $marque['nom'].' — NUBELLE Cosmetics')

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'Marque', 'url' => null],
  ]" />

  {{-- ============================== HERO MARQUE ========================= --}}
  <section class="relative mx-4 mt-4 overflow-hidden rounded-3xl sm:mx-auto sm:max-w-6xl">
    <img src="{{ asset('images/'.$marque['image_bandeau']) }}" alt="{{ $marque['nom'] }}"
         class="h-64 w-full object-cover sm:h-80">
    <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/45 text-center px-6">
      <img src="{{ asset('images/'.$marque['logo']) }}" alt="{{ $marque['nom'] }}" class="h-10 sm:h-12 object-contain mb-4 brightness-0 invert">
      <p class="max-w-xl text-sm sm:text-base text-white/90 leading-relaxed">{{ $marque['description'] }}</p>
    </div>
  </section>

  <section class="max-w-6xl mx-auto px-5 py-14">
    <x-section-heading eyebrow="La collection" title="Tous les produits {{ $marque['nom'] }}" />
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 sm:gap-6">
      @foreach ($produits as $p)
        <x-product-card :produit="$p" />
      @endforeach
    </div>
  </section>

@endsection
