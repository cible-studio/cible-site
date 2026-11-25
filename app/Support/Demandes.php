<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Demandes reçues par le formulaire /contact (2026-11-25).
 *
 * Jusqu'ici le site ne gardait rien : chaque demande partait par e-mail puis
 * disparaissait. La direction veut un rapport ; les demandes sont donc
 * conservées, sans base de données :
 *
 *   storage/app/contenu/demandes/AAAA-MM.jsonl   une demande par ligne
 *   storage/app/contenu/demandes/archives.json   chiffres des mois purgés
 *
 * Le disque « contenu » est le volume persistant de Coolify (comme les
 * contenus de l'admin) : les demandes survivent aux redéploiements.
 *
 * Règles :
 *   - les pièces jointes ne sont JAMAIS conservées, seulement leur libellé ;
 *   - une demande est gardée MOIS_CONSERVES mois, puis effacée ; ses
 *     chiffres (sans aucune donnée personnelle) restent dans archives.json ;
 *   - l'enregistrement ne doit jamais faire échouer le formulaire : en cas
 *     d'erreur disque, la demande part quand même par e-mail.
 */
class Demandes
{
    public const MOIS_CONSERVES = 12;

    /** Choix multiples et uniques du formulaire, tels qu'affichés dans le rapport. */
    public const MULTIPLES = ['objectif' => 'Objectifs', 'services' => 'Services', 'zone' => 'Zones', 'cible' => 'Cibles'];
    public const UNIQUES   = ['budget' => 'Budget', 'periode' => 'Période', 'provenance' => 'Provenance'];

    public const PERIODES = [
        'mois'           => 'Ce mois-ci',
        'mois-precedent' => 'Mois précédent',
        '3-mois'         => '3 derniers mois',
        '12-mois'        => '12 derniers mois',
    ];

    private const DOSSIER  = 'demandes';
    private const ARCHIVES = 'demandes/archives.json';

    /* ═══════════════════ Écriture ═══════════════════ */

    /**
     * Enregistre une demande (libellés lisibles, comme dans le mail du
     * commercial) et renvoie son identifiant, ou null si le disque a refusé.
     *
     * Un visiteur qui renvoie la même demande dans les 30 minutes (après un
     * échec d'envoi du mail, typiquement) ne crée pas de doublon.
     */
    public static function enregistrer(array $d): ?string
    {
        try {
            $maintenant = now();
            $empreinte  = hash('sha256', implode('|', [
                mb_strtolower(trim((string) ($d['email'] ?? ''))),
                mb_strtolower(trim((string) ($d['entreprise'] ?? ''))),
                trim((string) ($d['message'] ?? '')),
            ]));
            $id = null;

            self::modifierMois($maintenant->format('Y-m'), function (array $lignes) use ($d, $maintenant, $empreinte, &$id) {
                foreach ($lignes as $l) {
                    if (($l['empreinte'] ?? null) === $empreinte
                        && Carbon::parse($l['recue_le'])->gt($maintenant->copy()->subMinutes(30))) {
                        $id = $l['id'];
                        return $lignes;
                    }
                }

                $id = (string) Str::ulid();
                $lignes[] = [
                    'id'         => $id,
                    'recue_le'   => $maintenant->toIso8601String(),
                    'nom'        => $d['nom'] ?? null,
                    'entreprise' => $d['entreprise'] ?? null,
                    'poste'      => $d['poste'] ?? null,
                    'email'      => $d['email'] ?? null,
                    'tel'        => $d['tel'] ?? null,
                    'objectif'   => array_values($d['objectif'] ?? []),
                    'cible'      => array_values($d['cible'] ?? []),
                    'zone'       => array_values($d['zone'] ?? []),
                    'services'   => array_values($d['services'] ?? []),
                    'periode'    => $d['periode'] ?? null,
                    'budget'     => $d['budget'] ?? null,
                    'provenance' => $d['provenance'] ?? null,
                    'message'    => $d['message'] ?? null,
                    'documents'  => array_values($d['documents'] ?? []),
                    'mail'       => null,   // true / false une fois l'envoi tenté
                    'empreinte'  => $empreinte,
                ];

                return $lignes;
            });

            self::purger();

            return $id;
        } catch (\Throwable $e) {
            Journal::erreur('cible.demandes.enregistrement_impossible', ['erreur' => $e->getMessage()]);
            return null;
        }
    }

    /** Note si le mail de la demande est bien parti (affiché dans l'admin). */
    public static function marquerMail(?string $id, bool $envoye): void
    {
        if ($id === null) {
            return;
        }

        try {
            // Mois courant d'abord ; mois précédent si l'envoi a chevauché minuit.
            foreach ([now(), now()->subMonthNoOverflow()] as $date) {
                $trouve = false;
                self::modifierMois($date->format('Y-m'), function (array $lignes) use ($id, $envoye, &$trouve) {
                    foreach ($lignes as &$l) {
                        if ($l['id'] === $id) {
                            $l['mail'] = $envoye;
                            $trouve = true;
                        }
                    }
                    return $lignes;
                });
                if ($trouve) {
                    return;
                }
            }
        } catch (\Throwable $e) {
            Journal::erreur('cible.demandes.statut_mail_impossible', ['erreur' => $e->getMessage()]);
        }
    }

    /** Efface une demande (droit à l'effacement d'un prospect). */
    public static function supprimer(string $id): bool
    {
        foreach (self::moisDetailles() as $mois) {
            $trouve = false;
            self::modifierMois($mois, function (array $lignes) use ($id, &$trouve) {
                $reste = array_values(array_filter($lignes, fn ($l) => $l['id'] !== $id));
                $trouve = count($reste) !== count($lignes);
                return $reste;
            });
            if ($trouve) {
                return true;
            }
        }

        return false;
    }

    /**
     * Efface le détail des mois sortis de la période de conservation, après
     * avoir archivé leurs chiffres (sans données personnelles).
     */
    public static function purger(): void
    {
        $limite = now()->startOfMonth()->subMonthsNoOverflow(self::MOIS_CONSERVES - 1)->format('Y-m');
        $archives = self::archives();
        $change = false;

        foreach (self::moisDetailles() as $mois) {
            if ($mois >= $limite) {
                continue;
            }
            $archives[$mois] = self::compter(self::lireMois($mois));
            self::disque()->delete(self::fichier($mois));
            $change = true;
            Journal::avertir('cible.demandes.mois_purge', ['mois' => $mois, 'total' => $archives[$mois]['total']]);
        }

        if ($change) {
            ksort($archives);
            self::disque()->put(self::ARCHIVES, json_encode($archives, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }

    /* ═══════════════════ Lecture ═══════════════════ */

    /** @return array{0:Carbon,1:Carbon,2:string} début, fin, libellé */
    public static function periode(string $cle): array
    {
        $n = now();

        return match ($cle) {
            'mois'           => [$n->copy()->startOfMonth(), $n->copy(), self::libelleMois($n->format('Y-m'))],
            'mois-precedent' => [$n->copy()->subMonthNoOverflow()->startOfMonth(), $n->copy()->subMonthNoOverflow()->endOfMonth(),
                                 self::libelleMois($n->copy()->subMonthNoOverflow()->format('Y-m'))],
            '12-mois'        => [$n->copy()->startOfMonth()->subMonthsNoOverflow(11), $n->copy(), self::PERIODES['12-mois']],
            default          => [$n->copy()->startOfMonth()->subMonthsNoOverflow(2), $n->copy(), self::PERIODES['3-mois']],
        };
    }

    /** Période couvrant un mois donné (AAAA-MM), pour le rapport mensuel. */
    public static function periodeMois(string $mois): array
    {
        $debut = Carbon::createFromFormat('Y-m-d H:i:s', "$mois-01 00:00:00");

        return [$debut, $debut->copy()->endOfMonth(), self::libelleMois($mois)];
    }

    public static function libelleMois(string $mois): string
    {
        return Str::ucfirst(Carbon::createFromFormat('Y-m-d', "$mois-01")->locale('fr')->isoFormat('MMMM YYYY'));
    }

    /** Demandes de la période, les plus récentes d'abord. */
    public static function lister(Carbon $debut, Carbon $fin): array
    {
        $lignes = [];
        foreach (self::moisEntre($debut, $fin) as $mois) {
            foreach (self::lireMois($mois) as $l) {
                $quand = Carbon::parse($l['recue_le']);
                if ($quand->betweenIncluded($debut, $fin)) {
                    $lignes[] = $l;
                }
            }
        }

        usort($lignes, fn ($a, $b) => strcmp($b['recue_le'], $a['recue_le']));

        return $lignes;
    }

    /**
     * Chiffres de la période : total, demandes par mois, répartitions.
     * Les mois purgés sont repris de archives.json.
     */
    public static function statistiques(Carbon $debut, Carbon $fin): array
    {
        $stats = self::compter(self::lister($debut, $fin));
        $archives = self::archives();
        $detailles = self::moisDetailles();

        $parMois = [];
        foreach (self::moisEntre($debut, $fin) as $mois) {
            $parMois[$mois] = 0;
            if (!in_array($mois, $detailles, true) && isset($archives[$mois])) {
                $stats = self::fusionner($stats, $archives[$mois]);
                $parMois[$mois] = $archives[$mois]['total'];
            }
        }
        foreach (self::lister($debut, $fin) as $l) {
            $parMois[substr($l['recue_le'], 0, 7)]++;
        }
        $stats['par_mois'] = $parMois;

        return $stats;
    }

    /** Export tableur : CSV « ; » + BOM UTF-8, ouvert tel quel par Excel en français. */
    public static function csv(array $lignes): string
    {
        $colonnes = ['Reçue le', 'Nom', 'Entreprise', 'Poste', 'E-mail', 'Téléphone', 'Objectifs', 'Cibles', 'Zones',
                     'Services', 'Période', 'Budget', 'Provenance', 'Message', 'Documents joints', 'Mail envoyé'];

        $sortie = "\u{FEFF}" . self::ligneCsv($colonnes);
        foreach ($lignes as $l) {
            $sortie .= self::ligneCsv([
                Carbon::parse($l['recue_le'])->timezone('Africa/Abidjan')->format('d/m/Y H:i'),
                $l['nom'], $l['entreprise'], $l['poste'], $l['email'], $l['tel'],
                implode(', ', $l['objectif']), implode(', ', $l['cible']), implode(', ', $l['zone']), implode(', ', $l['services']),
                $l['periode'], $l['budget'], $l['provenance'], $l['message'], implode(', ', $l['documents']),
                $l['mail'] === null ? '' : ($l['mail'] ? 'Oui' : 'Non'),
            ]);
        }

        return $sortie;
    }

    /* ═══════════════════ Interne ═══════════════════ */

    private static function disque()
    {
        return Storage::disk('contenu');
    }

    private static function fichier(string $mois): string
    {
        return self::DOSSIER . "/$mois.jsonl";
    }

    /** Mois dont le détail existe encore (fichiers AAAA-MM.jsonl), du plus ancien au plus récent. */
    private static function moisDetailles(): array
    {
        if (!self::disque()->exists(self::DOSSIER)) {
            return [];
        }

        $mois = [];
        foreach (self::disque()->files(self::DOSSIER) as $f) {
            if (preg_match('#/(\d{4}-\d{2})\.jsonl$#', $f, $m)) {
                $mois[] = $m[1];
            }
        }
        sort($mois);

        return $mois;
    }

    private static function moisEntre(Carbon $debut, Carbon $fin): array
    {
        $mois = [];
        for ($d = $debut->copy()->startOfMonth(); $d->lte($fin); $d->addMonthNoOverflow()) {
            $mois[] = $d->format('Y-m');
        }

        return $mois;
    }

    private static function lireMois(string $mois): array
    {
        if (!self::disque()->exists(self::fichier($mois))) {
            return [];
        }

        return self::decoder(self::disque()->get(self::fichier($mois)));
    }

    private static function archives(): array
    {
        if (!self::disque()->exists(self::ARCHIVES)) {
            return [];
        }

        return json_decode(self::disque()->get(self::ARCHIVES), true) ?: [];
    }

    /**
     * Lecture-modification-écriture d'un mois sous verrou exclusif : deux
     * envois simultanés du formulaire ne peuvent pas s'écraser.
     */
    private static function modifierMois(string $mois, callable $modifier): void
    {
        self::disque()->makeDirectory(self::DOSSIER);
        $chemin = self::disque()->path(self::fichier($mois));

        $h = fopen($chemin, 'c+');
        if ($h === false) {
            throw new \RuntimeException("Impossible d'ouvrir $chemin");
        }

        try {
            flock($h, LOCK_EX);
            $lignes = $modifier(self::decoder((string) stream_get_contents($h)));
            ftruncate($h, 0);
            rewind($h);
            fwrite($h, implode('', array_map(
                fn ($l) => json_encode($l, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n",
                $lignes
            )));
            fflush($h);
        } finally {
            flock($h, LOCK_UN);
            fclose($h);
        }
    }

    private static function decoder(string $brut): array
    {
        return array_values(array_filter(
            array_map(fn ($l) => json_decode($l, true), preg_split('/\R/', $brut)),
            'is_array'
        ));
    }

    /** Totaux et répartitions d'une liste de demandes (aucune donnée personnelle). */
    private static function compter(array $lignes): array
    {
        $stats = ['total' => count($lignes), 'mails_non_partis' => 0, 'repartitions' => []];
        foreach (array_keys(self::MULTIPLES + self::UNIQUES) as $cle) {
            $stats['repartitions'][$cle] = [];
        }

        foreach ($lignes as $l) {
            if (($l['mail'] ?? null) === false) {
                $stats['mails_non_partis']++;
            }
            foreach (array_keys(self::MULTIPLES) as $cle) {
                foreach ($l[$cle] ?? [] as $v) {
                    $stats['repartitions'][$cle][$v] = ($stats['repartitions'][$cle][$v] ?? 0) + 1;
                }
            }
            foreach (array_keys(self::UNIQUES) as $cle) {
                $v = $l[$cle] ?? null;
                $v = $v === null || $v === '' ? 'Non précisé' : $v;
                $stats['repartitions'][$cle][$v] = ($stats['repartitions'][$cle][$v] ?? 0) + 1;
            }
        }

        foreach ($stats['repartitions'] as &$r) {
            arsort($r);
        }

        return $stats;
    }

    private static function fusionner(array $a, array $b): array
    {
        $a['total'] += $b['total'];
        $a['mails_non_partis'] += $b['mails_non_partis'] ?? 0;
        foreach ($b['repartitions'] ?? [] as $cle => $r) {
            foreach ($r as $v => $n) {
                $a['repartitions'][$cle][$v] = ($a['repartitions'][$cle][$v] ?? 0) + $n;
            }
            arsort($a['repartitions'][$cle]);
        }

        return $a;
    }

    /** Cellule CSV sûre : une valeur commençant par = + - @ ne devient pas une formule Excel. */
    private static function ligneCsv(array $valeurs): string
    {
        return implode(';', array_map(function ($v) {
            $v = (string) $v;
            if ($v !== '' && in_array($v[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
                $v = "'" . $v;
            }
            return '"' . str_replace('"', '""', $v) . '"';
        }, $valeurs)) . "\r\n";
    }
}
