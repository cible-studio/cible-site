<?php

namespace App\Support;

/**
 * Aides de la maquette de refonte (/marquette).
 *
 * Tout ce qui est propre à la maquette passe par ici ou par config/marquette.php :
 * le jour où elle remplace le site, ou si elle est abandonnée, rien d'autre
 * n'est à démêler.
 */
class Marquette
{
    /**
     * Attributs d'une photo du catalogue : src, srcset, largeur/hauteur
     * intrinsèques (évitent le saut de mise en page), texte alternatif.
     *
     * @return array{src:string, srcset:string, width:int, height:int, alt:string, credit:?string, lien:?string}
     */
    public static function photo(string $cle): array
    {
        $p = config("marquette.photos.$cle");

        abort_unless(is_array($p), 500, "Photo de maquette inconnue : $cle");

        [$w, $h] = $p['ratio'];

        if (isset($p['local'])) {
            $url    = fn (int $l) => asset("mq/img/photos/{$p['local']}-$l.webp");
            $src    = $url(1600);
            $srcset = $url(800) . ' 800w, ' . $url(1600) . ' 1600w';
        } else {
            // Recadrage et format (AVIF/WebP) faits par le CDN d'Unsplash.
            $url    = fn (int $l) => "https://images.unsplash.com/{$p['unsplash']}?auto=format&fit=crop&q=70&w=$l";
            $src    = $url(1600);
            $srcset = $url(800) . ' 800w, ' . $url(1200) . ' 1200w, ' . $url(1600) . ' 1600w';
        }

        return [
            'src'    => $src,
            'srcset' => $srcset,
            'width'  => $w,
            'height' => $h,
            'alt'    => $p['alt'],
            'credit' => $p['credit'] ?? null,
            'lien'   => $p['lien'] ?? null,
        ];
    }

    /** Photos d'illustration utilisées, pour le crédit en pied de page. */
    public static function credits(): array
    {
        return collect(config('marquette.photos'))
            ->filter(fn ($p) => isset($p['credit']))
            ->map(fn ($p) => ['nom' => $p['credit'], 'lien' => $p['lien']])
            ->unique('nom')
            ->values()
            ->all();
    }

    /** URL versionnée d'un fichier de public/mq (cache navigateur cassé à chaque modification). */
    public static function asset(string $chemin): string
    {
        $abs = public_path("mq/$chemin");

        return asset("mq/$chemin") . (is_file($abs) ? '?v=' . filemtime($abs) : '');
    }
}
