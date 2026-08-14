<?php

namespace App\Http\Controllers;

use App\Mail\CibleContactMail;
use App\Support\AntiSpam;
use App\Support\Contenu;
use App\Support\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Site vitrine CIBLE CI — régie publicitaire en Côte d'Ivoire.
 *
 * Projet Laravel indépendant (2026-08-05) — extrait de Panora.
 * Panora reste le SaaS interne CIBLE ; ce site est la vitrine
 * publique, sans aucune dépendance à Panora (si Panora tombe,
 * ce site continue de fonctionner).
 *
 * Routes (racine) :
 *   /                     → home (manifeste + preuves + CTA)
 *   /qui-sommes-nous      → histoire 30 ans + distinctions
 *   /services             → 4 pôles + phygital + workflow + objectifs
 *   /reseau               → parc média +400 panneaux · 31 communes + carte Leaflet
 *   /references           → réalisations filtrables + clients
 *   /references/{slug}    → détail d'une réalisation (6 slugs statiques)
 *   /contact              → mini-brief + coordonnées
 *   POST /devis           → réception formulaire (rate-limited, upload possible)
 *   GET  /api/reseau-map  → JSON statique pour la carte (pas de BDD)
 */
class CibleController extends Controller
{
    public function home()             { return view('home',              $this->baseData('home') + ['projets' => self::projets()]); }
    public function qui()              { return view('qui-sommes-nous',   $this->baseData('qui')); }
    public function services()         { return view('services',          $this->baseData('services')); }
    public function reseau()           { return view('reseau',            $this->baseData('reseau')); }
    public function references()       { return view('references',        $this->baseData('references') + ['projets' => self::projets()]); }
    public function contact()          { return view('contact',           $this->baseData('contact')); }

    /**
     * Page détail d'une réalisation — /references/{slug}
     *
     * Contenu 100 % statique issu du document textes (pas de BDD).
     * Un slug inconnu renvoie un 404 standard.
     */
    public function realisation(string $slug)
    {
        $projets = self::projets();

        abort_unless(isset($projets[$slug]), 404);

        $slugs    = array_keys($projets);
        $position = array_search($slug, $slugs, true);

        return view('realisation', $this->baseData('references') + [
            'projet'    => $projets[$slug],
            'slug'      => $slug,
            // Navigation circulaire entre projets — évite un cul-de-sac en
            // bas de page quand le visiteur enchaîne les réalisations.
            'precedent' => $slugs[($position - 1 + count($slugs)) % count($slugs)],
            'suivant'   => $slugs[($position + 1) % count($slugs)],
        ]);
    }

    /**
     * Les 6 réalisations affichées sur /references, sur l'accueil et sur
     * les pages détail. Source unique pour éviter toute divergence entre
     * les trois emplacements.
     *
     * `filtres` alimente la barre de filtres de /references : les clés
     * correspondent à REF.FILTRE.2 → REF.FILTRE.7 du document textes.
     */
    public static function projets(): array
    {
        // Source unique : les défauts vivent dans config/contenu.php et
        // l'espace admin les surcharge sur le volume persistant.
        return Contenu::section('realisations');
    }

    protected function baseData(string $current): array
    {
        return [
            'current' => $current,
        ];
    }

    /**
     * Sert un visuel de réalisation téléversé depuis l'admin — /visuels/{nom}
     *
     * Ces fichiers vivent sur le volume persistant, hors de public/ qui est
     * reconstruit depuis git à chaque déploiement. Le nom est strictement
     * contraint : sans cela, un « ../ » permettrait de remonter l'arborescence
     * et de lire n'importe quel fichier lisible par le serveur.
     */
    public function visuel(string $nom)
    {
        abort_unless((bool) preg_match('#^[A-Za-z0-9._-]+$#', $nom) && !str_contains($nom, '..'), 404);

        $disque = Storage::disk('contenu');

        abort_unless($disque->exists("visuels/$nom"), 404);

        return response($disque->get("visuels/$nom"), 200, [
            'Content-Type'  => $disque->mimeType("visuels/$nom") ?: 'image/jpeg',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Endpoint public JSON pour la carte du réseau — /api/reseau-map
     *
     * Lecture d'un fichier statique JSON (public/data/reseau-map.json)
     * plutôt qu'une requête BDD — le site vitrine n'a pas de BDD, et
     * on veut découplage total avec Panora. Le JSON est mis à jour
     * manuellement quand le parc panneaux évolue significativement
     * (rare — quelques fois par an).
     *
     * Cache 1h en RAM pour éviter de re-lire le disque à chaque hit.
     */
    public function mapData()
    {
        $rows = Cache::remember('cible.reseau_map.v1', now()->addHour(), function () {
            $path = public_path('data/reseau-map.json');
            if (!file_exists($path)) return [];
            $decoded = json_decode(file_get_contents($path), true);
            return is_array($decoded) ? $decoded : [];
        });

        return response()->json(['pins' => $rows], 200)
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Options du formulaire /contact — structuré comme un mini-brief
     * marketing (document textes 2026-08-10).
     *
     * Chaque entrée est `valeur_stockée => libellé affiché`. La vue rend
     * les libellés, la validation contrôle les clés, et submitDevis()
     * retraduit les clés en libellés avant l'envoi du mail : le commercial
     * reçoit du texte lisible, jamais des slugs.
     */
    public static function formOptions(): array
    {
        return [
            'objectif' => [
                'notoriete'     => 'Développer la notoriété de ma marque',
                'lancement'     => 'Lancer un nouveau produit ou service',
                'trafic'        => 'Générer du trafic vers un point de vente',
                'ventes'        => 'Augmenter mes ventes',
                'evenement'     => 'Communiquer sur un événement',
                'valorisation'  => 'Valoriser mon entreprise',
                'recrutement'   => 'Recruter',
                'institutionnel'=> 'Communication institutionnelle',
                'autre'         => 'Autre',
            ],
            'cible' => [
                'grand-public' => 'Grand public',
                'b2b'          => 'Professionnels (B2B)',
                'jeunes'       => 'Jeunes',
                'femmes'       => 'Femmes',
                'entreprises'  => 'Entreprises',
                'institutions' => 'Institutions',
                'decideurs'    => 'Décideurs',
                'etudiants'    => 'Étudiants',
                'autre'        => 'Autre',
            ],
            'zone' => [
                'plateau'       => 'Plateau',
                'cocody'        => 'Cocody',
                'marcory'       => 'Marcory',
                'yopougon'      => 'Yopougon',
                'abobo'         => 'Abobo',
                'grand-abidjan' => 'Grand Abidjan',
                'interieur'     => 'Intérieur du pays',
                'national'      => 'National',
                'a-definir'     => 'À définir avec vos équipes',
            ],
            'periode' => [
                'asap'         => 'Dès que possible',
                'moins-1-mois' => "Dans moins d'un mois",
                '1-3-mois'     => 'Dans 1 à 3 mois',
                'plus-tard'    => 'Plus tard',
            ],
            'services' => [
                'affichage'       => 'Affichage publicitaire',
                'mobile'          => 'Communication mobile',
                'street'          => 'Street marketing',
                'brand-experience'=> 'Expérience de marque',
                'digital'         => 'Communication digitale',
                'reseaux-sociaux' => 'Réseaux sociaux',
                'audiovisuel'     => 'Production audiovisuelle',
                'creation'        => 'Création graphique',
                'strategie'       => 'Stratégie média',
                'conseil'         => 'Conseil',
                'conseille'       => 'Je souhaite être conseillé',
            ],
            'budget' => [
                'moins-500k'  => 'Moins de 500 000 FCFA',
                '500k-2m'     => '500 000 à 2 M FCFA',
                '2-5m'        => '2 à 5 M FCFA',
                '5-10m'       => '5 à 10 M FCFA',
                'plus-10m'    => 'Plus de 10 M FCFA',
                'ne-sais-pas' => 'Je ne sais pas encore',
            ],
            'provenance' => [
                'google'         => 'Google',
                'linkedin'       => 'LinkedIn',
                'facebook'       => 'Facebook',
                'instagram'      => 'Instagram',
                'recommandation' => 'Recommandation',
                'client'         => 'Client',
                'evenement'      => 'Événement',
                'panneau'        => 'Panneau CIBLE',
                'autre'          => 'Autre',
            ],
        ];
    }

    /** Les 4 documents facultatifs joignables au brief (champ => libellé). */
    public const DOCUMENTS = [
        'doc_brief'   => 'Brief',
        'doc_logo'    => 'Logo',
        'doc_charte'  => 'Charte graphique',
        'doc_cahier'  => 'Cahier des charges',
    ];

    /**
     * Formats acceptés pour les 4 documents, et taille max par fichier (Ko).
     *
     * On valide l'extension (`extensions:`) et non le type deviné du
     * contenu (`mimes:`) : un .ai est un PDF déguisé et un .eps est vu
     * comme du PostScript, si bien que `mimes:` rejetterait des fichiers
     * de création parfaitement légitimes. Ces documents ne sont ni
     * stockés ni servis par le site — ils partent en pièce jointe et
     * sont analysés par l'antivirus de la boîte destinataire.
     */
    private const DOC_EXT     = 'pdf,jpg,jpeg,png,gif,webp,ai,eps,svg,zip,doc,docx,ppt,pptx';
    private const DOC_MAX_KO  = 10240; // 10 Mo

    /**
     * Réception du formulaire /contact — mini-brief marketing.
     *
     * Les documents éventuels sont joints au mail depuis leur emplacement
     * temporaire puis abandonnés à la fin de la requête : rien n'est
     * conservé sur le serveur (pas de rétention à gérer, pas de RGPD
     * supplémentaire côté stockage).
     */
    public function submitDevis(Request $request)
    {
        $options = self::formOptions();

        // Chaque case cochée doit appartenir à la liste d'options servie
        // par la vue — sinon un POST forgé pourrait injecter du texte
        // arbitraire dans le mail envoyé au commercial.
        $regleItem = fn (string $cle) => ['string', Rule::in(array_keys($options[$cle]))];

        $data = $request->validate([
            'nom'          => ['required', 'string', 'max:100'],
            'entreprise'   => ['required', 'string', 'max:150'],
            'poste'        => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:150'],
            'tel'          => ['required', 'string', 'max:30'],

            'objectif'     => ['nullable', 'array'],
            'objectif.*'   => $regleItem('objectif'),
            'cible'        => ['nullable', 'array'],
            'cible.*'      => $regleItem('cible'),
            'zone'         => ['nullable', 'array'],
            'zone.*'       => $regleItem('zone'),
            'services'     => ['nullable', 'array'],
            'services.*'   => $regleItem('services'),

            'periode'      => ['nullable', Rule::in(array_keys($options['periode']))],
            'budget'       => ['nullable', Rule::in(array_keys($options['budget']))],
            'provenance'   => ['nullable', Rule::in(array_keys($options['provenance']))],

            'message'      => ['nullable', 'string', 'max:4000'],
            'consentement' => ['accepted'],
            // Pots de miel : invisibles pour un humain, remplis par les robots
            // qui parcourent le DOM. Deux champs aux noms plausibles valent
            // mieux qu'un seul, les robots les plus soignés ignorant "website".
            'website'      => ['nullable', 'string', 'max:0'],
            'company_url'  => ['nullable', 'string', 'max:0'],
            '_ts'          => ['nullable', 'string', 'max:600'],  // jeton d'ouverture chiffre

            'doc_brief'    => ['nullable', 'file', 'extensions:' . self::DOC_EXT, 'max:' . self::DOC_MAX_KO],
            'doc_logo'     => ['nullable', 'file', 'extensions:' . self::DOC_EXT, 'max:' . self::DOC_MAX_KO],
            'doc_charte'   => ['nullable', 'file', 'extensions:' . self::DOC_EXT, 'max:' . self::DOC_MAX_KO],
            'doc_cahier'   => ['nullable', 'file', 'extensions:' . self::DOC_EXT, 'max:' . self::DOC_MAX_KO],
        ], [
            'website.max'          => 'Champ invalide.',
            'consentement.accepted'=> 'Merci d\'accepter d\'être recontacté pour que nous puissions traiter votre demande.',
        ]);

        // Cloudflare Turnstile, si les clés sont renseignées. Placé avant
        // l'analyse maison : inutile d'aller plus loin si le défi a échoué.
        if (!Turnstile::verifier($request->input('cf-turnstile-response'), $request->ip())) {
            return back()->withInput()->withErrors([
                'turnstile' => 'La vérification anti-robot a échoué. Merci de réessayer.',
            ]);
        }

        // Analyse anti-robot : addition de signaux faibles plutôt qu'une
        // règle unique et cassante (cf. App\Support\AntiSpam).
        $verdict = AntiSpam::analyser($request, $data);

        if ($verdict['score'] >= AntiSpam::SEUIL) {
            // On affiche le message de succès habituel : signaler le rejet
            // apprendrait au robot quel signal l'a trahi, et lui permettrait
            // d'itérer jusqu'à passer.
            Log::warning('cible.devis.robot_ecarte', [
                'score'   => $verdict['score'],
                'signaux' => $verdict['signaux'],
                'ip'      => $request->ip(),
                'nom'     => $data['nom'] ?? null,
                'email'   => $data['email'] ?? null,
            ]);

            return back()->with('devis_sent', true);
        }

        // Envoi accepté mais suspect : on trace pour pouvoir régler le seuil
        // sur des cas réels plutôt qu'au jugé.
        if ($verdict['score'] > 0) {
            Log::info('cible.devis.signaux_mineurs', [
                'score' => $verdict['score'], 'signaux' => $verdict['signaux'],
            ]);
        }

        // Slugs → libellés lisibles pour le mail du commercial.
        $lisible = [];
        foreach (['objectif', 'cible', 'zone', 'services'] as $cle) {
            $lisible[$cle] = array_map(
                fn ($v) => $options[$cle][$v],
                $data[$cle] ?? []
            );
        }
        foreach (['periode', 'budget', 'provenance'] as $cle) {
            $lisible[$cle] = isset($data[$cle]) ? $options[$cle][$data[$cle]] : null;
        }

        $fichiers = [];
        foreach (self::DOCUMENTS as $champ => $label) {
            if ($request->hasFile($champ)) {
                $fichiers[] = ['label' => $label, 'file' => $request->file($champ)];
            }
        }

        try {
            Mail::to(config('mail.cible_devis_to', 'commercial@cible-ci.com'))
                ->send(new CibleContactMail([
                    'nom'         => $data['nom'],
                    'entreprise'  => $data['entreprise'],
                    'poste'       => $data['poste'] ?? null,
                    'email'       => $data['email'],
                    'tel'         => $data['tel'],
                    'message'     => $data['message'] ?? null,
                    ...$lisible,
                    'ip'          => $request->ip(),
                    'ua'          => substr((string) $request->userAgent(), 0, 200),
                    'received_at' => now()->format('d/m/Y H:i'),
                ], $fichiers));

            Log::info('cible.devis.sent', [
                'nom' => $data['nom'], 'entreprise' => $data['entreprise'],
                'email' => $data['email'], 'ip' => $request->ip(),
                'documents' => count($fichiers),
            ]);
        } catch (\Throwable $e) {
            Log::error('cible.devis.mail_failed', [
                'error' => $e->getMessage(),
                // Les fichiers ne sont pas journalisés (volume + données client).
                'data'  => Arr::except($data, array_keys(self::DOCUMENTS)),
            ]);
            return back()->withInput()->with('devis_error',
                'Une erreur est survenue lors de l\'envoi de votre demande. Merci de réessayer ou de nous contacter directement au +225 07 00 78 06 28.'
            );
        }

        return back()->with('devis_sent', true);
    }
}
