<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Contenu éditable : défauts versionnés + surcharges persistées.
 *
 * Architecture délibérée :
 *
 *   défaut   → config/contenu.php, dans git, déployé avec le code
 *   surcharge→ storage/app/contenu/<section>.json, sur le volume persistant
 *   affiché  → surcharge si elle existe, sinon défaut
 *
 * Le volume n'est donc jamais la source de vérité, seulement une couche
 * par-dessus. Si Coolify perd le volume, s'il est mal monté ou vidé, le
 * site continue d'afficher le dernier contenu committé — au lieu de la
 * page blanche qu'on obtiendrait avec un stockage unique en base.
 *
 * C'est le point qui rendait l'admin risqué sur ce projet : le conteneur
 * est éphémère, on l'a constaté avec les logs.
 */
class Contenu
{
    /** Cache par requête : évite de relire le disque à chaque appel de vue. */
    private static array $cache = [];

    /** Sections administrables. La clé sert aussi de nom de fichier. */
    public const SECTIONS = ['coordonnees', 'chiffres', 'realisations'];

    /**
     * Retourne une section complète, surcharges appliquées.
     */
    public static function section(string $section): array
    {
        if (isset(self::$cache[$section])) {
            return self::$cache[$section];
        }

        $defaut = config("contenu.$section", []);
        $surcharge = self::lireSurcharge($section);

        // Fusion à un niveau : une entrée surchargée remplace la valeur par
        // défaut, les entrées absentes de la surcharge restent au défaut.
        // Ainsi l'ajout d'un champ dans config/contenu.php apparaît
        // immédiatement, sans devoir réenregistrer depuis l'admin.
        $fusion = $defaut;
        foreach ($surcharge as $cle => $valeur) {
            if (is_array($valeur) && isset($defaut[$cle]) && is_array($defaut[$cle])) {
                $fusion[$cle] = array_replace($defaut[$cle], $valeur);
            } else {
                $fusion[$cle] = $valeur;
            }
        }

        return self::$cache[$section] = $fusion;
    }

    /**
     * Valeur unitaire, en notation pointée : Contenu::get('coordonnees.tel_mobile')
     */
    public static function get(string $chemin, mixed $defaut = null): mixed
    {
        [$section, $cle] = array_pad(explode('.', $chemin, 2), 2, null);

        $valeurs = self::section($section);

        return $cle === null ? $valeurs : ($valeurs[$cle] ?? $defaut);
    }

    /**
     * Enregistre une section. Écrit la surcharge complète, pas un delta :
     * une relecture reste ainsi lisible et diffable à la main en cas de
     * besoin d'intervention directe sur le volume.
     */
    public static function enregistrer(string $section, array $valeurs): bool
    {
        if (!in_array($section, self::SECTIONS, true)) {
            return false;
        }

        // JSON_INVALID_UTF8_SUBSTITUTE : un texte collé depuis Word ou un PDF
        // contient parfois un octet invalide. Sans cette option, json_encode
        // renvoie false, on écrirait un fichier VIDE en annonçant un succès,
        // et la section repartirait silencieusement sur ses valeurs par
        // défaut à l'affichage suivant. Le cas s'est produit en test.
        $json = json_encode(
            $valeurs,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
        );

        if ($json === false || $json === '') {
            Log::error('cible.contenu.encodage_impossible', [
                'section' => $section, 'erreur' => json_last_error_msg(),
            ]);

            return false;
        }

        try {
            Storage::disk('contenu')->put("$section.json", $json);

            unset(self::$cache[$section]);

            return true;
        } catch (\Throwable $e) {
            // Volume absent ou non inscriptible : on trace et on refuse, mais
            // le site public continue de fonctionner sur les défauts.
            Log::error('cible.contenu.ecriture_impossible', [
                'section' => $section, 'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /** Efface la surcharge d'une section : retour au contenu de git. */
    public static function reinitialiser(string $section): bool
    {
        try {
            Storage::disk('contenu')->delete("$section.json");
            unset(self::$cache[$section]);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /** La section est-elle surchargée (donc différente de git) ? */
    public static function estSurchargee(string $section): bool
    {
        try {
            return Storage::disk('contenu')->exists("$section.json");
        } catch (\Throwable) {
            return false;
        }
    }

    /** Le stockage est-il réellement inscriptible ? Affiché dans l'admin. */
    public static function stockageDisponible(): bool
    {
        try {
            Storage::disk('contenu')->put('.verification', (string) time());
            Storage::disk('contenu')->delete('.verification');

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * URL d'affichage d'un visuel de réalisation.
     *
     * Deux origines coexistent :
     *  - « images/cible/campagne-1.jpg » : livré avec le code, servi par
     *    public/ comme n'importe quel asset ;
     *  - « visuel:nom.jpg » : téléversé depuis l'admin, stocké sur le volume
     *    persistant et servi par la route dédiée.
     *
     * public/ est reconstruit depuis git à chaque déploiement : y déposer un
     * fichier téléversé le ferait disparaître au déploiement suivant.
     */
    public static function urlImage(string $reference): string
    {
        if (str_starts_with($reference, 'visuel:')) {
            return route('visuel', ['nom' => substr($reference, 7)]);
        }

        return asset($reference);
    }

    /** Le visuel est-il réellement disponible ? (évite une vignette cassée) */
    public static function imageExiste(string $reference): bool
    {
        if (str_starts_with($reference, 'visuel:')) {
            try {
                return Storage::disk('contenu')->exists('visuels/' . substr($reference, 7));
            } catch (\Throwable) {
                return false;
            }
        }

        return file_exists(public_path($reference));
    }

    private static function lireSurcharge(string $section): array
    {
        try {
            if (!Storage::disk('contenu')->exists("$section.json")) {
                return [];
            }

            $json = json_decode(Storage::disk('contenu')->get("$section.json"), true);

            return is_array($json) ? $json : [];
        } catch (\Throwable $e) {
            // Un JSON corrompu ne doit pas casser le site : on ignore la
            // surcharge et on sert les défauts.
            Log::warning('cible.contenu.surcharge_illisible', [
                'section' => $section, 'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
