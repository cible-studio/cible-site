<?php

/*
|--------------------------------------------------------------------------
| Maquette de refonte — /marquette (2026-11-24)
|--------------------------------------------------------------------------
|
| Proposition de refonte servie à côté du site, sans le modifier : mêmes
| textes (Contenu::get), nouvelle mise en page. Non indexée, absente du menu.
|
| Photos : une clé = une image. Pour remplacer une photo provisoire, changer
| sa ligne ici et nulle part ailleurs.
|   - 'local'    : dérivés WebP des vraies photos CIBLE, générés depuis
|                  public/images (public/mq/img/photos/<nom>-800|1600.webp)
|   - 'unsplash' : photo d'illustration provisoire, servie par images.unsplash.com
|                  (autorisé dans la CSP, cf. SecurityHeaders) — à remplacer
|                  par des visuels CIBLE avant toute mise en production.
|
*/

return [

    'photos' => [
        // ── Vraies photos du réseau CIBLE ──────────────────────────────
        'axe'       => ['local' => 'axe-abidjan',   'ratio' => [1600, 1067], 'alt' => 'Axe routier d\'Abidjan bordé de panneaux publicitaires CIBLE'],
        'uber'      => ['local' => 'panneau-uber',  'ratio' => [1600, 1067], 'alt' => 'Panneau grand format CIBLE en bord de route à Abidjan'],
        'camion'    => ['local' => 'camion-podium', 'ratio' => [1600, 1067], 'alt' => 'Camion podium CIBLE avec écran LED'],
        'route'     => ['local' => 'panneau-route', 'ratio' => [1600, 1067], 'alt' => 'Panneau CIBLE photographié sur le terrain, au passage d\'une moto'],
        'bache'     => ['local' => 'bache-plateau', 'ratio' => [1600, 2133], 'alt' => 'Très grand format CIBLE au Plateau'],
        'perroquet' => ['local' => 'perroquet',     'ratio' => [1600, 904],  'alt' => 'Perroquet écarlate, symbole de la marque CIBLE'],

        // ── Illustrations provisoires (Unsplash) ───────────────────────
        'foule'    => ['unsplash' => 'photo-1781263378197-9ea12f94b827', 'ratio' => [1600, 1069], 'alt' => 'Public en liesse lors d\'un événement',
                       'credit' => 'Aliburhan S', 'lien' => 'https://unsplash.com/photos/EkzH-dD_agk'],
        'course'   => ['unsplash' => 'photo-1519530782816-ba0c305fbb0d', 'ratio' => [1600, 1067], 'alt' => 'Participants en couleurs sous un ciel bleu',
                       'credit' => 'Nqobile Vundla', 'lien' => 'https://unsplash.com/photos/5pSUw8o_xwc'],
        'tournage' => ['unsplash' => 'photo-1632187981988-40f3cbaeef5e', 'ratio' => [1600, 1067], 'alt' => 'Équipe de tournage autour d\'une caméra',
                       'credit' => 'Jakob Owens', 'lien' => 'https://unsplash.com/photos/xKfS7Hll0Ck'],
        'mobile'   => ['unsplash' => 'photo-1739271933163-8dcc7c8e8a3e', 'ratio' => [1600, 1067], 'alt' => 'Femme souriante consultant son téléphone',
                       'credit' => 'Ninthgrid', 'lien' => 'https://unsplash.com/photos/aCAxMbGlr2A'],
        'concert'  => ['unsplash' => 'photo-1778847195158-18f3137a07a8', 'ratio' => [1600, 1067], 'alt' => 'Scène et public lors d\'un concert',
                       'credit' => 'Bemnet Mesfin', 'lien' => 'https://unsplash.com/photos/XqrA7pnJqYA'],
    ],

];
