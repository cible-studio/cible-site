<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Coolify place un reverse proxy (Traefik) devant le conteneur : il
        // termine le TLS et transmet une requête en HTTP simple. Sans cette
        // ligne, $request->isSecure() est faux et Laravel génère TOUTES ses
        // URL en http:// alors que la page est servie en https:// — les
        // navigateurs bloquaient alors le favicon et le logo (contenu mixte),
        // et le <link rel="canonical"> annonçait une URL non sécurisée à
        // Google.
        //
        // Corrige aussi $request->ip() : sans proxy de confiance, le
        // formulaire /contact journalisait l'IP du proxy et non celle du
        // visiteur, rendant le rate-limit contournable... et surtout commun
        // à tous les visiteurs.
        //
        // `at: '*'` est le réglage attendu ici : l'IP du proxy est allouée
        // dynamiquement sur le réseau Docker. Cela suppose que le conteneur
        // ne soit joignable QUE via le proxy — c'est le cas sous Coolify.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
