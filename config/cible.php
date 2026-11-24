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

];
