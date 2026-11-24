{{-- ═══════════════════════════════════════════════════════════════════
     Maquette — QUI SOMMES-NOUS. Textes : Contenu::get('qui.*') (espace
     admin), inchangés ; libellés des chiffres et des 3 distinctions repris
     mot pour mot de la vue actuelle (resources/views/qui-sommes-nous.blade.php).
       1. héro : titre sur toute la largeur, grande photo en dessous ;
       2. récit : frise verticale 1994 → 01 → 04, photos du réseau à côté ;
       3. chiffres : bande noire, valeurs écrites en clair ;
       4. distinctions : frise des années 2016 → 2019 → 2020 ;
       5. contact.
     Photos du récit : vraies photos CIBLE (terrain, camion podium) plutôt
     qu'une équipe au travail — sur cette page, elle passerait pour le
     personnel de CIBLE (règle : aucune photo d'équipe).
════════════════════════════════════════════════════════════════════ --}}
@extends('marquette.layout')
@use('App\Support\Contenu')
@use('Illuminate\Support\Str')

@section('titre', 'Qui sommes-nous — CIBLE · 30 ans de communication extérieure')
@section('description', 'CIBLE, régie publicitaire ivoirienne fondée en 1994. Trois décennies au service des marques, trois distinctions officielles de l\'État.')

@php
    $ch = Contenu::section('chiffres');
    $hero = \App\Support\Marquette::photo('course');
    $heroSizes = '(min-width: 1340px) 1240px, 94vw';

    // Récit : « Notre origine — l'expertise de la visibilité » se lit en
    // deux temps (étape, puis idée). Sans tiret dans le titre saisi en
    // admin, le titre s'affiche d'un bloc : rien ne casse.
    $etapes = collect([
        [1, 'var(--rouge)',  'var(--blanc)'],
        [2, 'var(--jaune)',  'var(--noir)'],   // texte noir sur jaune (contraste)
        [3, 'var(--vert)',   'var(--blanc)'],
        [4, 'var(--violet)', 'var(--blanc)'],
    ])->map(function ($e) {
        [$n, $c, $fg] = $e;
        $titre = (string) Contenu::get("qui.recit_{$n}_titre");
        $coupe = Str::contains($titre, ' — ');

        return [
            'n'      => sprintf('%02d', $n),
            'c'      => $c,
            'fg'     => $fg,
            'etape'  => $coupe ? Str::before($titre, ' — ') : null,
            'idee'   => $coupe ? Str::after($titre, ' — ') : $titre,
            'texte'  => Contenu::get("qui.recit_{$n}_texte"),
        ];
    });

    // Distinctions : textes de la vue actuelle, à ne pas paraphraser.
    // [année, distinction, précision, couleur, couleur du texte de l'année]
    $distinctions = [
        ['2016', '2ᵉ prix du meilleur publicitaire', 'Distinction professionnelle du secteur de la publicité ivoirienne.', 'var(--jaune)', 'var(--noir)'],
        ['2019', 'Chevalier de l\'Ordre du Mérite de la Communication', 'Reconnaissance de la contribution à la structuration du métier en Côte d\'Ivoire.', 'var(--vert)', 'var(--blanc)'],
        ['2020', 'Officier de l\'Ordre du Mérite National', 'Distinction républicaine pour services rendus au pays.', 'var(--violet)', 'var(--blanc)'],
    ];
@endphp

@push('preload')
    <link rel="preload" as="image" imagesrcset="{{ $hero['srcset'] }}" imagesizes="{{ $heroSizes }}" fetchpriority="high">
@endpush

@push('css')
    <link rel="stylesheet" href="{{ \App\Support\Marquette::asset('pages/qui.css') }}">
@endpush

@section('contenu')

{{-- ═══ 1. HÉRO ═══ Titre long : il prend toute la largeur (3 lignes au
     lieu de 5 dans une demi-colonne), la photo s'étale dessous. --}}
<section class="page-hero qui-hero" aria-labelledby="hero-titre">
    <div class="wrap">
        <x-mq.fil :etapes="['Qui sommes-nous']" />
        <span class="sur">{{ Contenu::get('qui.hero_surtitre') }}</span>
        <h1 class="t1 riche qui-hero__titre" id="hero-titre">{!! Contenu::riche('qui.hero_titre') !!}</h1>

        <div class="qui-hero__suite">
            <p class="chapo">{{ Contenu::get('qui.hero_texte') }}</p>
            <div class="qui-hero__action">
                {{-- Flèche de marque au-dessus du bouton, hors de la zone du titre (cf. qui.css). --}}
                <span class="decor decor--fleche decor--masque-mobile qui-decor-large qui-hero__fleche" aria-hidden="true" style="--c:var(--jaune);--o:.9"></span>
                <div class="actions">
                    <a class="btn btn--rouge" href="{{ route('marquette.contact') }}">Recevoir ma recommandation média<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></a>
                </div>
                <ul class="reassurance">
                    @foreach(array_slice(Contenu::lignes('contact.reass_liste'), 0, 2) as $r)
                        <li><svg class="ic" aria-hidden="true"><use href="#i-check"/></svg>{{ $r }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="qui-hero__visuel">
            <div class="cadre cadre--panneau">
                <x-mq.photo cle="course" :sizes="$heroSizes" prioritaire />
            </div>
            <span class="pastille-flottante qui-hero__pastille-a"><b class="num">{{ $ch['annees'] }}</b>Ans d'expertise</span>
            <span class="pastille-flottante qui-hero__pastille-b" style="--c:var(--violet)"><b class="num">{{ $ch['distinctions'] }}</b>Distinctions d'État</span>
        </div>
    </div>
</section>

{{-- ═══ 2. RÉCIT — frise 1994 → 04 ═══ Seule date connue : 1994, qui
     ouvre la frise. Les étapes suivantes ne sont pas datées (aucune date
     inventée). Le titre de section n'existe pas dans les textes : il est
     réservé aux lecteurs d'écran. --}}
<section class="section section--gris qui-recit" aria-labelledby="recit-titre">
    <h2 class="vh" id="recit-titre">Notre histoire</h2>
    <div class="wrap qui-recit__grille">
        <div class="qui-recit__media">
            <div class="qui-recit__photos rv">
                <div class="cadre cadre--panneau qui-recit__photo-a">
                    <x-mq.photo cle="route" sizes="(min-width: 960px) 38vw, 56vw" style="object-position:50% 40%" />
                </div>
                <div class="cadre qui-recit__photo-b">
                    <x-mq.photo cle="camion" sizes="(min-width: 960px) 22vw, 40vw" style="object-position:42% 50%" />
                </div>
            </div>
        </div>

        <div class="frise">
            <p class="frise__depart rv"><span class="num">1994</span></p>
            <ol class="frise__liste">
                @foreach($etapes as $i => $e)
                    <li class="frise__etape rv" style="--c:{{ $e['c'] }};--fg:{{ $e['fg'] }};--d:{{ $i * .06 }}s">
                        <span class="frise__n num" aria-hidden="true">{{ $e['n'] }}</span>
                        <div class="frise__carte">
                            <h3>
                                @if($e['etape'])
                                    <span class="frise__etiquette">{{ $e['etape'] }}</span><span class="vh"> — </span>
                                @endif
                                <span class="frise__titre">{{ $e['idee'] }}</span>
                            </h3>
                            <p>{{ $e['texte'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
            <span class="frise__fin" aria-hidden="true"><svg class="ic"><use href="#i-fleche"/></svg></span>
        </div>
    </div>
</section>

{{-- ═══ 3. CHIFFRES — bande noire ═══ --}}
<section class="section section--noir qui-chiffres" aria-labelledby="chiffres-titre">
    <span class="decor decor--fleche decor--masque-mobile qui-decor-large" aria-hidden="true" style="--c:var(--jaune);--o:.9;width:84px;top:16%;right:7%;transform:rotate(-12deg)"></span>
    <div class="wrap">
        <x-mq.tete :surtitre="Contenu::get('qui.stats_surtitre')" :titre="Contenu::get('qui.stats_titre')" centre id-titre="chiffres-titre" />
        <div class="qui-chiffres__grille rv">
            <x-mq.chiffre :valeur="$ch['annees']" libelle="Ans d'expertise" />
            <x-mq.chiffre :valeur="$ch['panneaux']" plus libelle="Panneaux en propre" />
            <x-mq.chiffre :valeur="$ch['communes']" libelle="Communes couvertes" />
            <x-mq.chiffre :valeur="$ch['distinctions']" libelle="Distinctions d'État" />
        </div>
    </div>
</section>

{{-- ═══ 4. DISTINCTIONS — frise des années ═══ --}}
<section class="section section--motif" aria-labelledby="dist-titre">
    <div class="wrap">
        <x-mq.tete :surtitre="Contenu::get('qui.dist_surtitre')" :titre="Contenu::get('qui.dist_titre')" id-titre="dist-titre" />
        <ol class="qui-dist">
            @foreach($distinctions as $i => [$an, $titre, $texte, $c, $fg])
                <li class="qui-dist__jalon rv" style="--c:{{ $c }};--fg:{{ $fg }};--d:{{ $i * .08 }}s">
                    <span class="qui-dist__an num">{{ $an }}</span>
                    <article class="carte qui-dist__carte">
                        <div class="carte__corps">
                            <h3>{{ $titre }}</h3>
                            <p>{{ $texte }}</p>
                        </div>
                    </article>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ═══ 5. CONTACT ═══ --}}
<x-mq.bande-contact :titre="Contenu::get('qui.cta_titre')" />

@endsection
