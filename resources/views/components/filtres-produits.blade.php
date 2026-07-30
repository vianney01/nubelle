@props([
  'categories' => [],
  'categorieActive' => null,
  'tri' => 'pertinence',
  'q' => '',
  'action' => null,
  'masquerCategories' => false,
])

<form method="GET" action="{{ $action ?? url('/produits') }}" class="space-y-6">
  {{-- Recherche --}}
  <div>
    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Rechercher</label>
    <div class="relative">
      <input type="text" name="q" value="{{ $q }}" placeholder="Un produit, une routine…"
             class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-800 shadow-sm transition-colors focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
      <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
      </svg>
    </div>
  </div>

  @unless ($masquerCategories)
    <div>
      <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Catégories</p>
      <div class="flex flex-wrap gap-2">
        <label class="cursor-pointer">
          <input type="radio" name="categorie" value="" class="peer sr-only" {{ $categorieActive ? '' : 'checked' }} onchange="this.form.submit()">
          <span class="inline-block rounded-full border border-gray-200 px-3.5 py-1.5 text-xs font-medium text-gray-600 transition-colors peer-checked:border-ember peer-checked:bg-ember peer-checked:text-white">Toutes</span>
        </label>
        @foreach ($categories as $c)
          <label class="cursor-pointer">
            <input type="radio" name="categorie" value="{{ $c['slug'] }}" class="peer sr-only" {{ $categorieActive === $c['slug'] ? 'checked' : '' }} onchange="this.form.submit()">
            <span class="inline-block rounded-full border border-gray-200 px-3.5 py-1.5 text-xs font-medium text-gray-600 transition-colors peer-checked:border-ember peer-checked:bg-ember peer-checked:text-white">{{ $c['nom'] }}</span>
          </label>
        @endforeach
      </div>
    </div>
  @endunless

  <div>
    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Prix (FCFA)</p>
    <div class="flex items-center gap-2">
      <input type="number" name="prix_min" value="{{ request('prix_min') }}" placeholder="Min" min="0"
             class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
      <span class="text-gray-300">–</span>
      <input type="number" name="prix_max" value="{{ request('prix_max') }}" placeholder="Max" min="0"
             class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
    </div>
  </div>

  <label class="flex cursor-pointer items-center gap-2">
    <input type="checkbox" name="disponibilite" value="en_stock" {{ request('disponibilite') === 'en_stock' ? 'checked' : '' }}
           class="h-4 w-4 rounded border-gray-300 text-ember focus:ring-ember">
    <span class="text-sm text-gray-700">En stock uniquement</span>
  </label>

  <div>
    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Trier par</label>
    <select name="tri" onchange="this.form.submit()"
            class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
      <option value="pertinence" {{ $tri === 'pertinence' ? 'selected' : '' }}>Pertinence</option>
      <option value="nouveaute" {{ $tri === 'nouveaute' ? 'selected' : '' }}>Nouveautés</option>
      <option value="prix_asc" {{ $tri === 'prix_asc' ? 'selected' : '' }}>Prix croissant</option>
      <option value="prix_desc" {{ $tri === 'prix_desc' ? 'selected' : '' }}>Prix décroissant</option>
      <option value="nom" {{ $tri === 'nom' ? 'selected' : '' }}>Nom (A-Z)</option>
    </select>
  </div>

  <div class="flex gap-2">
    <button type="submit" class="flex-1 rounded-full bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-ember">
      Filtrer
    </button>
    <a href="{{ $action ?? url('/produits') }}" class="rounded-full border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 transition-colors hover:bg-cream">
      Réinitialiser
    </a>
  </div>
</form>
