<?php

namespace App\Http\Controllers;

use App\Mail\CibleContactMail;
use App\Support\AntiSpam;
use App\Support\Contenu;
use App\Support\Journal;
use App\Support\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
     * Confirmation d'envoi du formulaire — /contact/merci
     *
     * Même page que /contact (le bloc « demande reçue » s'affiche à la
     * place du formulaire), mais à une URL distincte : c'est ce qui permet à
     * GTM de compter une conversion sur une simple « Page vue ».
     *
     * N'est servie que juste après un envoi (flash `devis_sent`). Un accès
     * direct, un favori ou un rafraîchissement renvoient au formulaire :
     * sinon chacun compterait une fausse demande dans Analytics et Google Ads.
     */
    public function merci()
    {
        if (!session('devis_sent')) {
            return redirect()->route('contact');
        }

        return view('contact', $this->baseData('contact') + ['noindex' => true]);
    }

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
     * Ce qu'un document de brief ne peut en aucun cas être, quelle que soit
     * son extension — cf. contenuDangereux(). Liste de refus et non liste
     * d'acceptation : on ne veut pas revivre le rejet des .ai / .eps.
     */
    private const SIGNATURES_BINAIRES = [
        "MZ"               => 'executable-windows',
        "\x7FELF"          => 'executable-linux',
        "\xCF\xFA\xED\xFE" => 'executable-mac',
        "\xCA\xFE\xBA\xBE" => 'executable-mac-ou-java',
        "#!"               => 'script',
    ];
    private const SIGNATURES_TEXTE = [
        '<?php'          => 'php',
        '<!doctype html' => 'page-web',
        '<html'          => 'page-web',
        '<script'        => 'script',
        '<hta:'          => 'application-hta',
    ];
    private const TYPES_INTERDITS = [
        'application/x-dosexec', 'application/vnd.microsoft.portable-executable',
        'application/x-msdownload', 'application/x-executable', 'application/x-pie-executable',
        'application/x-sharedlib', 'application/x-mach-binary', 'application/x-elf',
        'application/x-msi', 'application/x-ms-shortcut', 'application/java-archive',
        'text/html', 'application/xhtml+xml', 'text/x-php', 'application/x-php',
        'text/x-shellscript', 'application/x-shellscript', 'text/javascript', 'application/javascript',
    ];
    /** Fichiers exécutables qu'une archive .zip ne doit pas contenir. */
    private const EXT_EXECUTABLES = 'exe|com|scr|bat|cmd|pif|msi|msp|dll|cpl|vbs|vbe|js|jse|wsf|wsh|hta|ps1|psm1|lnk|jar|apk|sh|php|reg|iso|img|docm|xlsm|pptm';

    /**
     * Analyse le contenu réel d'un document déposé. Renvoie la raison du
     * refus, ou null s'il est acceptable.
     *
     * Complète `extensions:` sans le remplacer. Ces fichiers partent en pièce
     * jointe chez le commercial : un « brief.pdf » qui est en réalité un
     * exécutable, une page web ou un SVG porteur de JavaScript est l'attaque
     * classique contre un formulaire de contact. Le reste (PDF piégés,
     * macros…) relève de l'antivirus de la boîte destinataire.
     */
    private static function contenuDangereux(UploadedFile $f): ?string
    {
        $chemin = $f->getRealPath();
        if ($chemin === false || !is_readable($chemin)) {
            return 'illisible';
        }

        // 1. Signature : les premiers octets ne mentent pas, l'extension si.
        $debut = (string) file_get_contents($chemin, false, null, 0, 1024);
        foreach (self::SIGNATURES_BINAIRES as $sig => $nature) {
            if (str_starts_with($debut, $sig)) {
                return $nature;
            }
        }
        $tete = ltrim($debut, "\xEF\xBB\xBF \t\r\n");   // BOM UTF-8 et blancs
        foreach (self::SIGNATURES_TEXTE as $sig => $nature) {
            if (stripos($tete, $sig) === 0) {
                return $nature;
            }
        }

        // 2. Type déduit du contenu (libmagic).
        $mime = (string) $f->getMimeType();
        if (in_array($mime, self::TYPES_INTERDITS, true)) {
            return "type:$mime";
        }

        $ext = strtolower($f->getClientOriginalExtension());

        // 3. SVG : du XML qui peut embarquer du JavaScript, exécuté si le
        //    fichier est ouvert dans un navigateur.
        if ($ext === 'svg' || $mime === 'image/svg+xml') {
            $svg = (string) file_get_contents($chemin);
            if (preg_match('#<script|javascript:|\son[a-z]+\s*=|<foreignObject|<iframe|<embed|<object#i', $svg)) {
                return 'svg-actif';
            }
        }

        // 4. Archive : un exécutable glissé parmi les fichiers.
        if ($ext === 'zip' && class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($chemin) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $nom = (string) $zip->getNameIndex($i);
                    if (preg_match('#\.(' . self::EXT_EXECUTABLES . ')$#i', $nom)) {
                        $zip->close();
                        return 'zip-executable';
                    }
                }
                $zip->close();
            }
        }

        return null;
    }

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

        // Champs d'une seule ligne : ni retour à la ligne ni caractère de
        // contrôle (la tabulation reste tolérée, copier-coller oblige). Nom
        // et entreprise composent l'objet du mail ; Symfony Mailer encode
        // déjà les en-têtes, c'est une seconde barrière.
        $uneLigne = 'not_regex:/[\x00-\x08\x0A-\x1F\x7F]/';

        // Contenu réel des documents joints — cf. contenuDangereux().
        $documentSur = function (string $champ, mixed $fichier, \Closure $echec) use ($request) {
            if (!$fichier instanceof UploadedFile) {
                return;
            }
            $raison = self::contenuDangereux($fichier);
            if ($raison !== null) {
                // Tracé pour repérer un éventuel faux positif sur un vrai brief.
                Journal::avertir('cible.devis.document_refuse', [
                    'raison'  => $raison,
                    'fichier' => mb_substr($fichier->getClientOriginalName(), 0, 120),
                    'ip'      => $request->ip(),
                ]);
                $echec('Ce fichier a été refusé par sécurité. Merci d\'envoyer un PDF, une image ou un document Office.');
            }
        };

        $data = $request->validate([
            'nom'          => ['required', 'string', 'max:100', $uneLigne],
            'entreprise'   => ['required', 'string', 'max:150', $uneLigne],
            'poste'        => ['required', 'string', 'max:100', $uneLigne],
            'email'        => ['required', 'email', 'max:150'],
            'tel'          => ['required', 'string', 'max:30', $uneLigne],

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

            'doc_brief'    => ['nullable', 'file', 'extensions:' . self::DOC_EXT, 'max:' . self::DOC_MAX_KO, $documentSur],
            'doc_logo'     => ['nullable', 'file', 'extensions:' . self::DOC_EXT, 'max:' . self::DOC_MAX_KO, $documentSur],
            'doc_charte'   => ['nullable', 'file', 'extensions:' . self::DOC_EXT, 'max:' . self::DOC_MAX_KO, $documentSur],
            'doc_cahier'   => ['nullable', 'file', 'extensions:' . self::DOC_EXT, 'max:' . self::DOC_MAX_KO, $documentSur],
        ], [
            'website.max'          => 'Champ invalide.',
            'not_regex'            => 'Ce champ doit tenir sur une seule ligne.',
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
            Journal::avertir('cible.devis.robot_ecarte', [
                'score'   => $verdict['score'],
                'signaux' => $verdict['signaux'],
                'ip'      => $request->ip(),
                'nom'     => $data['nom'] ?? null,
                'email'   => $data['email'] ?? null,
            ]);

            // Même destination qu'un envoi réussi, pour la même raison. Sans
            // JavaScript, le robot ne déclenche pas GTM : pas de fausse
            // conversion.
            return redirect()->route('contact.merci')->with('devis_sent', true);
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
            // Tableau d'adresses (cf. config/mail.php) ; filet si la variable
            // a été vidée par erreur, pour ne jamais envoyer à personne.
            Mail::to(config('mail.cible_devis_to') ?: ['commercial@cible-ci.com'])
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
            Journal::erreur('cible.devis.mail_failed', [
                'error' => $e->getMessage(),
                // Les fichiers ne sont pas journalisés (volume + données client).
                'data'  => Arr::except($data, array_keys(self::DOCUMENTS)),
            ]);
            return back()->withInput()->with('devis_error',
                'Une erreur est survenue lors de l\'envoi de votre demande. Merci de réessayer ou de nous contacter directement au +225 07 98 49 66 74.'
            );
        }

        // URL distincte de /contact : c'est elle que GTM compte comme une
        // demande envoyée. Les erreurs, elles, restent sur /contact (back()).
        return redirect()->route('contact.merci')->with('devis_sent', true);
    }
}
