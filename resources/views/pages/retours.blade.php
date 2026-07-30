@extends('layouts.app')

@section('title', 'Politique de retour — NUBELLE Cosmetics')

@section('content')
  <x-page-legale titre="Politique de retour" misAJour="1er janvier 2026">
    <h2>Délai de retour</h2>
    <p>Vous disposez de 14 jours après réception de votre commande pour effectuer un retour, produit non ouvert et dans son emballage d'origine.</p>

    <h2>Comment retourner un produit</h2>
    <ul>
      <li>Contactez notre service client via la page Contact</li>
      <li>Précisez le numéro de commande et le produit concerné</li>
      <li>Renvoyez le colis à l'adresse qui vous sera communiquée</li>
    </ul>

    <h2>Remboursement</h2>
    <p>Le remboursement est effectué sous 5 à 7 jours ouvrés après réception et vérification du produit retourné, sur le même moyen de paiement utilisé lors de l'achat.</p>

    <h2>Produits non retournables</h2>
    <p>Pour des raisons d'hygiène, les produits ouverts ou descellés ne peuvent pas être repris, sauf en cas de défaut avéré.</p>
  </x-page-legale>
@endsection
