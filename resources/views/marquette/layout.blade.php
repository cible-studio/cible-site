{{-- ═══════════════════════════════════════════════════════════════════
     Maquette de refonte CIBLE — mise en page commune (/marquette)
     · Non indexée, absente du menu du site, sans GTM (ne fausse pas les
       statistiques du vrai site).
     · Textes : les mêmes sources que le site (Contenu::get → espace admin).
     · Styles et scripts : public/mq/mq.css et mq.js, partagés par
       toutes les pages — c'est ce qui garantit la cohérence entre elles.
════════════════════════════════════════════════════════════════════ --}}
@use('App\Support\Contenu')
@use('App\Support\Marquette')
@php
    $co = Contenu::section('coordonnees');
    $tel = fn (string $n) => 'tel:+' . preg_replace('#\D#', '', $n);

    // Les 4 pôles : nom court tiré du libellé admin « Pôle 0X · Nom ».
    $poles = collect([
        ['regie',              'services.p1_tag', 'var(--rouge)'],
        ['mobile',             'services.p2_tag', 'var(--jaune)'],
        ['experience',         'services.p3_tag', 'var(--violet)'],
        ['media-intelligence', 'services.p4_tag', 'var(--bleu)'],
    ])->map(fn ($p, $i) => [
        'ancre' => $p[0],
        'nom'   => trim(\Illuminate\Support\Str::after(Contenu::get($p[1]), '·')),
        'c'     => $p[2],
        'n'     => sprintf('%02d', $i + 1),
    ]);

    $liens = [
        ['accueil',  'Accueil'],
        ['qui',      'Qui sommes-nous'],
        ['services', 'Nos services'],
        ['reseau',   'Notre réseau'],
        ['contact',  'Contact'],
    ];
    $cta = 'Recevoir ma recommandation média';
@endphp
<!DOCTYPE html>
<html lang="fr" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('titre', 'CIBLE — Régie publicitaire en Côte d\'Ivoire · Vous visez juste') · Maquette</title>
    <meta name="description" content="@yield('description', 'Régie publicitaire et studio créatif ivoirien depuis 1994.')">
    <meta name="theme-color" content="#E20613">
    <script>document.documentElement.className = 'js';</script>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icone-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-180.png') }}">
    <link rel="preload" href="{{ asset('mq/fonts/poppins-800-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('mq/fonts/nunito-400-800-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    @stack('preload')
    <link rel="stylesheet" href="{{ Marquette::asset('mq.css') }}">
    @stack('css')
</head>
<body data-page="{{ $page }}">

<a class="evitement" href="#contenu">Aller au contenu</a>

{{-- Pictogrammes : un seul sprite, réutilisé partout via <use>. --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <symbol id="i-fleche" viewBox="0 0 24 24"><path d="M4 12h15M13 6l6 6-6 6"/></symbol>
    <symbol id="i-bas" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></symbol>
    <symbol id="i-tel" viewBox="0 0 24 24"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></symbol>
    <symbol id="i-lieu" viewBox="0 0 24 24"><path d="M12 21s-7-6.1-7-11.5a7 7 0 0 1 14 0C19 14.9 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></symbol>
    <symbol id="i-fermer" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></symbol>
</svg>

<header class="entete">
    <div class="entete__barre">
        <a class="logo" href="{{ route('marquette.accueil') }}" aria-label="CIBLE — accueil">
            <img src="{{ asset('mq/img/marque/logo-couleur.png') }}" alt="CIBLE — Vous visez juste" width="152" height="42">
        </a>

        <nav class="nav" aria-label="Navigation principale">
            @foreach($liens as [$cle, $libelle])
                @if($cle === 'services')
                    <div class="deroulant">
                        <button class="nav__bouton {{ $page === 'services' ? 'est-actif' : '' }}" type="button" data-deroulant aria-expanded="false" aria-controls="sous-menu-services">
                            {{ $libelle }}<svg class="ic" aria-hidden="true"><use href="#i-bas"/></svg>
                        </button>
                        <div class="deroulant__panneau" id="sous-menu-services">
                            @foreach($poles as $p)
                                <a class="deroulant__lien" href="{{ route('marquette.services') }}#{{ $p['ancre'] }}">
                                    <span class="deroulant__num" style="--c:{{ $p['c'] }}{{ $p['ancre'] === 'mobile' ? ';color:var(--noir)' : '' }}">{{ $p['n'] }}</span>
                                    <span><b>{{ $p['nom'] }}</b></span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a class="nav__lien" href="{{ route("marquette.$cle") }}" @if($page === $cle) aria-current="page" @endif>{{ $libelle }}</a>
                @endif
            @endforeach
        </nav>

        <a class="btn btn--rouge entete__cta" href="{{ route('marquette.contact') }}">{{ $cta }}</a>

        <div class="entete__outils">
            <a class="rond-outil rond-outil--rouge" href="{{ $tel($co['tel_mobile']) }}" aria-label="Appeler CIBLE au {{ $co['tel_mobile'] }}">
                <svg class="ic" aria-hidden="true"><use href="#i-tel"/></svg>
            </a>
            <button class="rond-outil" type="button" data-menu-ouvrir aria-expanded="false" aria-controls="panneau-mobile" aria-label="Ouvrir le menu">
                <svg class="ic" aria-hidden="true"><use href="#i-menu"/></svg>
            </button>
        </div>
    </div>
</header>

{{-- Menu mobile : plein écran, gros liens (≥ 50 px), action et contacts en bas. --}}
<div class="panneau-mobile" id="panneau-mobile" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Menu">
    <div class="panneau-mobile__haut">
        <a class="logo" href="{{ route('marquette.accueil') }}"><img src="{{ asset('mq/img/marque/logo-couleur.png') }}" alt="CIBLE" width="152" height="42"></a>
        <button class="rond-outil" type="button" data-menu-fermer aria-label="Fermer le menu"><svg class="ic" aria-hidden="true"><use href="#i-fermer"/></svg></button>
    </div>
    <nav aria-label="Navigation mobile">
        @foreach($liens as [$cle, $libelle])
            <a href="{{ route("marquette.$cle") }}" @if($page === $cle) aria-current="page" @endif>{{ $libelle }}<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></a>
            @if($cle === 'services')
                @foreach($poles as $p)
                    <a class="sous" href="{{ route('marquette.services') }}#{{ $p['ancre'] }}">{{ $p['n'] }} · {{ $p['nom'] }}</a>
                @endforeach
            @endif
        @endforeach
    </nav>
    <div class="panneau-mobile__bas">
        <a class="btn btn--rouge btn--plein" href="{{ route('marquette.contact') }}">{{ $cta }}<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></a>
        <a class="btn btn--ligne btn--plein" href="{{ $tel($co['tel_mobile']) }}"><svg class="ic" aria-hidden="true"><use href="#i-tel"/></svg>{{ $co['tel_mobile'] }}</a>
    </div>
</div>

<main id="contenu">
    @yield('contenu')
</main>

<footer class="pied">
    <div class="wrap">
        <div class="pied__grille">
            <div>
                <img src="{{ asset('mq/img/marque/logo-fond-noir.png') }}" alt="CIBLE — Vous visez juste" width="166" height="46" loading="lazy">
                <p class="pied__slogan">Régie publicitaire et studio créatif.<br>+&nbsp;de&nbsp;30&nbsp;ans à rendre les marques visibles en Côte d'Ivoire.</p>
                <div class="pied__couleurs" aria-hidden="true">
                    @foreach(['rouge','jaune','vert','bleu','violet'] as $c)<i style="--c:var(--{{ $c }})"></i>@endforeach
                </div>
            </div>
            <div>
                <h2>Explorer</h2>
                <ul>
                    @foreach($liens as [$cle, $libelle])
                        <li><a href="{{ route("marquette.$cle") }}">{{ $libelle }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h2>Nos expertises</h2>
                <ul>
                    @foreach($poles as $p)
                        <li><a href="{{ route('marquette.services') }}#{{ $p['ancre'] }}">{{ $p['nom'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h2>Contact</h2>
                <ul>
                    <li><a class="num" href="{{ $tel($co['tel_mobile']) }}">{{ $co['tel_mobile'] }}</a></li>
                    @if($co['tel_fixe'])<li><a class="num" href="{{ $tel($co['tel_fixe']) }}">{{ $co['tel_fixe'] }}</a></li>@endif
                    <li><a href="mailto:{{ $co['email_commercial'] }}">{{ $co['email_commercial'] }}</a></li>
                    <li style="padding-top:10px">{{ $co['adresse_rue'] }}<br>{{ $co['adresse_complement'] }}</li>
                </ul>
            </div>
        </div>
        <div class="pied__bas">
            <span class="num">© {{ date('Y') }} CIBLE · Tous droits réservés</span>
            <span>
                Maquette de refonte — photos d'illustration provisoires :
                @foreach(Marquette::credits() as $c)<a href="{{ $c['lien'] }}" rel="noopener" target="_blank">{{ $c['nom'] }}</a>{{ $loop->last ? ' / Unsplash.' : ', ' }}@endforeach
                <a href="{{ route('home') }}">Voir le site actuel</a>
            </span>
        </div>
    </div>
</footer>

{{-- Action toujours à portée de pouce sur mobile (audit PPC.io). --}}
<div class="barre-action" aria-label="Actions rapides">
    <a class="rond-outil" href="{{ $tel($co['tel_mobile']) }}" aria-label="Appeler CIBLE au {{ $co['tel_mobile'] }}"><svg class="ic" aria-hidden="true"><use href="#i-tel"/></svg></a>
    <a class="btn btn--rouge" href="{{ route('marquette.contact') }}">{{ $cta }}</a>
</div>

<script src="{{ Marquette::asset('mq.js') }}" defer></script>
@stack('js')
</body>
</html>
