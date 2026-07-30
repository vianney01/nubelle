<?php

namespace App\Http\Controllers;

use App\Support\CatalogueDemo;

class PageController extends Controller
{
    public function apropos()
    {
        return view('pages.apropos');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function faq()
    {
        return view('pages.faq', [
            'groupes' => CatalogueDemo::faqsGroupees(),
        ]);
    }

    public function confidentialite()
    {
        return view('pages.confidentialite');
    }

    public function conditions()
    {
        return view('pages.conditions');
    }

    public function livraison()
    {
        return view('pages.livraison');
    }

    public function retours()
    {
        return view('pages.retours');
    }
}
