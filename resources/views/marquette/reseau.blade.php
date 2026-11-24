{{-- ═══════════════════════════════════════════════════════════════════
     Maquette — NOTRE RÉSEAU. Textes : Contenu::get('reseau.*') (espace
     admin) et, mot pour mot, les textes écrits en dur dans reseau.blade.php
     (cartes approche, carte, formats, Media Intelligence, exigence terrain).
       1. héro : la promesse, la preuve chiffrée, un accès direct à la carte ;
       2. approche média : la carte ne vaut que par la sélection ;
       3. carte interactive (Leaflet chargé à l'approche, pas au chargement :
          visiteurs en 3G) — JAMAIS de nombre de panneaux par commune ;
       4. couverture, formats, Media Intelligence, exigence terrain, contact.
     Section « Cas d'usage » de la page actuelle non reprise (page compacte).
     Styles : public/mq/pages/reseau.css · carte : public/mq/pages/reseau.js
════════════════════════════════════════════════════════════════════ --}}
@extends('marquette.layout')
@use('App\Support\Contenu')

@section('titre', 'Notre parc média — CIBLE · +400 panneaux dans 31 communes')
@section('description', 'Le parc publicitaire CIBLE : +400 panneaux exploités à Abidjan et dans les principales villes de Côte d\'Ivoire. Carte interactive, formats, couverture territoriale.')

@php
    $ch   = Contenu::section('chiffres');
    $co   = Contenu::section('coordonnees');
    $hero = \App\Support\Marquette::photo('route');

    $abidjan  = Contenu::lignes('reseau.comm_abidjan_liste');
    $interieur = Contenu::lignes('reseau.comm_int_liste');

    // Textes en dur de la page actuelle (reseau.blade.php), repris tels quels.
    $approche = [
        ['Vos objectifs',    'Notoriété, lancement de produit, couverture nationale, proximité commerciale ou génération de trafic.', 'var(--rouge)'],
        ['Vos audiences',    'Nous identifions les zones de présence, de déplacement et de concentration des publics que vous souhaitez atteindre.', 'var(--jaune)'],
        ['Votre dispositif', 'Nous construisons une sélection cohérente d\'emplacements, de formats et de périodes en fonction de votre budget.', 'var(--bleu)'],
    ];
    // Pictogramme : un panneau dessiné à l'échelle du format (largeur, hauteur, pied).
    $formats = [
        ['Affichage classique',              'Des formats efficaces pour construire une couverture régulière et répétée sur les principaux axes.',                             'var(--rouge)',  'classique'],
        ['Grands et très grands formats',    'Des dispositifs à fort impact visuel pour les lancements, les prises de parole institutionnelles et les campagnes de notoriété.', 'var(--jaune)',  'grand'],
        ['Dispositifs lumineux et digitaux', 'Des supports conçus pour renforcer la visibilité du message, notamment en soirée et dans les zones à forte fréquentation.',        'var(--bleu)',   'lumineux'],
        ['Affichage de proximité',           'Des formats implantés près des commerces, des lieux de passage et des zones de consommation.',                                     'var(--violet)', 'proximite'],
    ];
    $intelligence = [
        ['Inventaire centralisé', 'Chaque support est référencé selon sa localisation, son format, ses caractéristiques et son statut d\'exploitation.', 'var(--jaune)'],
        ['Sélection média',       'Les emplacements sont proposés selon les zones ciblées, les objectifs de campagne et les contraintes budgétaires.',   'var(--vert)'],
        ['Suivi terrain',         'Les opérations de pose sont documentées par des photos horodatées et géolocalisées.',                                  'var(--bleu)'],
        ['Reporting',             'Les annonceurs disposent d\'une vue claire des supports activés et des preuves d\'exécution de leur campagne.',        'var(--rouge)'],
    ];
    $exigence = [
        ['Maîtrise opérationnelle',     'Nos équipes pilotent la planification, la pose, le contrôle et le suivi des dispositifs activés.'],
        ['Entretien du réseau',         'Nos supports font l\'objet de contrôles et d\'interventions afin de préserver la qualité de présentation des campagnes.'],
        ['Preuves de diffusion',        'Chaque campagne est documentée à l\'aide de photos terrain horodatées et géolocalisées.'],
        ['Recommandation personnalisée','Nous ne vous remettons pas simplement une liste de panneaux : nous construisons un dispositif correspondant à vos objectifs et à vos audiences.'],
    ];
@endphp

@push('preload')
    <link rel="preload" as="image" imagesrcset="{{ $hero['srcset'] }}" imagesizes="(min-width: 960px) 44vw, 100vw" fetchpriority="high">
@endpush

@push('css')
    <link rel="stylesheet" href="{{ \App\Support\Marquette::asset('pages/reseau.css') }}">
@endpush

@section('contenu')

{{-- ═══ 1. HÉRO ═══ --}}
<section class="page-hero rs-hero" aria-labelledby="page-titre">
    <span class="decor decor--fleche decor--masque-mobile" aria-hidden="true" style="--c:var(--jaune);--o:.9;width:72px;top:5%;left:50%;transform:rotate(24deg)"></span>
    <div class="wrap">
        <div class="page-hero__grille">
            <div>
                <x-mq.fil :etapes="['Notre réseau']" />
                <span class="sur">{{ Contenu::get('reseau.hero_surtitre') }}</span>
                <h1 class="t1" id="page-titre">{{ Contenu::get('reseau.hero_titre') }}</h1>
                <p class="chapo">{{ Contenu::get('reseau.hero_texte') }}</p>
                <div class="actions">
                    <a class="btn btn--rouge" href="{{ route('marquette.contact') }}">Recevoir ma recommandation média<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></a>
                    <a class="btn btn--ligne rs-vers-carte" href="#carte">{{ Contenu::get('reseau.hero_cta') }}<svg class="ic" aria-hidden="true"><use href="#i-bas"/></svg></a>
                </div>
            </div>
            <div class="rs-hero__visuel">
                <div class="cadre cadre--panneau">
                    <x-mq.photo cle="route" sizes="(min-width: 960px) 44vw, 100vw" prioritaire style="object-position:56% 50%" />
                </div>
                {{-- Répartition par zone jamais chiffrée (décision 2026-08-10) :
                     seuls les deux totaux officiels sont affichés. --}}
                <div class="chiffres rs-hero__chiffres">
                    <x-mq.chiffre :valeur="$ch['panneaux']" plus libelle="Panneaux exploités" />
                    <x-mq.chiffre :valeur="$ch['communes']" libelle="Communes et villes couvertes" />
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ 2. APPROCHE MÉDIA ═══ --}}
<section class="section section--motif" aria-labelledby="approche-titre">
    <span class="decor decor--plume decor--masque-mobile" aria-hidden="true" style="--c:var(--rouge);--o:.08;width:150px;bottom:8%;right:3%;transform:rotate(18deg)"></span>
    <div class="wrap">
        <div class="tete tete--ligne rv">
            <div style="display:grid;gap:18px">
                <span class="sur">{{ Contenu::get('reseau.app_surtitre') }}</span>
                <h2 class="t2" id="approche-titre">{{ Contenu::get('reseau.app_titre') }}</h2>
            </div>
            <p class="chapo">{{ Contenu::get('reseau.app_texte') }}</p>
        </div>

        <ol class="rs-etapes">
            @foreach($approche as $i => [$titre, $texte, $c])
                <li class="carte rv" style="--c:{{ $c }};--d:{{ $i * .08 }}s">
                    <div class="carte__corps">
                        <span class="rs-n num" @if($c === 'var(--jaune)') style="--t:var(--noir)" @endif>{{ sprintf('%02d', $i + 1) }}</span>
                        <h3 class="t3">{{ $titre }}</h3>
                        <p class="rs-texte">{{ $texte }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ═══ 3. CARTE INTERACTIVE ═══
     Données : /api/reseau-map (31 communes, lat/lng). Le champ `total` des
     données n'est JAMAIS affiché (décision 2026-08-10). Les listes de
     l'admin fixent l'ordre de priorité des étiquettes quand elles se
     chevauchent. --}}
<section class="section section--blanc rs-carte-section" id="carte" aria-labelledby="carte-titre">
    <div class="wrap">
        <div class="tete tete--ligne rv">
            <div style="display:grid;gap:18px">
                <span class="sur">Notre couverture</span>
                <h2 class="t2" id="carte-titre">Explorez les zones couvertes par CIBLE.</h2>
            </div>
            <p class="chapo">Consultez les principales villes et zones dans lesquelles notre parc publicitaire est déployé.</p>
        </div>

        <div class="rs-carte-bloc rv"
             data-carte
             data-api="{{ route('api.reseau-map') }}"
             data-contours="{{ \App\Support\Marquette::asset('pages/reseau-contours.json') }}"
             data-abidjan="{{ Contenu::get('reseau.comm_abidjan_titre') }}"
             data-ordre="{{ json_encode(array_merge($abidjan, $interieur), JSON_UNESCAPED_UNICODE) }}">

            {{-- Les deux zones servent à la fois de légende et de raccourci. --}}
            <div class="rs-zones-btn" role="group" aria-label="Zones du réseau">
                <button class="rs-zone-btn" type="button" data-zone="abidjan" aria-pressed="false" aria-controls="rs-carte">
                    <i style="--c:var(--rouge)" aria-hidden="true"></i>{{ Contenu::get('reseau.comm_abidjan_titre') }}
                </button>
                <button class="rs-zone-btn" type="button" data-zone="interieur" aria-pressed="false" aria-controls="rs-carte">
                    <i style="--c:var(--jaune)" aria-hidden="true"></i>{{ Contenu::get('reseau.comm_int_titre') }}
                </button>
            </div>

            <div class="rs-carte">
                <div class="rs-carte__map" id="rs-carte" role="region" aria-label="Carte interactive du réseau CIBLE" aria-describedby="rs-carte-note"></div>
                <div class="rs-carte__etat" role="status">
                    <span data-etat="chargement"><span class="rs-spin" aria-hidden="true"></span>Chargement de la carte…</span>
                    <span data-etat="vide" hidden>Aucune donnée à afficher pour le moment.</span>
                    <span data-etat="erreur" hidden>Chargement de la carte impossible.<small>Notre équipe reste disponible : <a href="mailto:{{ $co['email_commercial'] }}">{{ $co['email_commercial'] }}</a></small></span>
                </div>
                {{-- Écran tactile : un doigt fait défiler la page, deux doigts
                     déplacent la carte (la page n'est jamais « piégée »). --}}
                <p class="rs-carte__aide" aria-hidden="true">Utilisez deux doigts pour déplacer la carte</p>
            </div>

            {{-- Ordinateur : zones en haut et note en bas d'un panneau latéral
                 (le pays, plus haut que large, laisse sinon deux marges vides).
                 Mobile : zones au-dessus de la carte, note au-dessous. --}}
            <div class="rs-carte-pied">
                <p class="rs-carte-note" id="rs-carte-note">
                    <span class="rs-carte-note__ic" aria-hidden="true"><svg class="ic"><use href="#i-lieu"/></svg></span>
                    <span>Sélectionnez une zone pour découvrir sa couverture générale. Les emplacements disponibles et les recommandations détaillées vous seront communiqués sur demande.</span>
                </p>
                <a class="lien-fleche" href="{{ route('marquette.contact') }}"><span class="rond"><svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></span>Recevoir une sélection d'emplacements</a>
            </div>
        </div>
    </div>
</section>

{{-- ═══ 4. COUVERTURE TERRITORIALE — mêmes couleurs que la carte ═══ --}}
<section class="section section--gris" aria-labelledby="couverture-titre">
    <div class="wrap">
        <x-mq.tete :surtitre="Contenu::get('reseau.comm_surtitre')" :titre="Contenu::get('reseau.comm_titre')" id-titre="couverture-titre" />

        <div class="territoires">
            @foreach([
                ['abidjan', 'rouge', $abidjan],
                ['int',     'jaune', $interieur],
            ] as $i => [$cle, $couleur, $liste])
                <article class="territoire territoire--large territoire--{{ $couleur }} rs-zone rv" style="--c:var(--{{ $couleur }});--d:{{ $i * .08 }}s" aria-labelledby="zone-{{ $cle }}">
                    <span class="territoire__forme" aria-hidden="true"></span>
                    <div class="rs-zone__haut">
                        <span class="rs-zone__ic" aria-hidden="true"><svg class="ic"><use href="#i-lieu"/></svg></span>
                        <h3 id="zone-{{ $cle }}">{{ Contenu::get("reseau.comm_{$cle}_titre") }}</h3>
                    </div>
                    <p>{{ Contenu::get("reseau.comm_{$cle}_sous") }}</p>
                    <ul class="tags">
                        @foreach($liste as $commune)<li class="tag">{{ $commune }}</li>@endforeach
                    </ul>
                    <p class="rs-zone__note">{{ Contenu::get("reseau.comm_{$cle}_note") }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ 5. FORMATS ═══ --}}
<section class="section section--blanc" aria-labelledby="formats-titre">
    <div class="wrap">
        <x-mq.tete surtitre="Des formats pour chaque ambition" titre="De la proximité à l'impact spectaculaire." intro="Notre parc comprend des dispositifs adaptés aux campagnes locales, urbaines, nationales et événementielles." id-titre="formats-titre" />

        <div class="rs-grille-4">
            @foreach($formats as $i => [$titre, $texte, $c, $picto])
                <article class="carte rs-format rv" style="--c:{{ $c }};--d:{{ $i * .06 }}s">
                    <div class="carte__corps">
                        <span class="rs-picto rs-picto--{{ $picto }}" aria-hidden="true"><i></i></span>
                        <h3 class="t3">{{ $titre }}</h3>
                        <p class="rs-texte">{{ $texte }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ 6. MEDIA INTELLIGENCE ═══ --}}
<section class="section section--noir rs-mi" aria-labelledby="mi-titre">
    <span class="decor decor--plume decor--masque-mobile" aria-hidden="true" style="--c:var(--blanc);--o:.06;width:170px;bottom:6%;left:2%;transform:rotate(-16deg)"></span>
    <div class="wrap grille-2">
        <div class="rv">
            <span class="sur">Media Intelligence</span>
            <h2 class="t2" id="mi-titre" style="margin-top:16px">Un parc référencé, piloté et documenté.</h2>
            <p class="chapo" style="margin-top:18px">Notre réseau est progressivement digitalisé afin d'améliorer la sélection des emplacements, la planification des campagnes, le contrôle des poses et la transmission des preuves de diffusion.</p>
        </div>
        <ul class="rs-briques">
            @foreach($intelligence as $i => [$titre, $texte, $c])
                <li class="rs-brique rv" style="--c:{{ $c }};--d:{{ $i * .06 }}s">
                    <span class="rs-brique__n num">{{ sprintf('%02d', $i + 1) }}</span>
                    <h3>{{ $titre }}</h3>
                    <p>{{ $texte }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>

{{-- ═══ 7. EXIGENCE TERRAIN ═══ --}}
<section class="section section--blanc" aria-labelledby="exigence-titre">
    <div class="wrap grille-2 rs-exigence">
        <div class="rv">
            <div class="cadre cadre--panneau rs-exigence__photo">
                <x-mq.photo cle="uber" sizes="(min-width: 900px) 46vw, 100vw" style="object-position:0% 50%" />
            </div>
        </div>
        <div class="rv" style="--d:.1s">
            <span class="sur">Notre exigence terrain</span>
            <h2 class="t2 rs-exigence__titre" id="exigence-titre">Un réseau exploité avec rigueur, du choix de l'emplacement au suivi de la campagne.</h2>
            <ul class="recit-liste">
                @foreach($exigence as [$titre, $texte])
                    <li>
                        <span class="rs-coche" aria-hidden="true"><svg class="ic"><use href="#i-check"/></svg></span>
                        <div>
                            <h3>{{ $titre }}</h3>
                            <p>{{ $texte }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- ═══ 8. CONTACT ═══ --}}
<x-mq.bande-contact :surtitre="Contenu::get('reseau.cta_surtitre')" :titre="Contenu::get('reseau.cta_titre')" :texte="Contenu::get('reseau.cta_texte')" />

@endsection

@push('js')
    <script src="{{ \App\Support\Marquette::asset('pages/reseau.js') }}" defer></script>
@endpush
