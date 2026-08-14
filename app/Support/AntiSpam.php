<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Analyse anti-robot du formulaire /contact.
 *
 * Principe : plutôt qu'une règle unique et cassante (« pas de lien » →
 * rejette un vrai prospect qui colle l'URL de son site), on additionne des
 * signaux faibles. Un envoi n'est écarté qu'au-delà d'un seuil, et chaque
 * décision est journalisée avec le détail des signaux, pour pouvoir régler
 * la sensibilité sur des cas réels plutôt qu'au jugé.
 *
 * Contexte : le formulaire a reçu du spam d'affiliation en août 2026 (nom
 * aléatoire, téléphone hors format, corps en allemand avec un lien
 * raccourci). Le pot de miel seul ne suffit pas — un robot qui remplit
 * correctement les champs le contourne sans effort.
 */
class AntiSpam
{
    /** Au-delà de ce total, l'envoi est considéré comme automatisé. */
    public const SEUIL = 5;

    /** Délai minimal de remplissage crédible pour un humain (secondes). */
    private const DELAI_MINIMAL = 4;

    /** Au-delà, le formulaire a été ouvert trop longtemps : jeton rejoué. */
    private const DELAI_MAXIMAL = 7200; // 2 h — aligné sur la durée de session

    /** Raccourcisseurs d'URL : seul intérêt en formulaire = masquer une cible. */
    private const RACCOURCISSEURS = [
        'bit.ly', 'tinyurl.com', 'goo.gl', 't.co', 'ow.ly', 'is.gd', 'buff.ly',
        'cutt.ly', 'rb.gy', 'shorturl.at', 'tiny.cc', 'lnkd.in', 'rebrand.ly',
    ];

    /**
     * @return array{score:int, signaux:array<int,string>}
     */
    public static function analyser(Request $request, array $data): array
    {
        $signaux = [];
        $score   = 0;

        // ─── 1. Pot de miel ────────────────────────────────────────────
        // Champ invisible : un humain ne peut pas le remplir.
        foreach (['website', 'company_url'] as $piege) {
            if (filled($request->input($piege))) {
                $signaux[] = "pot-de-miel:$piege";
                $score += 10; // à lui seul, disqualifiant
            }
        }

        // ─── 2. Piège temporel ─────────────────────────────────────────
        $ouvert = self::instantOuverture($request->input('_ts'));
        if ($ouvert === null) {
            // Jeton absent ou falsifié : un navigateur normal le renvoie.
            $signaux[] = 'horodatage:absent-ou-invalide';
            $score += 4;
        } else {
            $ecoule = time() - $ouvert;
            if ($ecoule < self::DELAI_MINIMAL) {
                $signaux[] = "horodatage:trop-rapide({$ecoule}s)";
                $score += 5;
            } elseif ($ecoule > self::DELAI_MAXIMAL) {
                $signaux[] = 'horodatage:perime';
                $score += 2;
            }
        }

        // ─── 3. Liens dans les champs d'identité ───────────────────────
        // Une URL n'y est jamais légitime.
        foreach (['nom', 'entreprise', 'poste', 'tel'] as $champ) {
            if (self::contientUrl($data[$champ] ?? '')) {
                $signaux[] = "url-interdite:$champ";
                $score += 5;
            }
        }

        // ─── 4. Liens dans la description ──────────────────────────────
        // Tolérés (un prospect peut citer son site), sauf raccourcisseurs
        // et accumulation manifeste.
        $message = (string) ($data['message'] ?? '');
        $liens   = self::compterUrls($message);
        if ($liens >= 3) {
            $signaux[] = "message:$liens-liens";
            $score += 3;
        }
        if (self::contientRaccourcisseur($message . ' ' . implode(' ', array_map(
            fn ($c) => (string) ($data[$c] ?? ''),
            ['nom', 'entreprise', 'poste']
        )))) {
            $signaux[] = 'raccourcisseur-url';
            $score += 5;
        }

        // ─── 5. Balises HTML dans du texte libre ───────────────────────
        // Un humain n'écrit pas « <a href= » dans un formulaire de contact.
        if (preg_match('#<\s*(a|b|strong|script|iframe|img)\b#i', $message)) {
            $signaux[] = 'html-dans-message';
            $score += 4;
        }

        // ─── 6. Écriture non latine dans l'identité ────────────────────
        // Signal faible seul (un nom peut être translittéré), mais très
        // discriminant combiné aux autres. Jamais disqualifiant à lui seul.
        $identite = ($data['nom'] ?? '') . ' ' . ($data['entreprise'] ?? '');
        if (preg_match('#[\x{0400}-\x{04FF}\x{4E00}-\x{9FFF}\x{0600}-\x{06FF}]#u', $identite)) {
            $signaux[] = 'ecriture-non-latine';
            $score += 2;
        }

        // ─── 7. Nom et entreprise strictement identiques ───────────────
        // Signature classique du remplissage automatique.
        $nom  = mb_strtolower(trim((string) ($data['nom'] ?? '')));
        $ent  = mb_strtolower(trim((string) ($data['entreprise'] ?? '')));
        if ($nom !== '' && $nom === $ent) {
            $signaux[] = 'nom-egal-entreprise';
            $score += 2;
        }

        // ─── 8. Aucun choix coché ──────────────────────────────────────
        // Un prospect réel renseigne au moins un objectif ou un service ;
        // les robots ne cochent que le strict nécessaire à la validation.
        $choix = array_merge(
            $data['objectif'] ?? [], $data['cible'] ?? [],
            $data['zone'] ?? [], $data['services'] ?? []
        );
        if ($choix === [] && filled($data['budget'] ?? null) === false) {
            $signaux[] = 'aucun-choix-coche';
            $score += 2;
        }

        return ['score' => $score, 'signaux' => $signaux];
    }

    /** Jeton d'ouverture à déposer dans le formulaire (chiffré, donc infalsifiable). */
    public static function jeton(): string
    {
        return encrypt((string) time());
    }

    private static function instantOuverture(?string $jeton): ?int
    {
        if (!is_string($jeton) || $jeton === '') {
            return null;
        }

        try {
            $t = (int) decrypt($jeton);
        } catch (\Throwable) {
            return null; // jeton bricolé
        }

        return $t > 0 ? $t : null;
    }

    private static function contientUrl(string $v): bool
    {
        return (bool) preg_match('#(https?://|www\.)#i', $v);
    }

    private static function compterUrls(string $v): int
    {
        return preg_match_all('#(https?://|www\.)#i', $v);
    }

    private static function contientRaccourcisseur(string $v): bool
    {
        foreach (self::RACCOURCISSEURS as $d) {
            if (stripos($v, $d) !== false) {
                return true;
            }
        }

        return false;
    }
}
