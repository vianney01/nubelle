@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ').' FCFA';
    $modes = ['carte' => 'Carte bancaire', 'mobile_money' => 'Mobile Money', 'livraison' => 'Paiement à la livraison'];
    $lienAdmin = url('/admin/commandes/'.$commande->id);
@endphp
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Segoe UI,Helvetica,Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;padding:24px;">
        <div style="background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.06);">

            <div style="background:#aa4c00;padding:22px 28px;">
                <p style="margin:0;color:#fff;font-size:20px;font-weight:800;letter-spacing:.04em;">NUBELLE<span style="font-weight:400;opacity:.85;"> Cosmetics</span></p>
                <p style="margin:6px 0 0;color:#ffe;opacity:.9;font-size:13px;">Nouvelle commande reçue</p>
            </div>

            <div style="padding:28px;">
                <p style="margin:0 0 4px;font-size:15px;">Une nouvelle commande vient d'être passée sur la boutique.</p>
                <p style="margin:0 0 20px;font-size:22px;font-weight:800;color:#aa4c00;">{{ $commande->numero }}</p>

                <table style="width:100%;border-collapse:collapse;font-size:14px;margin-bottom:22px;">
                    <tr>
                        <td style="padding:4px 0;color:#6b7280;">Date</td>
                        <td style="padding:4px 0;text-align:right;font-weight:600;">{{ $commande->created_at->translatedFormat('d F Y à H:i') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6b7280;">Client</td>
                        <td style="padding:4px 0;text-align:right;font-weight:600;">{{ $commande->client ? trim($commande->client->prenom.' '.$commande->client->nom) : '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6b7280;">Email</td>
                        <td style="padding:4px 0;text-align:right;">{{ $commande->client->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6b7280;">Téléphone</td>
                        <td style="padding:4px 0;text-align:right;">{{ $commande->client->telephone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6b7280;">Paiement</td>
                        <td style="padding:4px 0;text-align:right;">{{ $modes[$commande->mode_paiement] ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:#6b7280;">Livraison</td>
                        <td style="padding:4px 0;text-align:right;">{{ $commande->adresse_livraison ?? '—' }}</td>
                    </tr>
                </table>

                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    <thead>
                        <tr style="text-align:left;color:#6b7280;font-size:12px;text-transform:uppercase;">
                            <th style="padding:8px 6px;border-bottom:2px solid #eee;">Produit</th>
                            <th style="padding:8px 6px;border-bottom:2px solid #eee;text-align:center;">Qté</th>
                            <th style="padding:8px 6px;border-bottom:2px solid #eee;text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commande->lignes as $ligne)
                            <tr>
                                <td style="padding:8px 6px;border-bottom:1px solid #f3f4f6;">{{ $ligne->produit->nom ?? 'Produit supprimé' }}</td>
                                <td style="padding:8px 6px;border-bottom:1px solid #f3f4f6;text-align:center;">{{ $ligne->quantite }}</td>
                                <td style="padding:8px 6px;border-bottom:1px solid #f3f4f6;text-align:right;">{{ $fmt($ligne->prix_unitaire * $ligne->quantite) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <table style="width:100%;border-collapse:collapse;font-size:14px;margin-top:14px;">
                    <tr>
                        <td style="padding:3px 6px;color:#6b7280;">Sous-total</td>
                        <td style="padding:3px 6px;text-align:right;">{{ $fmt($commande->total_avant_remise) }}</td>
                    </tr>
                    @if ($commande->reduction_montant > 0)
                        <tr>
                            <td style="padding:3px 6px;color:#059669;">Réduction{{ $commande->codePromo ? ' ('.$commande->codePromo->code.')' : '' }}</td>
                            <td style="padding:3px 6px;text-align:right;color:#059669;">− {{ $fmt($commande->reduction_montant) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding:3px 6px;color:#6b7280;">Frais de livraison</td>
                        <td style="padding:3px 6px;text-align:right;">{{ $commande->frais_livraison > 0 ? $fmt($commande->frais_livraison) : 'Gratuite' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 6px 0;font-weight:800;font-size:16px;">Total</td>
                        <td style="padding:10px 6px 0;text-align:right;font-weight:800;font-size:16px;color:#aa4c00;">{{ $fmt($commande->total) }}</td>
                    </tr>
                </table>

                <div style="text-align:center;margin-top:28px;">
                    <a href="{{ $lienAdmin }}" style="display:inline-block;background:#111827;color:#fff;text-decoration:none;padding:12px 28px;border-radius:9999px;font-weight:600;font-size:14px;">Voir la commande dans l'admin</a>
                </div>
            </div>

            <div style="padding:16px 28px;border-top:1px solid #f3f4f6;color:#9ca3af;font-size:12px;text-align:center;">
                NUBELLE Cosmetics · Notification automatique de commande
            </div>
        </div>
    </div>
</body>
</html>
