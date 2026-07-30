@props(['question', 'reponse'])

<div class="group">
  <button onclick="toggleFAQ(this)" class="faq-btn flex w-full items-center justify-between gap-4 px-6 py-5 text-left font-semibold text-gray-900 transition-colors hover:bg-cream/50">
    <span>{{ $question }}</span>
    <span class="icon grid h-7 w-7 shrink-0 place-items-center rounded-full bg-cream text-ember text-xl transition-transform duration-300">+</span>
  </button>
  <div class="faq-answer max-h-0 overflow-hidden px-6 text-gray-500 leading-relaxed transition-all duration-300">
    <p class="pb-5">{{ $reponse }}</p>
  </div>
</div>
