<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facture {{ $commande->numero }} — NUBELLE Cosmetics</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', system-ui, sans-serif; color: #1f2937; background: #f3f4f6; padding: 24px; }
    .facture { max-width: 800px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 24px rgba(0,0,0,.06); }
    .entete { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #aa4c00; padding-bottom: 20px; margin-bottom: 24px; }
    .marque { font-size: 26px; font-weight: 800; color: #aa4c00; letter-spacing: .05em; }
    .marque small { display: block; font-size: 11px; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; color: #9ca3af; }
    .doc-titre { text-align: right; }
    .doc-titre h1 { font-size: 22px; color: #111827; }
    .doc-titre p { font-size: 13px; color: #6b7280; margin-top: 4px; }
    .blocs { display: flex; gap: 40px; margin-bottom: 28px; }
    .bloc { flex: 1; }
    .bloc h3 { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin-bottom: 8px; }
    .bloc p { font-size: 14px; line-height: 1.5; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    thead th { background: #fff8f0; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; padding: 10px 12px; }
    thead th.r, tbody td.r { text-align: right; }
    tbody td { padding: 12px; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
    .totaux { margin-left: auto; width: 300px; }
    .totaux .ligne { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
    .totaux .reduction { color: #059669; }
    .totaux .total { border-top: 2px solid #e5e7eb; margin-top: 8px; padding-top: 10px; font-size: 18px; font-weight: 700; color: #aa4c00; }
    .statut { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; background: #fff8f0; color: #aa4c00; }
    .pied { margin-top: 32px; padding-top: 16px; border-top: 1px solid #f3f4f6; font-size: 12px; color: #9ca3af; text-align: center; }
    .barre-actions { max-width: 800px; margin: 0 auto 16px; display: flex; gap: 10px; justify-content: flex-end; }
    .btn { cursor: pointer; border: none; border-radius: 6px; padding: 10px 18px; font-size: 13px; font-weight: 600; text-decoration: none; }
    .btn-print { background: #aa4c00; color: #fff; }
    .btn-retour { background: #e5e7eb; color: #374151; }
    @media print {
      body { background: #fff; padding: 0; }
      .facture { box-shadow: none; border-radius: 0; max-width: none; padding: 0; }
      .barre-actions { display: none; }
    }
  </style>
</head>
<body>

  @php
    $reductionTotale = (float) $commande->reduction_montant;
    $sousTotal = (float) $commande->total_avant_remise;
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ').' FCFA';
  @endphp

  <div class="barre-actions">
    <button class="btn btn-print" onclick="window.print()">Imprimer / Enregistrer en PDF</button>
    <a class="btn btn-retour" href="{{ url()->previous() }}">Retour</a>
  </div>

  <div class="facture">
    <div class="entete">
      <div class="marque">NUBELLE<small>Cosmetics</small></div>
      <div class="doc-titre">
        <h1>Facture</h1>
        <p>N° {{ $commande->numero }}</p>
        <p>{{ $commande->created_at->translatedFormat('d F Y à H:i') }}</p>
        <p><span class="statut">{{ $commande->statutLabel() }}</span></p>
      </div>
    </div>

    <div class="blocs">
      <div class="bloc">
        <h3>Facturé à</h3>
        @if ($commande->client)
          <p>
            <strong>{{ trim($commande->client->prenom.' '.$commande->client->nom) }}</strong><br>
            {{ $commande->client->email }}<br>
            {{ $commande->client->telephone }}
          </p>
        @else
          <p>Client supprimé</p>
        @endif
      </div>
      <div class="bloc">
        <h3>Livraison</h3>
        <p>
          {{ $commande->adresse_livraison ?? '—' }}<br>
          {{ $commande->client->ville ?? '' }}<br>
          Côte d'Ivoire<br>
          <em>{{ $commande->methodeLivraison() }}</em>
        </p>
      </div>
      <div class="bloc">
        <h3>Paiement</h3>
        <p>
          {{ ['carte' => 'Carte bancaire', 'mobile_money' => 'Mobile Money', 'livraison' => 'À la livraison'][$commande->mode_paiement] ?? '—' }}<br>
          Statut : {{ $commande->statutPaiementLabel() }}<br>
          @if ($commande->reference_paiement)
            Réf. {{ $commande->reference_paiement }}
          @endif
        </p>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Produit</th>
          <th>Réf.</th>
          <th class="r">Prix unitaire</th>
          <th class="r">Qté</th>
          <th class="r">Sous-total</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($commande->lignes as $ligne)
          <tr>
            <td>{{ $ligne->produit->nom ?? 'Produit supprimé' }}</td>
            <td>REF-{{ str_pad((string) $ligne->produit_id, 4, '0', STR_PAD_LEFT) }}</td>
            <td class="r">{{ $fmt($ligne->prix_unitaire) }}</td>
            <td class="r">{{ $ligne->quantite }}</td>
            <td class="r">{{ $fmt($ligne->prix_unitaire * $ligne->quantite) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="totaux">
      <div class="ligne"><span>Sous-total</span><span>{{ $fmt($sousTotal) }}</span></div>
      @if ($reductionTotale > 0)
        <div class="ligne reduction">
          <span>Réduction{{ $commande->codePromo ? ' ('.$commande->codePromo->code.')' : '' }}</span>
          <span>− {{ $fmt($reductionTotale) }}</span>
        </div>
      @endif
      <div class="ligne"><span>Frais de livraison</span><span>{{ $commande->frais_livraison > 0 ? $fmt($commande->frais_livraison) : 'Gratuite' }}</span></div>
      <div class="ligne total"><span>Total TTC</span><span>{{ $fmt($commande->total) }}</span></div>
    </div>

    <div class="pied">
      Merci pour votre confiance — NUBELLE Cosmetics · Abidjan, Côte d'Ivoire<br>
      Cette facture a été générée le {{ now()->translatedFormat('d F Y à H:i') }}.
    </div>
  </div>

</body>
</html>
