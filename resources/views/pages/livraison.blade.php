@extends('layouts.app')

@section('title', 'Politique de livraison — NUBELLE Cosmetics')

@section('content')
  <x-page-legale titre="Politique de livraison" misAJour="1er janvier 2026">
    <h2>Zones et délais</h2>
    <ul>
      <li>Abidjan : 24 à 48h</li>
      <li>Intérieur de la Côte d'Ivoire : 48 à 72h</li>
      <li>Afrique &amp; Europe : 5 à 10 jours ouvrés</li>
    </ul>

    <h2>Frais de livraison</h2>
    <ul>
      <li>Abidjan : à partir de 500 FCFA</li>
      <li>Intérieur du pays : à partir de 1 500 FCFA</li>
      <li>International : à partir de 5 000 FCFA</li>
    </ul>

    <h2>Suivi de commande</h2>
    <p>Un numéro de suivi vous est communiqué par e-mail dès l'expédition de votre commande, consultable depuis votre espace « Mon compte ».</p>

    <h2>Retard de livraison</h2>
    <p>En cas de retard important, notre service client reste disponible pour vous accompagner et vous informer de l'état de votre colis.</p>
  </x-page-legale>
@endsection
