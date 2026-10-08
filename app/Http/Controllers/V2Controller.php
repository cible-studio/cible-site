<?php

namespace App\Http\Controllers;

use App\Support\Contenu;

/**
 * Maquette de refonte — servie sous /v2, branche `refonte-v2`.
 *
 * Volontairement séparée de CibleController : la V1 reste intacte et en
 * production pendant toute la refonte. Aucun fichier de la V1 n'est
 * modifié, donc aucun risque sur le site vivant.
 *
 * Les pages lisent le MÊME contenu éditable que la V1 (config/admin-schema
 * et config/contenu via App\Support\Contenu). Deux conséquences utiles :
 * la maquette montre les vrais textes de CIBLE et non du remplissage, et
 * l'espace d'administration construit en août pilote déjà la refonte —
 * il n'y aura rien à recâbler au basculement.
 *
 * Les routes répondent 404 si APP_ENV est en production ET que
 * CIBLE_V2_OUVERT n'est pas activé : une maquette n'a pas à être
 * atteignable par un visiteur du site public tant qu'elle n'est pas validée.
 */
class V2Controller extends Controller
{
    /**
     * Garde-fou : en production, la maquette n'existe pas tant qu'on ne
     * l'ouvre pas explicitement. 404 et non 403 — un visiteur n'a aucune
     * raison d'apprendre qu'il y a un chantier derrière cette adresse.
     */
    public function __construct()
    {
        abort_if(
            app()->isProduction() && !filter_var(env('CIBLE_V2_OUVERT', false), FILTER_VALIDATE_BOOL),
            404
        );
    }

    public function accueil()
    {
        return view('v2.accueil', $this->commun());
    }

    public function expertises()
    {
        return view('v2.expertises', $this->commun());
    }

    public function reseau()
    {
        return view('v2.reseau', $this->commun());
    }

    public function travaux()
    {
        return view('v2.travaux', $this->commun());
    }

    public function contact()
    {
        return view('v2.contact', $this->commun());
    }

    /**
     * Données partagées : les réalisations viennent de la source unique
     * (Contenu), jamais dupliquées dans une vue.
     */
    private function commun(): array
    {
        return [
            'realisations' => Contenu::section('realisations'),
            'options'      => CibleController::formOptions(),
        ];
    }
}
