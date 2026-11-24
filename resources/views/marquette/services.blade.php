{{-- ═══════════════════════════════════════════════════════════════════
     Maquette — NOS SERVICES. Message de la page : CIBLE ne vend plus
     seulement des panneaux. Les 4 pôles ont donc le même traitement
     (même gabarit, même place), seule leur couleur change.

     Textes : Contenu::get('services.*') (espace admin), plus les listes
     écrites en dur dans la vue actuelle resources/views/services.blade.php
     (formats, dispositifs mobiles, offres, briques, parcours, méthode),
     reprises mot pour mot.

     Ancres #regie #mobile #experience #media-intelligence : visées par le
     sous-menu et le pied de page du layout — ne pas les renommer.
     Réalisations absentes (visuels et informations clients à fournir).
════════════════════════════════════════════════════════════════════ --}}
@extends('marquette.layout')
@use('App\Support\Contenu')
@use('Illuminate\Support\Str')

@section('titre', 'Nos services — CIBLE · Régie, mobile, brand experience, Media Intelligence')
@section('description', 'Quatre pôles complémentaires : régie publicitaire (+400 panneaux, 6 formats), communication mobile, brand experience & communication 360°, Media Intelligence et pilotage de campagne.')

@php
    $ch = Contenu::section('chiffres');
    $hero = \App\Support\Marquette::photo('bache');

    // Les 4 pôles : ancre, numéro, couleur, couleur du texte posé dessus
    // (noir sur le jaune, cf. charte), photo et son cadrage.
    $poles = collect([
        ['regie',              1, 'var(--rouge)',  'var(--blanc)', 'uber',   '18% 50%'],
        ['mobile',             2, 'var(--jaune)',  'var(--noir)',  'camion', '34% 50%'],
        ['experience',         3, 'var(--violet)', 'var(--blanc)', 'course', '50% 50%'],
        ['media-intelligence', 4, 'var(--bleu)',   'var(--blanc)', 'route',  '60% 50%'],
    ])->map(function ($p) {
        $tag = Contenu::get("services.p{$p[1]}_tag");
        // Nom court pour la navigation interne : « Pôle 01 · Régie publicitaire
        // & visibilité extérieure » → « Régie publicitaire ».
        $nom = trim(Str::before(trim(Str::after($tag, '·')), '&'));

        return [
            'ancre' => $p[0], 'n' => $p[1], 'num' => sprintf('%02d', $p[1]),
            'c' => $p[2], 'fg' => $p[3], 'photo' => $p[4], 'cadrage' => $p[5],
            'tag' => $tag, 'nom' => $nom,
        ];
    });

    // Pôle 1 — formats d'affichage (vue actuelle, lignes 201-208). Les
    // dimensions chiffrées servent au dessin à l'échelle : [longueur, hauteur]
    // en mètres ; la seconde, quand elle existe, est la borne haute de la
    // fourchette (tracée en pointillé).
    $formats = [
        ['Petit format',      '2×1m à 5×2m',  '2 → 10 m²',  [[2, 1], [5, 2]]],
        ['Classique',         '4×3m',         '12 m²',      [[4, 3]]],
        ['Grande dimension',  '6×3m à 6×4m',  '18 → 24 m²', [[6, 3], [6, 4]]],
        ['Grand format',      '8×4m à 6×6m',  '32 → 36 m²', [[8, 4], [6, 6]]],
        ['Très grand format', '10×5m à 9×6m', '50 → 54 m²', [[10, 5], [9, 6]]],
        ['Panoramique',       '14×5m',        '70 m²',      [[14, 5]]],
    ];

    // Pôles 2 à 4 — listes de la vue actuelle (lignes 251-256, 285-294, 326-333).
    $listes = [
        2 => [
            ['Camions publicitaires', 'Des surfaces d\'affichage mobiles à fort impact pour accompagner un lancement, annoncer une opération ou occuper plusieurs zones au cours d\'une même campagne.'],
            ['Motos publicitaires', 'Des dispositifs agiles pour circuler dans les zones denses, toucher les quartiers de proximité et accéder aux espaces difficiles à couvrir avec de grands véhicules.'],
            ['Branding véhicules', 'Habillage partiel ou intégral de véhicules d\'entreprise, de flottes commerciales ou de véhicules spécifiquement dédiés à une campagne.'],
            ['Branding taxis & cars', 'Une présence répétée dans les flux urbains pour renforcer la mémorisation et augmenter la fréquence d\'exposition au message.'],
            ['Chevalets et dispositifs de proximité', 'Des supports tactiques déployés autour des marchés, points de vente, événements et lieux de forte affluence.'],
            ['Roadshows et caravanes', 'Des campagnes itinérantes combinant visibilité mobile, animation, prise de parole et interaction directe avec les publics.'],
        ],
        // 8 offres. Le document source numérote deux fois « OFFRE.4 » et saute
        // « OFFRE.7 » : ordre du document, numérotation recalculée à l'écran.
        3 => [
            ['Création graphique & identité visuelle', 'Conception de campagnes, créations publicitaires, adaptations aux différents formats, supports print et contenus digitaux.'],
            ['Stratégie de communication', 'Définition du concept, des messages, des audiences, des canaux et du plan de déploiement selon vos objectifs de notoriété, d\'engagement ou de conversion.'],
            ['Street marketing', 'Distributions ciblées, animations commerciales, échantillonnage, campagnes de proximité et dispositifs d\'interaction avec les consommateurs.'],
            ['Digital & réseaux sociaux', 'Campagnes sociales, contenus, community management, activations digitales, amplification en ligne et dispositifs drive-to-store.'],
            ['Relations presse & influence', 'Coordination des prises de parole, relations avec les médias, dispositifs d\'influence et amplification des temps forts de la campagne.'],
            ['Production audiovisuelle', 'Films institutionnels, spots publicitaires, capsules digitales, contenus de marque, motion design et productions audio.'],
            ['Architecture événementielle', 'Conception et réalisation de stands, espaces de marque, corners, signalétique et dispositifs événementiels.'],
            ['Activations et expériences de marque', 'Pop-up stores, roadshows, stands expérientiels, lancements, dispositifs immersifs et animations permettant au public de découvrir, tester et vivre la marque.'],
        ],
        4 => [
            ['Inventaire média digitalisé', 'Visualisation et référencement des emplacements selon les communes, les axes, les formats, les disponibilités et les caractéristiques des supports.'],
            ['Sélection intelligente des emplacements', 'Recommandation des zones et dispositifs selon les objectifs de campagne, les audiences visées, les flux et les contraintes budgétaires.'],
            ['Suivi terrain digitalisé', 'Contrôle du déploiement grâce à des photos horodatées et géolocalisées, associées à chaque opération de pose.'],
            ['Reporting de campagne', 'Centralisation des preuves de diffusion, synthèse des emplacements activés et partage d\'un bilan clair aux équipes annonceurs.'],
            ['Espace client', 'Accès à une vue consolidée des campagnes en cours, des supports mobilisés et des éléments de suivi disponibles.'],
            ['Analyse et optimisation', 'Exploitation progressive des données de campagne pour améliorer les choix de zones, de formats, de périodes et de dispositifs.'],
        ],
    ];

    // Parcours phygital (vue actuelle, lignes 363-366).
    $parcours = [
        ['Attirer',   'Créer la visibilité et capter l\'attention grâce aux dispositifs outdoor, mobiles et digitaux.', 'var(--rouge)', 'var(--blanc)'],
        ['Engager',   'Faire participer les publics grâce aux activations, aux animations, aux contenus et aux expériences interactives.', 'var(--jaune)', 'var(--noir)'],
        ['Convertir', 'Générer du trafic, des prises de contact, des visites en point de vente, des essais ou des actions mesurables.', 'var(--vert)', 'var(--blanc)'],
        ['Amplifier', 'Prolonger l\'expérience en ligne grâce aux réseaux sociaux, aux contenus et aux dispositifs de partage.', 'var(--violet)', 'var(--blanc)'],
    ];

    // Méthode en 8 étapes (vue actuelle, lignes 392-399).
    $methode = [
        ['Cadrage',                   'Nous clarifions vos objectifs, vos cibles, vos zones prioritaires, votre calendrier et votre budget.', 'var(--rouge)'],
        ['Analyse',                   'Nous étudions les points de contact, les emplacements, les formats et les dispositifs les plus pertinents.', 'var(--jaune)'],
        ['Recommandation',            'Nous construisons une stratégie de visibilité et d\'activation adaptée à votre campagne.', 'var(--vert)'],
        ['Création',                  'Nous développons ou adaptons les visuels, les contenus et l\'expérience de marque.', 'var(--bleu)'],
        ['Planification',             'Nous organisons les équipes, les supports, les itinéraires, les délais et les opérations terrain.', 'var(--violet)'],
        ['Déploiement',               'Nos équipes assurent la pose, la diffusion, l\'animation et l\'exécution opérationnelle de la campagne.', 'var(--rouge)'],
        ['Contrôle',                  'Chaque opération est suivie et documentée par des preuves terrain horodatées et géolocalisées.', 'var(--jaune)'],
        ['Reporting & Optimisations', 'Nous partageons le bilan de la campagne et identifions les leviers d\'amélioration pour les prochaines activations.', 'var(--vert)'],
    ];

    // Cycle de 5 tons, comme la vue actuelle : supporte tout nombre de lignes
    // saisi dans l'admin. [couleur, couleur du pictogramme posé dessus]
    $tons = [
        ['var(--rouge)', 'var(--blanc)'], ['var(--jaune)', 'var(--noir)'], ['var(--vert)', 'var(--blanc)'],
        ['var(--bleu)', 'var(--blanc)'], ['var(--violet)', 'var(--blanc)'],
    ];
@endphp

@push('preload')
    <link rel="preload" as="image" imagesrcset="{{ $hero['srcset'] }}" imagesizes="(min-width: 960px) 24vw, 52vw" fetchpriority="high">
@endpush

@push('css')
    <link rel="stylesheet" href="{{ \App\Support\Marquette::asset('pages/services.css') }}">
@endpush

@push('js')
    <script src="{{ \App\Support\Marquette::asset('pages/services.js') }}" defer></script>
@endpush

@section('contenu')

{{-- Silhouette humaine (1,75 m) : repère d'échelle des formats d'affichage. --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <symbol id="sv-humain" viewBox="0 0 20 71">
        <circle cx="10" cy="6" r="5.5"/>
        <path d="M5 13h10a4 4 0 0 1 4 4v19a1.5 1.5 0 0 1-3 0V19h-.6v49a2.2 2.2 0 0 1-4.4 0V40H9v28a2.2 2.2 0 0 1-4.4 0V19H4v17a1.5 1.5 0 0 1-3 0V17a4 4 0 0 1 4-4z"/>
    </symbol>
</svg>

{{-- ═══ 1. HÉRO ═══ --}}
<section class="page-hero sv-hero" aria-labelledby="services-titre">
    <span class="decor decor--fleche decor--masque-mobile" aria-hidden="true" style="--c:var(--jaune);--o:.9;width:80px;top:2%;right:6%;transform:rotate(-14deg)"></span>
    <div class="wrap page-hero__grille">
        <div>
            <x-mq.fil :etapes="['Nos services']" />
            <span class="sur">{{ Contenu::get('services.hero_surtitre') }}</span>
            <h1 class="t1" id="services-titre">{{ Contenu::get('services.hero_titre_1') }} <em>{{ Contenu::get('services.hero_titre_2') }}</em></h1>
            <p class="chapo">{{ Contenu::get('services.hero_texte') }}</p>
            <p class="sv-accroche">{{ Contenu::get('services.hero_accroche') }}</p>
            <div class="actions">
                <a class="btn btn--rouge" href="{{ route('marquette.contact') }}">Recevoir ma recommandation média<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></a>
            </div>
            <ul class="reassurance">
                @foreach(array_slice(Contenu::lignes('contact.reass_liste'), 0, 2) as $r)
                    <li><svg class="ic" aria-hidden="true"><use href="#i-check"/></svg>{{ $r }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Trois photos dans la silhouette « panneau » de la charte :
             la visibilité (très grand format), l'expérience, le contenu. --}}
        <div class="sv-mosaique">
            <div class="cadre"><x-mq.photo cle="bache" sizes="(min-width: 960px) 24vw, 52vw" prioritaire style="object-position:50% 30%" /></div>
            <div class="cadre"><x-mq.photo cle="foule" sizes="(min-width: 960px) 22vw, 46vw" /></div>
            <div class="cadre"><x-mq.photo cle="tournage" sizes="(min-width: 960px) 22vw, 46vw" /></div>
        </div>
    </div>
</section>

{{-- La navigation interne reste collée sous l'en-tête tant qu'on lit
     l'approche et les 4 pôles, puis s'efface avec eux. --}}
<div class="sv-zone">

    {{-- ═══ NAVIGATION INTERNE — les 4 pôles ═══ --}}
    <nav class="sv-nav" aria-label="Les 4 pôles" data-sv-nav>
        <div class="wrap">
            <ul class="sv-nav__piste">
                @foreach($poles as $p)
                    <li>
                        <a class="sv-nav__lien" href="#{{ $p['ancre'] }}" style="--c:{{ $p['c'] }};--fg:{{ $p['fg'] }}">
                            <span class="sv-nav__num num" aria-hidden="true">{{ $p['num'] }}</span>{{ $p['nom'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>

    {{-- ═══ 2. NOTRE APPROCHE ═══ --}}
    <section class="section section--motif sv-approche-section" aria-labelledby="approche-titre">
        <span class="decor decor--plume decor--masque-mobile" aria-hidden="true" style="--c:var(--violet);--o:.1;width:150px;bottom:8%;left:2%;transform:rotate(14deg)"></span>
        <div class="wrap">
            <x-mq.tete :surtitre="Contenu::get('services.approche_surtitre')" :titre="Contenu::get('services.approche_titre')" :intro="Contenu::get('services.approche_texte')" centre id-titre="approche-titre" />

            <div class="sv-approche">
                @foreach([['var(--rouge)', 'var(--blanc)'], ['var(--jaune)', 'var(--noir)'], ['var(--bleu)', 'var(--blanc)']] as $i => [$c, $fg])
                    <article class="carte rv" style="--c:{{ $c }};--fg:{{ $fg }};--d:{{ $i * .08 }}s">
                        <div class="carte__corps">
                            <span class="sv-num num" aria-hidden="true">0{{ $i + 1 }}</span>
                            <h3 class="t3">{{ Contenu::get('services.approche_' . ($i + 1) . '_titre') }}</h3>
                            <p>{{ Contenu::get('services.approche_' . ($i + 1) . '_texte') }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ 3. LES 4 PÔLES — même gabarit, image alternée ═══ --}}
    @foreach($poles as $i => $p)
        @php
            $n = $p['n'];
            $accroche = Contenu::get("services.p{$n}_accroche");
        @endphp
        <section id="{{ $p['ancre'] }}" class="section sv-pole sv-pole--{{ $p['ancre'] }} {{ $i % 2 ? 'section--gris sv-pole--inverse' : '' }}"
                 style="--c:{{ $p['c'] }};--fg:{{ $p['fg'] }}" aria-labelledby="{{ $p['ancre'] }}-titre">
            <span class="decor decor--{{ $i % 2 ? 'plume' : 'fleche' }} decor--masque-mobile sv-pole__decor" aria-hidden="true"></span>
            <div class="wrap">
                <div class="grille-2">
                    <div class="rv">
                        <div class="sv-pole__marque">
                            <span class="sv-num sv-num--grand num" aria-hidden="true">{{ $p['num'] }}</span>
                            <span class="sv-pole__tag">{{ $p['tag'] }}</span>
                        </div>
                        <h2 class="t2 riche" id="{{ $p['ancre'] }}-titre">{!! Contenu::riche("services.p{$n}_titre") !!}</h2>
                        @if($n === 1)
                            <p class="chapo riche sv-pole__texte">{!! Contenu::riche('services.p1_texte') !!}</p>
                        @else
                            <p class="chapo sv-pole__texte">{{ Contenu::get("services.p{$n}_texte") }}</p>
                        @endif
                        @if($accroche)
                            <p class="sv-accroche">{{ $accroche }}</p>
                        @endif

                        @if($n === 1)
                            <div class="sv-dispositifs">
                                <h3 class="sv-label">Nos dispositifs d'affichage</h3>
                                <ul class="tags">
                                    @foreach(Contenu::lignes('services.p1_dispositifs') as $d)
                                        <li class="tag tag--point">{{ $d }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="sv-pole__visuel rv" style="--d:.1s">
                        <div class="cadre cadre--panneau">
                            <x-mq.photo :cle="$p['photo']" sizes="(min-width: 900px) 46vw, 100vw" style="object-position:{{ $p['cadrage'] }}" />
                        </div>
                        @if($n === 1)
                            <span class="pastille-flottante"><b class="num">{{ $ch['communes'] }}</b>Communes et villes couvertes</span>
                        @endif
                    </div>
                </div>

                @if($n === 1)
                    {{-- Formats dessinés à l'échelle : même échelle pour les 6,
                         la silhouette mesure 1,75 m. Le dessin est décoratif ;
                         nom, dimensions et surface restent écrits en clair. --}}
                    <div class="sv-pole__suite">
                        <div class="sv-sous-tete rv">
                            <h3 class="t3">Formats disponibles</h3>
                            <p>Du mobilier urbain de proximité aux dispositifs panoramiques, nous adaptons le format à votre message, à votre audience et à votre ambition de visibilité.</p>
                        </div>
                        <ul class="sv-formats">
                            @foreach($formats as $k => [$nom, $dim, $surface, $tailles])
                                <li class="sv-format rv" style="--d:{{ ($k % 3) * .06 }}s">
                                    <div class="sv-format__cadre" aria-hidden="true">
                                        <div class="sv-format__scene">
                                            <svg class="sv-format__humain"><use href="#sv-humain"/></svg>
                                            @foreach(array_reverse($tailles) as $t => [$l, $h])
                                                <span class="sv-format__rect {{ count($tailles) > 1 && $t === 0 ? 'sv-format__rect--max' : '' }}" style="--l:{{ $l }};--h:{{ $h }}"></span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="sv-format__corps">
                                        <h4>{{ $nom }}</h4>
                                        <p class="sv-format__dim num">{{ $dim }}</p>
                                        <p class="sv-format__surface num">{{ $surface }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <ol class="sv-items sv-items--{{ count($listes[$n]) % 4 === 0 ? 4 : 3 }} sv-pole__suite">
                        @foreach($listes[$n] as $k => [$nom, $texte])
                            <li class="carte rv" style="--d:{{ ($k % 4) * .06 }}s">
                                <div class="carte__corps">
                                    <span class="sv-num num" aria-hidden="true">{{ sprintf('%02d', $k + 1) }}</span>
                                    <h3>{{ $nom }}</h3>
                                    <p>{{ $texte }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </section>
    @endforeach

</div>

{{-- ═══ 4. CAMPAGNES PHYGITALES — un parcours en 4 temps ═══ --}}
<section class="section sv-phygital" aria-labelledby="phygital-titre">
    <span class="decor decor--fleche decor--masque-mobile" aria-hidden="true" style="--c:var(--vert);--o:.12;width:130px;top:9%;right:4%;transform:rotate(-10deg)"></span>
    <div class="wrap">
        <x-mq.tete :surtitre="Contenu::get('services.phy_surtitre')" :titre="Contenu::get('services.phy_titre')" :intro="Contenu::get('services.phy_texte')" centre id-titre="phygital-titre" />

        <ol class="sv-parcours">
            @foreach($parcours as $k => [$titre, $texte, $c, $fg])
                <li class="rv" style="--c:{{ $c }};--fg:{{ $fg }};--d:{{ $k * .08 }}s">
                    <span class="sv-parcours__n num" aria-hidden="true">0{{ $k + 1 }}</span>
                    @unless($loop->last)<svg class="ic sv-parcours__fleche" aria-hidden="true"><use href="#i-fleche"/></svg>@endunless
                    <div>
                        <h3 class="t3">{{ $titre }}</h3>
                        <p>{{ $texte }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ═══ 5. NOTRE MÉTHODE — 8 étapes ═══ --}}
<section class="section section--noir sv-methode-section" aria-labelledby="methode-titre">
    <span class="decor decor--plume decor--masque-mobile" aria-hidden="true" style="--c:var(--blanc);--o:.06;width:170px;top:7%;left:3%;transform:rotate(20deg)"></span>
    <div class="wrap">
        <div class="tete tete--centre rv">
            <span class="sur">{{ Contenu::get('services.work_surtitre') }}</span>
            <h2 class="t2" id="methode-titre">{{ Contenu::get('services.work_titre_1') }} <em>{{ Contenu::get('services.work_titre_2') }}</em></h2>
            <p class="chapo" style="margin-inline:auto">{{ Contenu::get('services.work_intro') }}</p>
        </div>

        <ol class="sv-methode">
            @foreach($methode as $k => [$titre, $texte, $c])
                <li class="rv" style="--c:{{ $c }};--d:{{ ($k % 4) * .06 }}s">
                    <span class="sv-methode__n num" aria-hidden="true">{{ sprintf('%02d', $k + 1) }}</span>
                    <h3>{{ $titre }}</h3>
                    <p>{{ $texte }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ═══ 6. POUR QUELS OBJECTIFS ═══ --}}
<section class="section section--motif" aria-labelledby="objectifs-titre">
    <div class="wrap">
        <x-mq.tete :surtitre="Contenu::get('services.obj_surtitre')" :titre="Contenu::get('services.obj_titre')" id-titre="objectifs-titre" />

        <ul class="sv-objectifs">
            @foreach(Contenu::lignes('services.obj_liste') as $k => $objectif)
                <li class="sv-objectif rv" style="--c:{{ $tons[$k % 5][0] }};--fg:{{ $tons[$k % 5][1] }};--d:{{ ($k % 4) * .06 }}s">
                    <span class="sv-objectif__rond" aria-hidden="true"><svg class="ic"><use href="#i-check"/></svg></span>
                    {{ $objectif }}
                </li>
            @endforeach
        </ul>
    </div>
</section>

{{-- ═══ 7. CONTACT ═══ --}}
<x-mq.bande-contact :surtitre="Contenu::get('services.cta_surtitre')" :titre="Contenu::get('services.cta_titre')" :texte="Contenu::get('services.cta_texte')" />

@endsection
