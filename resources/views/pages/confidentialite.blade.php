@extends('layouts.app')

@section('title', 'Politique de confidentialité — NUBELLE Cosmetics')

@section('content')
  <x-page-legale titre="Politique de confidentialité" misAJour="1er janvier 2026">
    <p>Nubelle Cosmetics accorde une attention particulière à la protection de vos données personnelles. Cette page explique quelles informations nous collectons et comment nous les utilisons.</p>

    <h2>Données collectées</h2>
    <p>Lors de la création d'un compte ou d'une commande, nous collectons : nom, prénom, adresse e-mail, adresse postale, numéro de téléphone.</p>

    <h2>Utilisation des données</h2>
    <ul>
      <li>Traitement et suivi de vos commandes</li>
      <li>Communication relative à votre compte et vos achats</li>
      <li>Envoi de notre newsletter, si vous y avez consenti</li>
      <li>Amélioration de nos produits et services</li>
    </ul>

    <h2>Partage des données</h2>
    <p>Vos données ne sont jamais vendues. Elles peuvent être transmises à nos partenaires logistiques uniquement pour assurer la livraison de vos commandes.</p>

    <h2>Vos droits</h2>
    <p>Vous pouvez à tout moment demander l'accès, la rectification ou la suppression de vos données personnelles en nous contactant via notre page Contact.</p>
  </x-page-legale>
@endsection
