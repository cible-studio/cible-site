<?php

namespace App\Http\Controllers;

use App\Support\Contenu;

/**
 * Maquette de refonte — servie sous /v2, branche `refonte-v2`.
 *
 * Volontairement séparée de CibleController : la V1 reste intacte et en
 * production pendant toute la refonte. Aucun fichier de la V1 n'est
 * modifié, donc aucun risque sur le site vivant.
 *
 * Les pages lisent le MÊME contenu éditable que la V1 (config/admin-schema
 * et config/contenu via App\Support\Contenu). Deux conséquences utiles :
 * la maquette montre les vrais textes de CIBLE et non du remplissage, et
 * l'espace d'administration construit en août pilote déjà la refonte —
 * il n'y aura rien à recâbler au basculement.
 *
 * Les routes répondent 404 si APP_ENV est en production ET que
 * CIBLE_V2_OUVERT n'est pas activé : une maquette n'a pas à être
 * atteignable par un visiteur du site public tant qu'elle n'est pas validée.
 */
class V2Controller extends Controller
{
    /**
     * Garde-fou : en production, la maquette n'existe pas tant qu'on ne
     * l'ouvre pas explicitement. 404 et non 403 — un visiteur n'a aucune
     * raison d'apprendre qu'il y a un chantier derrière cette adresse.
     */
    public function __construct()
    {
        abort_if(
            app()->isProduction() && !filter_var(env('CIBLE_V2_OUVERT', false), FILTER_VALIDATE_BOOL),
            404
        );
    }

    public function accueil()
    {
        return view('v2.accueil', $this->commun());
    }

    public function expertises()
    {
        return view('v2.expertises', $this->commun());
    }

    public function reseau()
    {
        return view('v2.reseau', $this->commun());
    }

    public function travaux()
    {
        return view('v2.travaux', $this->commun());
    }

    public function contact()
    {
        return view('v2.contact', $this->commun());
    }

    /**
     * Titres courts des réalisations — PROPOSITION À CORRIGER.
     *
     * Les titres de la V1 sont descriptifs et interchangeables entre les
     * six clients (« Faire vivre la marque au plus près de ses publics »,
     * « Traduire une vision institutionnelle en récit de marque »). Une
     * galerie de travaux demande des titres qui donnent envie d'ouvrir,
     * comme le fait la référence citée par le client : « FOR PAPA »,
     * « INCLUSIVE BY DESIGN ».
     *
     * ⚠ Ceux-ci sont dérivés STRICTEMENT des textes déjà présents dans
     * config/contenu.php : aucune ville, aucun chiffre, aucune durée, aucun
     * dispositif n'a été ajouté, faute de connaître le terrain. Ils sont donc
     * justes mais encore abstraits. Un titre nourri d'un fait réel les
     * battra tous — c'est le sens de la demande faite au client.
     *
     * Ils vivent ici, et non dans config/contenu.php, pour deux raisons :
     * la V1 est en production et son champ `titre` ne doit pas changer, et
     * un emplacement unique et commenté est plus simple à corriger. Au
     * basculement, ils rejoindront le schéma pour devenir éditables depuis
     * l'admin.
     */
    public const TITRES_COURTS = [
        'orange'    => 'Aller chercher les gens',
        'cofina'    => "Ce qu'une institution a à dire",
        'snedai'    => 'Même voix, partout',
        'sgs-sicta' => 'Tenir la parole en ligne',
        'ifg'       => "Un stand qu'on n'évite pas",
        'sigfu'     => 'Donner un corps à une présence',
    ];

    /**
     * Les quatre pôles d'expertise — contenu fourni par le client le
     * 2026-10-08, complété pour les pôles 3 et 4.
     *
     * Le client a donné la nomenclature réelle (OOH · Digital · Marketing
     * opérationnel · Media Intelligence) et le texte intégral des pôles 1 et
     * 2. Les pôles 3 et 4 sont écrits ici dans la même structure et la même
     * voix — phrases courtes, verbes d'action, aucun superlatif — et restent
     * à valider.
     *
     * Ce contenu vit dans le controller et NON dans config/admin-schema.php :
     * la V1 est en production et lit ce schéma pour ses propres pages
     * Services. Au basculement, il rejoindra le schéma pour devenir éditable
     * depuis l'admin.
     *
     * Structure voulue par le client, identique pour les quatre :
     * accroche → introduction → ce que nous faisons → comment nous
     * travaillons → notre différence → appel à l'action.
     */
    public const POLES = [
        [
            'id' => 'ooh', 'num' => '01', 'nom' => 'OOH',
            'couleur' => 'var(--rouge)',
            'visuel' => 'test/rue-afrique',
            'accroche' => "Donner de la visibilité aux marques dans l'espace public.",
            'intro' => "L'espace public est le seul média qu'on ne peut pas zapper. Nous concevons des campagnes qui se voient, se comprennent en trois secondes et se retiennent.",
            'faisons' => [
                ['Affichage grand format et mobilier urbain', 'De la couverture, de l\'impact.'],
                ['DOOH', "Des messages adaptés à l'heure, au lieu, à l'audience."],
                ['Transports et gares', 'Votre marque là où se passent les trajets quotidiens.'],
                ['Dispositifs spéciaux et street marketing', 'Habillages, installations, activations.'],
                ['Création des visuels', 'Un message, un format, une lecture immédiate.'],
            ],
            'methode' => [
                ['Cibler', 'Flux, zones, audiences.'],
                ['Concevoir', 'Une création taillée pour le format.'],
                ['Déployer', 'Achat, planning, suivi de pose.'],
                ['Mesurer', 'Bilan chiffré et recommandations.'],
            ],
            'difference' => "Nous ne vendons pas des faces d'affichage. Nous construisons des campagnes qui s'articulent avec le reste de votre communication.",
            'cta' => 'Lancez votre campagne',
        ],
        [
            'id' => 'digital', 'num' => '02', 'nom' => 'Digital — Stratégie &amp; Création',
            'couleur' => 'var(--jaune)', 'couleur_texte' => 'var(--rouge)',
            'visuel' => 'test/mobile',
            'accroche' => 'Construire des marques qui émergent dans les conversations digitales.',
            'intro' => "Une marque qui diffuse ne suffit plus. Elle doit se faire entendre et donner envie de répondre. Nous définissons votre stratégie, puis nous créons ce qui la rend visible.",
            'faisons' => [
                ['Stratégie de marque et de contenu', 'Positionnement, ton, territoires.'],
                ['Social media', 'Plateformes, lignes éditoriales, animation de communauté.'],
                ['Création de contenus', 'Visuels, vidéo, formats courts, motion.'],
                ['Campagnes digitales', 'Des idées pensées pour le mobile.'],
                ['Sites et expériences web', 'Design, ergonomie, parcours.'],
                ['Influence', 'Des partenariats alignés avec vos valeurs.'],
            ],
            'methode' => [
                ['Auditer', 'Votre présence, votre audience, votre marché.'],
                ['Décider', 'Une stratégie claire, des objectifs mesurables.'],
                ['Créer', 'Des contenus qui provoquent une réaction.'],
                ['Ajuster', 'On regarde les résultats et on corrige.'],
            ],
            'difference' => "Stratégie et création sont dans la même équipe, dès le premier jour. Pas d'idée sans objectif, pas d'objectif sans idée forte.",
            'cta' => 'Parlons de votre marque',
        ],
        [
            // ⚠ Pôle écrit par nous, à valider par le client.
            'id' => 'operationnel', 'num' => '03', 'nom' => 'Marketing opérationnel',
            'couleur' => 'var(--violet)',
            'visuel' => 'test/foule-festive',
            'accroche' => 'Aller au contact, là où la décision se prend.',
            'intro' => "Une campagne qui reste à distance ne convertit pas. Le marketing opérationnel met votre marque à portée de main : on se place sur le trajet des gens, on leur parle, on leur fait essayer.",
            'faisons' => [
                ['Activation terrain et street marketing', 'Votre marque là où circule votre audience.'],
                ['Animation en point de vente', 'Au dernier mètre, quand le choix se fait.'],
                ['Roadshows et caravanes', 'Une campagne itinérante, commune par commune.'],
                ['Communication mobile', 'Camions, motos, véhicules habillés, chevalets.'],
                ['Événements et stands', 'Un espace conçu pour faire entrer et faire rester.'],
                ['Équipes terrain', 'Recrutement et formation de ceux qui portent votre message.'],
            ],
            'methode' => [
                ['Repérer', 'Les lieux et les moments où votre cible est disponible.'],
                ['Scénariser', 'Un dispositif, un rôle pour chaque équipier, un message.'],
                ['Déployer', 'Logistique, matériel, équipes, autorisations.'],
                ['Rendre compte', 'Fréquentation, contacts, photos horodatées.'],
            ],
            'difference' => "Nos équipes sont formées par nous, pas louées à la journée. Ce qu'elles disent de votre marque, nous en répondons.",
            'cta' => 'Organisons une activation',
        ],
        [
            // ⚠ Pôle écrit par nous, à valider par le client.
            'id' => 'intelligence', 'num' => '04', 'nom' => 'Media Intelligence',
            'couleur' => 'var(--bleu)',
            'visuel' => 'test/architecture',
            'accroche' => 'Piloter une campagne, pas seulement la diffuser.',
            'intro' => "Un plan média ne vaut que par ce qu'on peut en vérifier. Nous instrumentons vos campagnes pour que chaque décision s'appuie sur une donnée, et chaque pose sur une preuve.",
            'faisons' => [
                ['Plan média chiffré', 'Emplacements, formats, périodes, couverture estimée.'],
                ['Pige photo horodatée et géolocalisée', 'La preuve de chaque face posée.'],
                ["Suivi d'exécution", 'Ce qui est posé, ce qui reste à poser, au jour près.'],
                ['Bilan de campagne', "Ce qui a fonctionné, ce qu'il faut corriger."],
                ['Veille concurrentielle', 'Qui parle, où, à quel rythme.'],
                ['Tableaux de bord', 'Vos chiffres, consultables quand vous voulez.'],
            ],
            'methode' => [
                ['Instrumenter', "Définir ce qu'on va mesurer, et comment."],
                ['Collecter', 'Relevés terrain, photos, dates, coordonnées.'],
                ['Lire', 'Transformer les relevés en décisions.'],
                ['Capitaliser', "La campagne suivante part de ce qu'on a appris."],
            ],
            'difference' => "Nous apportons la preuve avant qu'on nous la demande. Chaque face posée est photographiée, datée et localisée.",
            'cta' => 'Demander un plan média',
        ],
    ];

    /**
     * Visuels de TEST pour la revue — placeholders Unsplash.
     *
     * Autorisés explicitement par le client pour la maquette. Ils tiennent
     * la place des prises de vue que la charte demande et que CIBLE n'a pas
     * encore : « des personnages humains qui sont en joie », des images de
     * liberté, le perroquet. Les visuels d'origine du dépôt sont des
     * créations publicitaires de CIBLE et des constats de pose — utiles,
     * mais pas des photographies de campagne.
     *
     * ⚠ À REMPLACER avant toute mise en ligne publique. On surcharge ici la
     * copie locale du tableau, sans jamais toucher config/contenu.php : la
     * V1 en production continue d'afficher ses propres visuels.
     */
    public const VISUELS_TEST = [
        'orange'    => 'refonte/test/foule-festive.webp',   // activation terrain
        'cofina'    => 'refonte/test/studio-lumiere.webp',  // production audiovisuelle
        'snedai'    => 'refonte/test/rue-afrique.webp',     // présence multi-supports
        'sgs-sicta' => 'refonte/test/mobile.webp',          // digital et réseaux
        'ifg'       => 'refonte/test/stand.webp',           // stand expérientiel
        'sigfu'     => 'refonte/test/architecture.webp',    // design événementiel
    ];

    /**
     * Données partagées : les réalisations viennent de la source unique
     * (Contenu), jamais dupliquées dans une vue. On y greffe seulement le
     * titre court et le visuel de test, sans écraser le titre d'origine —
     * la page de détail continue de l'afficher.
     */
    private function commun(): array
    {
        $realisations = Contenu::section('realisations');

        foreach ($realisations as $slug => &$projet) {
            $projet['titre_court'] = self::TITRES_COURTS[$slug] ?? ($projet['titre'] ?? '');

            if (isset(self::VISUELS_TEST[$slug])) {
                $projet['image'] = self::VISUELS_TEST[$slug];
            }
        }
        unset($projet);

        return [
            'realisations' => $realisations,
            'poles'        => self::POLES,
            'options'      => CibleController::formOptions(),
        ];
    }
}
