@php
    /** @var \App\Models\Commande $commande */
    $commande = $getRecord();

    $statutLabel = \App\Models\Commande::STATUTS_LABELS[$commande->statut] ?? $commande->statut;
    $paieLabel = \App\Models\Commande::STATUTS_PAIEMENT_LABELS[$commande->statut_paiement] ?? $commande->statut_paiement;

    $modes = ['carte' => 'Carte bancaire', 'mobile_money' => 'Mobile Money', 'livraison' => 'À la livraison'];

    $statutCouleurs = ['en_attente' => '#6b7280', 'en_preparation' => '#2563eb', 'expediee' => '#d97706', 'livree' => '#059669', 'annulee' => '#dc2626'];
    $paieCouleurs = ['en_attente' => '#d97706', 'paye' => '#059669', 'rembourse' => '#dc2626'];
    $sc = $statutCouleurs[$commande->statut] ?? '#6b7280';
    $pc = $paieCouleurs[$commande->statut_paiement] ?? '#6b7280';

    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ').' FCFA';
    $meta = 'font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;opacity:.55;margin:0;';
    $badge = fn ($couleur) => "display:inline-block;padding:.15rem .6rem;border-radius:999px;font-size:.7rem;font-weight:700;color:{$couleur};background:{$couleur}1f;";
@endphp

<div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1.5rem;">

    {{-- Identité + statuts --}}
    <div style="min-width:220px;">
        <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;">
            <span style="font-size:1.4rem;font-weight:800;letter-spacing:-.01em;">{{ $commande->numero }}</span>
            <span style="{{ $badge($sc) }}">{{ $statutLabel }}</span>
            <span style="{{ $badge($pc) }}">Paiement&nbsp;: {{ $paieLabel }}</span>
        </div>
        <p style="margin:.45rem 0 0;font-size:.82rem;opacity:.6;">Passée le {{ $commande->created_at->translatedFormat('d F Y à H:i') }}</p>
    </div>

    {{-- Faits clés + total --}}
    <div style="display:flex;align-items:center;gap:1.75rem;flex-wrap:wrap;">
        <div style="min-width:110px;">
            <p style="{{ $meta }}">Paiement</p>
            <p style="margin:.15rem 0 0;font-weight:600;font-size:.9rem;">{{ $modes[$commande->mode_paiement] ?? '—' }}</p>
        </div>
        <div style="min-width:130px;">
            <p style="{{ $meta }}">Livraison</p>
            <p style="margin:.15rem 0 0;font-weight:600;font-size:.9rem;">{{ $commande->methodeLivraison() }}</p>
        </div>
        <div style="text-align:right;padding-left:1.75rem;border-left:1px solid rgba(128,128,128,.25);min-width:150px;">
            <p style="{{ $meta }}">Total</p>
            <p style="margin:.1rem 0 0;font-size:1.6rem;font-weight:800;color:#aa4c00;line-height:1.1;">{{ $fmt($commande->total) }}</p>
        </div>
    </div>
</div>
