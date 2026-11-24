{{-- ═══════════════════════════════════════════════════════════════════
     Maquette — ACCUEIL. Textes : Contenu::get('accueil.*') (espace admin),
     inchangés. Ordre repensé d'après l'audit PPC.io :
       1. héro : vraie photo du réseau, preuve chiffrée près du bouton,
          délai de réponse affiché ;
       2. les 4 pôles juste après : CIBLE ne vend plus seulement des panneaux ;
       3. les 5 territoires tous visibles (plus d'onglets cachés) ;
       4. récit, mission, preuve réseau, distinctions, contact.
     Réalisations absentes (visuels et informations clients à fournir).
════════════════════════════════════════════════════════════════════ --}}
@extends('marquette.layout')
@use('App\Support\Contenu')

@section('titre', 'CIBLE — Régie publicitaire et studio créatif en Côte d\'Ivoire')
@section('description', 'Régie publicitaire ivoirienne depuis 1994 : affichage grand format, communication mobile, brand experience et Media Intelligence. +400 panneaux dans 31 communes.')

@php
    $ch = Contenu::section('chiffres');
    $hero = \App\Support\Marquette::photo('axe');
    // [ancre, n°, photo, couleur, couleur du surtitre de carte (jamais de
    //  petit texte jaune ou bleu sur blanc, cf. mq.css), étiquettes]
    $poles = [
        ['regie',              1, 'uber',   'var(--rouge)',  'var(--rouge)',  array_slice(Contenu::lignes('services.p1_dispositifs'), 0, 4)],
        ['mobile',             2, 'camion', 'var(--jaune)',  'var(--noir)',   array_slice(Contenu::lignes('accueil.terr2_tags'), 0, 4)],
        ['experience',         3, 'foule',  'var(--violet)', 'var(--violet)', array_slice(Contenu::lignes('accueil.terr5_tags'), 0, 4)],
        ['media-intelligence', 4, 'route',  'var(--bleu)',   'var(--noir)',   ['Inventaire média digitalisé', 'Suivi terrain digitalisé', 'Reporting de campagne']],
    ];
    $territoires = [
        [1, 'rouge',  '+' . $ch['panneaux'], true],
        [2, 'jaune',  $ch['communes'],       true],
        [3, 'vert',   Contenu::get('accueil.terr3_chiffre'), false],
        [4, 'bleu',   Contenu::get('accueil.terr4_chiffre'), false],
        [5, 'violet', Contenu::get('accueil.terr5_chiffre'), false],
    ];
@endphp

@push('preload')
    <link rel="preload" as="image" imagesrcset="{{ $hero['srcset'] }}" imagesizes="(min-width: 960px) 46vw, 100vw" fetchpriority="high">
@endpush

@section('contenu')

{{-- ═══ 1. HÉRO ═══ --}}
<section class="hero" aria-labelledby="hero-titre">
    <span class="decor decor--fleche decor--masque-mobile" aria-hidden="true" style="--c:var(--jaune);--o:.9;width:84px;top:4%;right:46%;transform:rotate(-14deg)"></span>
    <div class="wrap">
        <div class="hero__grille">
            <div>
                <span class="sur">{{ Contenu::get('accueil.hero_surtitre') }}</span>
                <h1 id="hero-titre">
                    <span class="l">{{ Contenu::get('accueil.hero_titre_1') }}</span>
                    <span class="l">{{ Contenu::get('accueil.hero_titre_2') }}</span>
                    <span class="l">{{ Contenu::get('accueil.hero_titre_3') }}</span>
                </h1>
                <p class="chapo hero__sous">{{ Contenu::get('accueil.hero_sous_titre') }}</p>
                <div class="actions">
                    <a class="btn btn--rouge" href="{{ route('marquette.contact') }}">Recevoir ma recommandation média<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></a>
                    <a class="btn btn--ligne" href="{{ route('marquette.services') }}">Nos services</a>
                </div>
                {{-- Audit : dire ce qui se passe après le clic, à côté du bouton. --}}
                <x-mq.reassurance />
            </div>
            <div class="hero__visuel">
                <div class="cadre cadre--panneau">
                    <x-mq.photo cle="axe" sizes="(min-width: 960px) 46vw, 100vw" prioritaire style="object-position:40% 50%" />
                </div>
                <span class="pastille-flottante pastille-flottante--a"><b class="num">+{{ $ch['panneaux'] }}</b>Panneaux en exploitation</span>
                <span class="pastille-flottante pastille-flottante--b" style="--c:var(--vert);--fg:var(--noir)"><b class="num">{{ $ch['communes'] }}</b>Communes et villes couvertes</span>
            </div>
        </div>

        {{-- Audit : la preuve chiffrée remonte dans le premier écran. --}}
        <div class="chiffres rv">
            <x-mq.chiffre :valeur="$ch['annees']" libelle="Ans d'expertise" />
            <x-mq.chiffre :valeur="$ch['panneaux']" plus libelle="Panneaux en propre" />
            <x-mq.chiffre :valeur="$ch['communes']" libelle="Communes couvertes" />
            <x-mq.chiffre :valeur="$ch['distinctions']" libelle="Distinctions d'État" />
        </div>
    </div>
</section>

{{-- ═══ BANDEAU DÉFILANT ═══ Piste dupliquée : la boucle exige deux moitiés identiques. --}}
<div class="defilant" role="region" aria-label="CIBLE en bref">
    <div class="defilant__piste">
        @for($passe = 0; $passe < 2; $passe++)
            <div style="display:flex" @if($passe) aria-hidden="true" @endif>
                @foreach(Contenu::lignes('accueil.ticker') as $mention)<span>{{ $mention }}</span>@endforeach
            </div>
        @endfor
    </div>
</div>

{{-- ═══ 2. LES 4 PÔLES ═══ --}}
<section class="section section--motif" aria-labelledby="poles-titre">
    <div class="wrap">
        <x-mq.tete ligne :surtitre="Contenu::get('services.hero_surtitre')" id-titre="poles-titre">
            {{ Contenu::get('services.hero_titre_1') }} <em>{{ Contenu::get('services.hero_titre_2') }}</em>
            <x-slot:intro>{!! Contenu::riche('accueil.hero_accroche') !!}</x-slot:intro>
        </x-mq.tete>

        <div class="poles">
            @foreach($poles as $i => [$ancre, $n, $photo, $c, $t, $tags])
                <a class="carte rv" href="{{ route('marquette.services') }}#{{ $ancre }}" style="--c:{{ $c }};--t:{{ $t }};--d:{{ $i * .08 }}s">
                    <div class="cadre cadre--169 cadre--zoom"><x-mq.photo :cle="$photo" sizes="(min-width: 1200px) 300px, (min-width: 720px) 50vw, 100vw" /></div>
                    <div class="carte__corps">
                        <span class="carte__tag">{{ Contenu::get("services.p{$n}_tag") }}</span>
                        <h3 class="t3 riche">{!! Contenu::riche("services.p{$n}_titre") !!}</h3>
                        <div class="tags">@foreach($tags as $tag)<span class="tag">{{ $tag }}</span>@endforeach</div>
                        <span class="lien-fleche"><span class="rond"><svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></span>Découvrir le pôle</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ 3. LES 5 TERRITOIRES — tous visibles, sans clic ═══ --}}
<section class="section" aria-labelledby="territoires-titre">
    <div class="wrap">
        <x-mq.tete :surtitre="Contenu::get('accueil.terr_surtitre')" :titre="Contenu::get('accueil.terr_titre')" :intro="Contenu::get('accueil.terr_intro')" id-titre="territoires-titre" />

        <div class="territoires">
            @foreach($territoires as $i => [$n, $couleur, $valeur, $large])
                <article class="territoire territoire--{{ $couleur }} {{ $large ? 'territoire--large' : '' }} rv" style="--c:var(--{{ $couleur }});--d:{{ $i * .06 }}s">
                    <span class="territoire__forme" aria-hidden="true"></span>
                    <div class="territoire__haut">
                        <span class="territoire__nom">{{ Contenu::get("accueil.terr{$n}_onglet") }}</span>
                    </div>
                    <div>
                        <div class="territoire__v num">{{ $valeur }}</div>
                        <div class="territoire__l">{{ Contenu::get("accueil.terr{$n}_surtitre") }}</div>
                    </div>
                    <h3>{{ Contenu::get("accueil.terr{$n}_titre") }}</h3>
                    <p>{{ Contenu::get("accueil.terr{$n}_texte") }}</p>
                    <div class="tags">@foreach(array_slice(Contenu::lignes("accueil.terr{$n}_tags"), 0, $large ? 6 : 3) as $t)<span class="tag">{{ $t }}</span>@endforeach</div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ 4. RÉCIT — Opération plume rouge (le perroquet n'apparaît qu'ici) ═══ --}}
<section class="section section--gris" aria-labelledby="recit-titre">
    <div class="wrap grille-2">
        <div class="rv">
            <div class="cadre cadre--panneau cadre--43">
                <x-mq.photo cle="perroquet" sizes="(min-width: 900px) 46vw, 100vw" style="object-position:55% 40%" />
            </div>
        </div>
        <div class="rv" style="--d:.1s">
            <span class="sur">{{ Contenu::get('accueil.marque_surtitre') }}</span>
            <h2 class="t2" id="recit-titre">{{ Contenu::get('accueil.marque_titre') }}</h2>
            <p class="chapo">{{ Contenu::get('accueil.marque_intro') }}</p>
            <ol class="recit-liste">
                @foreach([1, 2, 3] as $n)
                    <li>
                        <span class="n num">0{{ $n }}</span>
                        <div>
                            <h3>{{ Contenu::get("accueil.marque_{$n}_titre") }}</h3>
                            <p>{{ Contenu::get("accueil.marque_{$n}_texte") }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
            <p class="signature">{!! Contenu::riche('accueil.marque_signature') !!}</p>
        </div>
    </div>
</section>

{{-- ═══ MISSION ═══ --}}
<section class="section" aria-label="Notre mission">
    <span class="decor decor--plume decor--masque-mobile" aria-hidden="true" style="--c:var(--rouge);--o:.08;width:180px;top:12%;left:4%;transform:rotate(-20deg)"></span>
    <span class="decor decor--fleche decor--masque-mobile" aria-hidden="true" style="--c:var(--bleu);--o:.1;width:150px;bottom:14%;right:5%;transform:rotate(12deg)"></span>
    <div class="wrap enonce rv">
        <p>{{ Contenu::get('accueil.mission_texte') }}</p>
        <p class="fort">{{ Contenu::get('accueil.mission_fort') }}</p>
    </div>
</section>

{{-- ═══ 5. RÉSEAU — la preuve ═══ --}}
<section class="section section--motif" aria-labelledby="reseau-titre">
    <div class="wrap grille-2 grille-2--55">
        <div class="rv">
            <span class="sur">{{ Contenu::get('accueil.reseau_surtitre') }}</span>
            <h2 class="t2" id="reseau-titre">{{ Contenu::get('accueil.reseau_titre') }}</h2>
            <p class="citation-preuve">{{ Contenu::get('accueil.reseau_texte') }}</p>
            <div class="chiffres-duo">
                <x-mq.chiffre :valeur="$ch['panneaux']" plus libelle="Panneaux en exploitation" />
                <x-mq.chiffre :valeur="$ch['communes']" libelle="Communes et villes couvertes" />
            </div>
            <div class="actions">
                <a class="btn btn--ligne" href="{{ route('marquette.reseau') }}">{{ Contenu::get('accueil.reseau_cta') }}<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></a>
            </div>
        </div>
        <div class="preuve-photo rv" style="--d:.1s">
            <div class="cadre cadre--panneau"><x-mq.photo cle="bache" sizes="(min-width: 900px) 42vw, 100vw" style="object-position:50% 30%" /></div>
            <span class="pastille-flottante" style="--c:var(--rouge)"><b><svg class="ic" aria-hidden="true"><use href="#i-check"/></svg></b>{{ Contenu::get('accueil.terr1_preuve') }}</span>
        </div>
    </div>
</section>

{{-- ═══ 6. DISTINCTIONS ═══ --}}
<section class="section" aria-labelledby="dist-titre">
    <div class="wrap">
        <x-mq.tete surtitre="Reconnaissances officielles" titre="Trois distinctions de l'État ivoirien." id-titre="dist-titre" />
        {{-- [année, distinction, précision, couleur, couleur de l'année (noir pour le jaune)] --}}
        <div class="grille-3">
            @foreach([
                ['2016', '2ᵉ prix du meilleur publicitaire', 'Distinction professionnelle du secteur de la publicité ivoirienne.', 'var(--jaune)', 'var(--noir)'],
                ['2019', 'Chevalier de l\'Ordre du Mérite de la Communication', 'Reconnaissance de la contribution à la structuration du métier.', 'var(--vert)', 'var(--vert)'],
                ['2020', 'Officier de l\'Ordre du Mérite National', 'Distinction républicaine pour services rendus au pays.', 'var(--violet)', 'var(--violet)'],
            ] as $i => [$an, $titre, $texte, $c, $t])
                <article class="carte distinction rv" style="--c:{{ $c }};--t:{{ $t }};--d:{{ $i * .08 }}s">
                    <div class="carte__corps">
                        <span class="carte__num num">{{ $an }}</span>
                        <h3>{{ $titre }}</h3>
                        <p>{{ $texte }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ 7. CONTACT ═══ --}}
<x-mq.bande-contact :titre="Contenu::get('accueil.contact_titre')" :texte="Contenu::get('accueil.contact_texte')" />

@endsection
