@extends('layouts.app')

@section('title', 'FAQ — NUBELLE Cosmetics')

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'FAQ', 'url' => null],
  ]" />

  <section class="max-w-3xl mx-auto px-5 py-10">
    <x-section-heading eyebrow="Besoin d'aide ?" title="Questions fréquentes" subtitle="Toutes les réponses à vos questions, organisées par thème." />

    <div class="space-y-10">
      @foreach ($groupes as $categorie => $questions)
        <div>
          <h2 class="mb-4 font-serif text-xl font-bold text-ember">{{ $categorie }}</h2>
          <div class="divide-y divide-gray-200 rounded-3xl bg-white ring-1 ring-black/5 shadow-sm overflow-hidden">
            @foreach ($questions as $f)
              <x-faq-item :question="$f['q']" :reponse="$f['r']" />
            @endforeach
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-12 rounded-3xl bg-cream/40 p-8 text-center">
      <p class="font-serif text-lg font-bold text-gray-900">Vous ne trouvez pas votre réponse ?</p>
      <a href="{{ url('/contact') }}" class="mt-3 inline-block rounded-full bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-ember">
        Contactez-nous
      </a>
    </div>
  </section>

@endsection
