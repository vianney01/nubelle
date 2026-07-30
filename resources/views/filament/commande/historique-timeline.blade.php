@php
    /** @var \App\Models\Commande $commande */
    $commande = $getRecord();
    $commande->loadMissing(['historiques.user']);
    $evenements = $commande->historiques; // triés du plus ancien au plus récent
    $libelles = \App\Models\HistoriqueCommande::LIBELLES;
@endphp

@if ($evenements->isEmpty())
    <p style="opacity:.55;font-size:.85rem;">Aucun événement pour le moment.</p>
@else
    <ol style="list-style:none;margin:0;padding:0;">
        @foreach ($evenements as $ev)
            <li style="position:relative;padding:0 0 1.1rem 1.6rem;">
                @unless ($loop->last)
                    <span style="position:absolute;left:.32rem;top:1.05rem;bottom:-.15rem;width:2px;background:rgba(128,128,128,.25);"></span>
                @endunless
                <span style="position:absolute;left:0;top:.2rem;height:.75rem;width:.75rem;border-radius:999px;background:#aa4c00;box-shadow:0 0 0 3px rgba(170,76,0,.15);"></span>
                <p style="margin:0;font-weight:600;font-size:.85rem;">{{ $libelles[$ev->type] ?? $ev->type }}</p>
                <p style="margin:.1rem 0 0;font-size:.72rem;opacity:.6;">{{ $ev->created_at->format('d/m/Y à H:i') }} · {{ $ev->user?->name ?? 'Client / système' }}</p>
                @if ($ev->commentaire)
                    <p style="margin:.25rem 0 0;font-size:.8rem;opacity:.85;">{{ $ev->commentaire }}</p>
                @endif
            </li>
        @endforeach
    </ol>
@endif
