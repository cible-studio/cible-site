<?php

/*
|--------------------------------------------------------------------------
| Espace d'administration CIBLE
|--------------------------------------------------------------------------
|
| Compte unique, sans table `users`. Le hash se génère avec :
|
|     php artisan cible:admin-hash
|
| puis se colle dans CIBLE_ADMIN_HASH côté Coolify. Tant que l'une des deux
| variables est vide, /admin répond 404 : l'espace n'existe tout simplement
| pas pour un visiteur, et aucun scanner ne peut deviner qu'il existe.
|
*/

return [

    'admin' => [
        'email' => env('CIBLE_ADMIN_EMAIL'),
        'hash'  => env('CIBLE_ADMIN_HASH'),

        /*
        | Adresse de l'espace admin (2026-11-25). « /admin » est la première
        | adresse que testent les robots : en production, CIBLE_ADMIN_CHEMIN
        | porte une valeur longue et imprévisible, et /admin répond 404 comme
        | une page qui n'existe pas. Jamais écrite dans git.
        | Lettres minuscules, chiffres et tirets seulement. Une valeur vide,
        | trop courte ou réservée par une page du site retombe sur « admin »,
        | pour ne jamais rendre l'espace inaccessible par erreur.
        */
        'chemin' => (function () {
            $c = preg_replace('#[^a-z0-9-]#', '', strtolower(trim((string) env('CIBLE_ADMIN_CHEMIN', 'admin'), '/')));
            $reserves = ['qui-sommes-nous', 'services', 'reseau', 'references', 'contact', 'devis',
                         'api', 'visuels', 'marquette', 'mq', 'images', 'data', 'up', 'storage'];
            return ($c === '' || strlen($c) < 5 || in_array($c, $reserves, true)) ? 'admin' : $c;
        })(),
    ],

    /*
    | Réalisations (accueil, /references, /references/{slug}) — masquées
    | depuis le 2026-11-24 : les visuels en place n'étaient pas ceux des
    | clients (ex. la carte « Orange » montrait une publicité de CIBLE).
    | À false, la section, les boutons et les liens disparaissent et
    | /references redirige vers /services ; l'admin connecté voit toujours
    | les pages pour préparer le contenu. Réafficher = CIBLE_REALISATIONS=true
    | dans Coolify, sans toucher au code.
    */
    'realisations_visibles' => (bool) env('CIBLE_REALISATIONS', false),

    /*
    | Récapitulatif mensuel des demandes du formulaire (2026-11-25), envoyé
    | par « php artisan cible:rapport-demandes » (tâche planifiée Coolify,
    | le 1er du mois). Adresses séparées par des virgules ; vide = aucun envoi.
    | Le mail ne contient que des chiffres, aucune donnée personnelle.
    */
    'rapport_to' => array_values(array_filter(array_map('trim', explode(',', (string) env('CIBLE_RAPPORT_TO', ''))))),

];
