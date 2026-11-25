<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CibleController;
use App\Http\Controllers\MarquetteController;
use App\Http\Middleware\AdminProtege;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site vitrine CIBLE CI — routes racine
|--------------------------------------------------------------------------
|
| Projet Laravel indépendant. Toutes les routes sont à la racine
| (pas de préfixe /cible comme dans Panora).
|
*/

Route::get('/',                 [CibleController::class, 'home'])->name('home');
Route::get('/qui-sommes-nous',  [CibleController::class, 'qui'])->name('qui');
Route::get('/services',         [CibleController::class, 'services'])->name('services');
Route::get('/reseau',           [CibleController::class, 'reseau'])->name('reseau');
Route::get('/references',       [CibleController::class, 'references'])->name('references');
// Détail d'une réalisation (6 slugs statiques — cf. CibleController::projets()).
// Déclarée après /references : Laravel teste les routes dans l'ordre, la
// route statique reste donc prioritaire sur le paramètre.
Route::get('/references/{slug}', [CibleController::class, 'realisation'])->name('realisation');
Route::get('/contact',          [CibleController::class, 'contact'])->name('contact');
// Confirmation d'envoi : URL distincte pour que GTM compte les demandes
// (déclencheur « Page vue », Page Path = /contact/merci). Accessible
// seulement juste après un envoi — cf. CibleController::merci().
Route::get('/contact/merci',    [CibleController::class, 'merci'])->name('contact.merci');
// Deux limites cumulées :
//   - `devis-ip`     : 5 envois / 10 min par visiteur, contre l'acharnement
//                      d'une seule source ;
//   - `devis-global` : 40 envois / heure toutes sources confondues, contre
//                      une attaque distribuée qui contournerait la limite
//                      par IP en changeant d'adresse à chaque envoi. Ce
//                      plafond protège la boîte du commercial : au-delà,
//                      c'est de toute façon une anomalie, pas des prospects.
Route::post('/devis',           [CibleController::class, 'submitDevis'])
    ->middleware(['throttle:devis-ip', 'throttle:devis-global'])
    ->name('devis.submit');

// Maquette de refonte (2026-11-24) — servie à côté du site, sans le modifier.
// Non indexée (noindex dans sa mise en page), absente du menu. Le
// formulaire de sa page contact poste sur la vraie route /devis.
Route::prefix('marquette')->name('marquette.')->controller(MarquetteController::class)->group(function () {
    Route::get('/',                'accueil')->name('accueil');
    Route::get('/qui-sommes-nous', 'qui')->name('qui');
    Route::get('/services',        'services')->name('services');
    Route::get('/reseau',          'reseau')->name('reseau');
    Route::get('/contact',         'contact')->name('contact');
});

// Endpoint JSON de la carte réseau — lit un fichier statique
// public/data/reseau-map.json (pas de BDD, découplé de Panora).
Route::get('/api/reseau-map',   [CibleController::class, 'mapData'])
    ->middleware('throttle:60,1')
    ->name('api.reseau-map');

// Visuels de réalisation téléversés depuis l'admin. Publics (ils s'affichent
// sur le site) mais servis depuis le volume persistant, hors de public/.
Route::get('/visuels/{nom}',    [CibleController::class, 'visuel'])->name('visuel');

/*
|--------------------------------------------------------------------------
| Espace d'administration
|--------------------------------------------------------------------------
|
| Répond 404 tant que CIBLE_ADMIN_EMAIL et CIBLE_ADMIN_HASH ne sont pas
| renseignés : l'espace n'existe pas pour un visiteur, et rien ne signale à
| un scanner qu'il y aurait quelque chose à attaquer.
|
| La connexion est fortement limitée en débit — c'est la seule porte du
| site, donc la seule cible d'une attaque par force brute.
|
*/
// Adresse secrète (2026-11-25) : le préfixe vient de CIBLE_ADMIN_CHEMIN
// (config/cible.php) ; « admin » seulement tant qu'elle n'est pas renseignée.
// Toutes les vues passent par les noms de route (admin.*) : rien d'autre à
// changer quand l'adresse change.
Route::prefix(config('cible.admin.chemin'))->name('admin.')->group(function () {
    Route::get('/connexion',  [AdminController::class, 'login'])->name('login');
    Route::post('/connexion', [AdminController::class, 'authentifier'])
        ->middleware('throttle:6,10')
        ->name('authentifier');

    // Double authentification : code à 6 chiffres envoyé par e-mail après le
    // mot de passe. Saisie limitée (10 / 10 min) en plus des 5 essais par
    // code ; renvoi limité (3 / 10 min) pour ne pas inonder la boîte admin.
    Route::get('/verification',  [AdminController::class, 'verification'])->name('verification');
    Route::post('/verification', [AdminController::class, 'verifier'])
        ->middleware('throttle:10,10')
        ->name('verifier');
    Route::post('/verification/renvoyer', [AdminController::class, 'renvoyer'])
        ->middleware('throttle:3,10')
        ->name('renvoyer');
    Route::post('/deconnexion', [AdminController::class, 'deconnexion'])->name('deconnexion');

    Route::middleware(AdminProtege::class)->group(function () {
        Route::get('/', [AdminController::class, 'tableau'])->name('tableau');

        // Rapport des demandes du formulaire de contact (2026-11-25).
        Route::get('/demandes',                  [AdminController::class, 'demandes'])->name('demandes');
        Route::get('/demandes/export',           [AdminController::class, 'exporterDemandes'])->name('demandes.export');
        Route::post('/demandes/{id}/supprimer',  [AdminController::class, 'supprimerDemande'])->name('demandes.supprimer');

        Route::get('/coordonnees',  [AdminController::class, 'coordonnees'])->name('coordonnees');
        Route::post('/coordonnees', [AdminController::class, 'enregistrerCoordonnees'])->name('coordonnees.enregistrer');

        Route::get('/chiffres',  [AdminController::class, 'chiffres'])->name('chiffres');
        Route::post('/chiffres', [AdminController::class, 'enregistrerChiffres'])->name('chiffres.enregistrer');

        Route::get('/realisations',            [AdminController::class, 'realisations'])->name('realisations');
        Route::get('/realisations/{slug}',     [AdminController::class, 'editerRealisation'])->name('realisation.editer');
        Route::post('/realisations/{slug}',    [AdminController::class, 'enregistrerRealisation'])->name('realisation.enregistrer');

        // Écrans générés à partir de config/admin-schema.php : une seule
        // paire de routes couvre toutes les pages du site.
        Route::get('/page/{cle}',  [AdminController::class, 'page'])->name('page');
        Route::post('/page/{cle}', [AdminController::class, 'enregistrerPage'])->name('page.enregistrer');

        Route::post('/reinitialiser/{section}', [AdminController::class, 'reinitialiser'])->name('reinitialiser');
    });
});
