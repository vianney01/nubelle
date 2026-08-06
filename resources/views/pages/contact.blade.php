@extends('layouts.app')

@section('title', 'Contact — NUBELLE Cosmetics')

@section('content')

  <x-breadcrumb :items="[
    ['label' => 'Accueil', 'url' => url('/')],
    ['label' => 'Contact', 'url' => null],
  ]" />

  <section class="max-w-6xl mx-auto px-5 py-8">
    <x-section-heading align="left" eyebrow="Nous contacter" title="Une question ? Nous sommes là." subtitle="Notre équipe vous répond sous 24h ouvrées." class="max-w-xl" />

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_360px]">

      {{-- ============================= FORMULAIRE ============================ --}}
      <form onsubmit="event.preventDefault(); document.getElementById('confirmationContact').classList.remove('hidden'); this.reset();"
            class="space-y-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <input type="text" required placeholder="Nom complet" class="rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
          <input type="email" required placeholder="Adresse e-mail" class="rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
        </div>
        <input type="text" required placeholder="Sujet" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember">
        <textarea required rows="5" placeholder="Votre message" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-ember focus:outline-none focus:ring-1 focus:ring-ember"></textarea>
        <button type="submit" class="rounded-full bg-gray-900 px-8 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-ember">
          Envoyer le message
        </button>
        <p id="confirmationContact" class="hidden text-sm font-medium text-green-600">Merci, votre message a bien été envoyé — nous revenons vers vous rapidement.</p>
      </form>

      {{-- ============================= COORDONNÉES ============================ --}}
      <aside class="space-y-4">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
          <a href="tel:{{ $whatsappTel ?? '' }}" class="flex items-center gap-3 rounded-xl px-2 py-3 transition-colors hover:bg-cream/50">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-cream text-ember">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.125 1.125 0 0 0-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97a1.125 1.125 0 0 0 .417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
            </span>
            <span>
              <span class="block text-xs text-gray-400">Téléphone</span>
              <span class="font-semibold text-gray-800">{{ $whatsappAffichage ?? '' }}</span>
            </span>
          </a>
          <a href="{{ !empty($whatsappLien) ? 'https://wa.me/'.$whatsappLien : '#' }}" @if(!empty($whatsappLien)) target="_blank" rel="noopener" @endif class="flex items-center gap-3 rounded-xl px-2 py-3 transition-colors hover:bg-cream/50">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-cream text-ember">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 12a8.25 8.25 0 1 1-12.6-7.02L4.5 4.2l1.06 3.4A8.2 8.2 0 0 1 12 3.75a8.25 8.25 0 0 1 8.25 8.25Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9.5c.3 2.6 2.1 4.4 4.7 4.7.8.1 1.3-.7 1-1.3l-.6-1-1.6.4a4 4 0 0 1-2-2l.4-1.6-1-.6c-.6-.3-1.4.2-1.3 1Z" />
              </svg>
            </span>
            <span>
              <span class="block text-xs text-gray-400">WhatsApp</span>
              <span class="font-semibold text-gray-800">Discuter avec nous</span>
            </span>
          </a>
          <a href="mailto:contact@nubelle-cosmetics.com" class="flex items-center gap-3 rounded-xl px-2 py-3 transition-colors hover:bg-cream/50">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-cream text-ember">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.007 1.872l-7.5 5.007a2.25 2.25 0 0 1-2.486 0L3.257 8.865A2.25 2.25 0 0 1 2.25 6.993V6.75" /></svg>
            </span>
            <span>
              <span class="block text-xs text-gray-400">E-mail</span>
              <span class="font-semibold text-gray-800">contact@nubelle-cosmetics.com</span>
            </span>
          </a>
        </div>

        <div class="flex items-center justify-center gap-3 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">
          <a href="#" aria-label="Instagram" class="drawer-social"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><rect x="3.5" y="3.5" width="17" height="17" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17" cy="7" r="0.8" fill="currentColor" stroke="none" /></svg></a>
          <a href="#" aria-label="Facebook" class="drawer-social"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.5 21v-7.5h2.5l.5-3.25h-3V8.1c0-.94.26-1.6 1.62-1.6H17.5V3.6C17.2 3.56 16.2 3.47 15 3.47c-2.4 0-4 1.46-4 4.15v2.63H8.5v3.25H11V21" /></svg></a>
          <a href="#" aria-label="TikTok" class="drawer-social"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3.5c.45 2.1 1.85 3.4 4 3.55V10c-1.45-.05-2.8-.5-4-1.35v6.1a5.15 5.15 0 1 1-4.45-5.1v3.05a2.1 2.1 0 1 0 1.45 2v-11.2Z" /></svg></a>
        </div>

        <div class="overflow-hidden rounded-3xl shadow-sm ring-1 ring-black/5">
          <iframe
            src="https://www.google.com/maps?q=Abidjan,+C%C3%B4te+d%27Ivoire&output=embed"
            class="h-56 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            title="Localisation Nubelle Cosmetics"></iframe>
        </div>
      </aside>
    </div>
  </section>

@endsection
