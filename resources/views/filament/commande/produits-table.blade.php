@php
    /** @var \App\Models\Commande $commande */
    $commande = $getRecord();
    $commande->loadMissing(['lignes.produit.categorie', 'codePromo']);
    $lignes = $commande->lignes;
    $code = $commande->codePromo?->code;
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ').' FCFA';
@endphp

<div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:.85rem;color:inherit;">
        <thead>
            <tr style="text-transform:uppercase;font-size:.68rem;letter-spacing:.05em;opacity:.55;">
                <th style="padding:.55rem .75rem;text-align:left;">Produit</th>
                <th style="padding:.55rem .75rem;text-align:left;">SKU</th>
                <th style="padding:.55rem .75rem;text-align:left;">Catégorie</th>
                <th style="padding:.55rem .75rem;text-align:right;">Prix unit.</th>
                <th style="padding:.55rem .75rem;text-align:center;">Qté</th>
                <th style="padding:.55rem .75rem;text-align:right;">Sous-total</th>
                <th style="padding:.55rem .75rem;text-align:left;">Réduction</th>
                <th style="padding:.55rem .75rem;text-align:left;">Stock</th>
                <th style="padding:.55rem .75rem;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lignes as $ligne)
                @php
                    $produit = $ligne->produit;
                    $sous = (float) $ligne->prix_unitaire * $ligne->quantite;
                    $stock = $produit?->stock ?? 0;
                    $stockCouleur = $stock <= 0 ? '#dc2626' : ($stock < 5 ? '#d97706' : '#059669');
                    $url = $produit
                        ? \App\Filament\Resources\Produits\ProduitResource::getUrl('edit', ['record' => $produit->id])
                        : null;
                @endphp
                <tr style="border-top:1px solid rgba(128,128,128,.2);">
                    <td style="padding:.6rem .75rem;">
                        <div style="display:flex;align-items:center;gap:.6rem;min-width:190px;">
                            <span style="display:inline-flex;height:44px;width:44px;flex:none;align-items:center;justify-content:center;border-radius:.6rem;background:rgba(128,128,128,.12);overflow:hidden;">
                                @if ($produit?->image)
                                    <img src="{{ $produit->image }}" alt="" loading="lazy" style="height:100%;width:100%;object-fit:contain;padding:3px;">
                                @endif
                            </span>
                            <span style="font-weight:600;">{{ $produit?->nom ?? 'Produit supprimé' }}</span>
                        </div>
                    </td>
                    <td style="padding:.6rem .75rem;opacity:.75;white-space:nowrap;">REF-{{ str_pad((string) $ligne->produit_id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td style="padding:.6rem .75rem;opacity:.75;">{{ $produit?->categorie?->nom ?? '—' }}</td>
                    <td style="padding:.6rem .75rem;text-align:right;white-space:nowrap;">{{ $fmt($ligne->prix_unitaire) }}</td>
                    <td style="padding:.6rem .75rem;text-align:center;">{{ $ligne->quantite }}</td>
                    <td style="padding:.6rem .75rem;text-align:right;font-weight:700;white-space:nowrap;">{{ $fmt($sous) }}</td>
                    <td style="padding:.6rem .75rem;">
                        @if ($code)
                            <span style="display:inline-block;padding:.1rem .5rem;border-radius:999px;background:rgba(5,150,105,.14);color:#059669;font-size:.7rem;font-weight:700;">{{ $code }}</span>
                        @else
                            <span style="opacity:.5;">—</span>
                        @endif
                    </td>
                    <td style="padding:.6rem .75rem;">
                        <span style="display:inline-block;padding:.1rem .5rem;border-radius:999px;font-size:.7rem;font-weight:700;color:{{ $stockCouleur }};background:{{ $stockCouleur }}1f;white-space:nowrap;">{{ $stock }} u.</span>
                    </td>
                    <td style="padding:.6rem .75rem;white-space:nowrap;">
                        @if ($url)
                            <a href="{{ $url }}" target="_blank" style="color:#aa4c00;font-weight:600;text-decoration:none;">Voir →</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" style="padding:1.2rem;text-align:center;opacity:.55;">Aucun produit sur cette commande.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
