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
     * Titres courts des réalisations — PROPOSITION À CORRIGER.
     *
     * Les titres de la V1 sont descriptifs et interchangeables entre les
     * six clients (« Faire vivre la marque au plus près de ses publics »,
     * « Traduire une vision institutionnelle en récit de marque »). Une
     * galerie de travaux demande des titres qui donnent envie d'ouvrir,
     * comme le fait la référence citée par le client : « FOR PAPA »,
     * « INCLUSIVE BY DESIGN ».
     *
     * ⚠ Ceux-ci sont dérivés STRICTEMENT des textes déjà présents dans
     * config/contenu.php : aucune ville, aucun chiffre, aucune durée, aucun
     * dispositif n'a été ajouté, faute de connaître le terrain. Ils sont donc
     * justes mais encore abstraits. Un titre nourri d'un fait réel les
     * battra tous — c'est le sens de la demande faite au client.
     *
     * Ils vivent ici, et non dans config/contenu.php, pour deux raisons :
     * la V1 est en production et son champ `titre` ne doit pas changer, et
     * un emplacement unique et commenté est plus simple à corriger. Au
     * basculement, ils rejoindront le schéma pour devenir éditables depuis
     * l'admin.
     */
    public const TITRES_COURTS = [
        'orange'    => 'Aller chercher les gens',
        'cofina'    => "Ce qu'une institution a à dire",
        'snedai'    => 'Même voix, partout',
        'sgs-sicta' => 'Tenir la parole en ligne',
        'ifg'       => "Un stand qu'on n'évite pas",
        'sigfu'     => 'Donner un corps à une présence',
    ];

    /**
     * Visuels de TEST pour la revue — placeholders Unsplash.
     *
     * Autorisés explicitement par le client pour la maquette. Ils tiennent
     * la place des prises de vue que la charte demande et que CIBLE n'a pas
     * encore : « des personnages humains qui sont en joie », des images de
     * liberté, le perroquet. Les visuels d'origine du dépôt sont des
     * créations publicitaires de CIBLE et des constats de pose — utiles,
     * mais pas des photographies de campagne.
     *
     * ⚠ À REMPLACER avant toute mise en ligne publique. On surcharge ici la
     * copie locale du tableau, sans jamais toucher config/contenu.php : la
     * V1 en production continue d'afficher ses propres visuels.
     */
    public const VISUELS_TEST = [
        'orange'    => 'refonte/test/foule-festive.webp',   // activation terrain
        'cofina'    => 'refonte/test/studio-lumiere.webp',  // production audiovisuelle
        'snedai'    => 'refonte/test/rue-afrique.webp',     // présence multi-supports
        'sgs-sicta' => 'refonte/test/mobile.webp',          // digital et réseaux
        'ifg'       => 'refonte/test/stand.webp',           // stand expérientiel
        'sigfu'     => 'refonte/test/architecture.webp',    // design événementiel
    ];

    /**
     * Données partagées : les réalisations viennent de la source unique
     * (Contenu), jamais dupliquées dans une vue. On y greffe seulement le
     * titre court et le visuel de test, sans écraser le titre d'origine —
     * la page de détail continue de l'afficher.
     */
    private function commun(): array
    {
        $realisations = Contenu::section('realisations');

        foreach ($realisations as $slug => &$projet) {
            $projet['titre_court'] = self::TITRES_COURTS[$slug] ?? ($projet['titre'] ?? '');

            if (isset(self::VISUELS_TEST[$slug])) {
                $projet['image'] = self::VISUELS_TEST[$slug];
            }
        }
        unset($projet);

        return [
            'realisations' => $realisations,
            'options'      => CibleController::formOptions(),
        ];
    }
}
