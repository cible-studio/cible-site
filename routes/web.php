<?php

use App\Http\Controllers\CibleController;
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

// Endpoint JSON de la carte réseau — lit un fichier statique
// public/data/reseau-map.json (pas de BDD, découplé de Panora).
Route::get('/api/reseau-map',   [CibleController::class, 'mapData'])
    ->middleware('throttle:60,1')
    ->name('api.reseau-map');
