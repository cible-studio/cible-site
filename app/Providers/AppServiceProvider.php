<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->limitesDevis();
    }

    /**
     * Limites de débit du formulaire /contact.
     *
     * Deux seaux cumulés, parce qu'ils couvrent deux menaces différentes :
     *
     *  - `devis-ip`     borne l'acharnement d'une source unique ;
     *  - `devis-global` borne le volume total. Sans lui, une attaque
     *    distribuée (un envoi par adresse, des milliers d'adresses) passe
     *    sous le radar de la limite par IP et noie la boîte du commercial.
     *
     * Le plafond global est volontairement haut : 40 demandes en une heure
     * sur un site vitrine n'est pas un pic d'activité, c'est une anomalie.
     *
     * ⚠ Ces limites ne sont fiables que parce que le proxy Coolify est
     * déclaré de confiance (bootstrap/app.php) : sans cela, $request->ip()
     * renvoyait l'adresse du proxy pour tout le monde et le seau « par IP »
     * était en réalité commun à tous les visiteurs.
     */
    private function limitesDevis(): void
    {
        RateLimiter::for('devis-ip', fn (Request $r) => Limit::perMinutes(10, 5)->by($r->ip()));

        RateLimiter::for('devis-global', fn () => Limit::perHour(40)->by('devis-global'));
    }
}
