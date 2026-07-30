@php
    /** @var \App\Models\Commande $commande */
    $commande = $getRecord();
    $commande->loadMissing(['historiques.user']);
    $notes = $commande->historiques->where('type', 'note');
@endphp

@if ($notes->isEmpty())
    <p style="opacity:.55;font-size:.85rem;">Aucune note interne. Utilisez « Ajouter une note » pour en créer une.</p>
@else
    <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:.55rem;">
        @foreach ($notes as $n)
            <li style="padding:.6rem .7rem;border-radius:.5rem;background:rgba(128,128,128,.09);font-size:.82rem;">
                <p style="margin:0;">{{ $n->commentaire }}</p>
                <p style="margin:.2rem 0 0;font-size:.7rem;opacity:.55;">{{ $n->created_at->format('d/m/Y H:i') }} · {{ $n->user?->name ?? 'Système' }}</p>
            </li>
        @endforeach
    </ul>
@endif
