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

];
