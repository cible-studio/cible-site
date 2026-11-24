<?php

namespace App\Http\Controllers;

/**
 * Maquette de refonte — /marquette (2026-11-24).
 *
 * Proposition de nouvelle mise en page servie à côté du site actuel, sans
 * le modifier : les textes viennent des mêmes sources (Contenu::get, donc
 * l'espace admin), seule la présentation change. Pages non indexées et
 * absentes du menu du site.
 *
 * Contrôleur séparé à dessein : supprimer la maquette = supprimer ce
 * fichier, ses routes, resources/views/marquette et public/marquette.
 */
class MarquetteController extends Controller
{
    public function accueil()  { return $this->page('accueil'); }
    public function qui()      { return $this->page('qui'); }
    public function services() { return $this->page('services'); }
    public function reseau()   { return $this->page('reseau'); }
    public function contact()  { return $this->page('contact'); }

    private function page(string $page)
    {
        return view("marquette.$page", ['page' => $page]);
    }
}
