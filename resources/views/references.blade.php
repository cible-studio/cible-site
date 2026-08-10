@extends('_layout', [
    'seo_title'       => 'Réalisations — CIBLE · Des campagnes visibles, des marques renforcées',
    'seo_description' => 'Nos réalisations : Orange, Groupe Cofina, SNEDAI, SGS · SICTA, IFG, SIGFU. Affichage, brand experience, street marketing, digital, production audiovisuelle, design événementiel.',
])

@push('page-css')
    .hero-ref{padding:clamp(60px,8vw,110px) var(--pad);background:linear-gradient(180deg,#fff,#F9F9F5)}
    .hero-ref .sur{color:var(--jaune)}
    .hero-ref h1{margin-top:14px;max-width:24ch}
    .hero-ref p{margin-top:24px;max-width:62ch;font-size:19px;color:#444}

    /* Introduction */
    .intro-ref{padding:clamp(50px,7vw,90px) var(--pad);background:var(--gris)}
    .intro-ref .entete{max-width:760px;margin:0 auto;text-align:center}
    .intro-ref .sur{color:var(--violet)}
    .intro-ref .t1{margin-top:12px}
    .intro-ref p{margin-top:20px;color:#444}

    .travaux{padding:clamp(56px,8vw,100px) var(--pad)}

    /* Filtres */
    .filtres{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-bottom:44px}
    .filtre{
        font-family:var(--titre);font-weight:700;font-size:13.5px;
        padding:11px 20px;border-radius:999px;cursor:pointer;
        border:1.5px solid var(--gris);background:#fff;color:#555;
        transition:border-color .2s,background .2s,color .2s,transform .2s;
    }
    .filtre:hover{border-color:var(--noir);transform:translateY(-2px)}
    .filtre[aria-pressed="true"]{background:var(--noir);border-color:var(--noir);color:#fff}

    .grille{display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(18px,2.5vw,30px)}
    @media(max-width:960px){.grille{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:640px){.grille{grid-template-columns:1fr}}
    .carte{display:flex;flex-direction:column;transition:transform .28s cubic-bezier(.2,.8,.3,1),opacity .28s}
    .carte:hover{transform:translateY(-8px)}
    .carte[hidden]{display:none}
    .carte .vign{aspect-ratio:4/3;border-radius:22px;overflow:hidden;position:relative}
    .carte .vign::after{content:"";position:absolute;inset:auto 0 0 0;height:7px;background:var(--c);transform:scaleX(0);transform-origin:left;transition:transform .35s cubic-bezier(.2,.8,.3,1)}
    .carte:hover .vign::after{transform:scaleX(1)}
    .carte h3{font-family:var(--titre);font-weight:800;font-size:20px;margin-top:16px}
    .carte .meta{font-size:13.5px;color:#666;margin-top:4px;display:flex;align-items:center;gap:8px}
    .pastille{display:inline-block;width:10px;height:10px;border-radius:50%;background:var(--c);flex-shrink:0}
    .carte .accroche{font-family:var(--titre);font-weight:700;font-size:16px;line-height:1.35;margin-top:12px;color:#111}
    .carte .desc{font-size:14px;color:#555;line-height:1.6;margin-top:10px}
    .carte .services{font-size:12.5px;color:#888;margin-top:12px;line-height:1.5}
    .carte .lien{
        margin-top:auto;padding-top:16px;
        font-family:var(--titre);font-weight:800;font-size:14px;color:var(--c);
        display:inline-flex;align-items:center;gap:8px;
    }
    .carte .lien svg{width:14px;fill:currentColor;transition:transform .22s}
    .carte:hover .lien svg{transform:translateX(4px)}
    .filtre-vide{text-align:center;color:#888;font-family:var(--titre);font-weight:600;padding:40px 0}

    /* Impact */
    .impact{padding:clamp(56px,8vw,100px) var(--pad);background:var(--noir);color:#fff}
    .impact .entete{max-width:700px;margin:0 auto 48px;text-align:center}
    .impact .sur{color:var(--jaune)}
    .impact .t1{margin-top:12px;color:#fff}
    .impact-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
    @media(max-width:900px){.impact-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:520px){.impact-grid{grid-template-columns:1fr}}
    .impact-card{padding:26px 22px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:14px;border-top:3px solid var(--c)}
    .impact-card h4{font-family:var(--titre);font-weight:800;font-size:17px;color:var(--c);margin-bottom:10px}
    .impact-card p{font-size:13.5px;color:rgba(255,255,255,.72);line-height:1.6}

    /* Méthode & preuves */
    .preuves{padding:clamp(56px,8vw,100px) var(--pad)}
    .preuves-wrap{display:grid;grid-template-columns:1fr 1fr;gap:clamp(30px,5vw,70px);align-items:center;max-width:1200px;margin:0 auto}
    @media(max-width:860px){.preuves-wrap{grid-template-columns:1fr}}
    .preuves .sur{color:var(--rouge)}
    .preuves .t1{margin-top:12px}
    .preuves p.body{margin-top:20px;color:#444;line-height:1.7}
    .preuves-list{list-style:none;display:flex;flex-direction:column;gap:12px}
    .preuves-list li{
        display:flex;align-items:flex-start;gap:14px;
        background:var(--gris);border-radius:12px;padding:18px 20px;
        font-family:var(--titre);font-weight:700;font-size:15px;color:#222;
    }
    .preuves-list li i{width:10px;height:10px;border-radius:50%;background:var(--c);flex-shrink:0;margin-top:6px}

    .clients-band{padding:clamp(56px,8vw,100px) var(--pad);background:var(--gris)}
    /* Centrage 2026-08-04 */
    .clients-band .entete{max-width:720px;margin:0 auto 44px;text-align:center}
    .clients-band .sur{color:var(--vert)}
    .clients-band .entete p{margin-top:18px;color:#444}
    .clients-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:16px;align-items:center}
    @media(max-width:900px){.clients-grid{grid-template-columns:repeat(3,1fr)}}
    @media(max-width:500px){.clients-grid{grid-template-columns:repeat(2,1fr)}}
    .clogo{aspect-ratio:2/1;background:#fff;border-radius:12px;display:flex;align-items:center;justify-content:center;padding:14px;transition:transform .2s}
    .clogo:hover{transform:translateY(-3px)}
    .clogo img{max-height:60px;max-width:100%;object-fit:contain}
    .clogo-txt{font-family:var(--titre);font-weight:800;color:#666;font-size:14px;text-align:center}

    .cta-ref{padding:clamp(60px,8vw,120px) var(--pad);text-align:center}
    .cta-ref .sur{color:var(--rouge)}
    .cta-ref .t1{max-width:24ch;margin:14px auto 0}
    .cta-ref p{margin-top:20px;max-width:58ch;margin-left:auto;margin-right:auto;color:#666}
@endpush

@section('content')

<svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs>
<symbol id="ref-fleche" viewBox="0 0 177 119"><path fill-rule="evenodd" transform="translate(177,0) scale(-1,1)" d="M0.1 94.8C0.1 75.5 0.4 70.9 1.3 72.5C3.0 75.4 12.6 84.6 19.0 89.5C31.2 98.8 49.7 108.5 63.9 113.1C70.3 115.2 71.2 115.2 75.1 114.0C83.4 111.2 88.1 103.7 86.6 95.6C85.6 90.3 78.7 83.9 71.9 81.7C64.6 79.5 50.8 72.6 51.6 71.7C52.7 70.6 70.3 70.9 76.2 72.1C89.4 74.8 98.3 77.6 108.0 82.3C122.7 89.3 131.3 95.4 142.8 107.0C148.3 112.5 153.3 117.0 153.8 117.0C154.4 117.0 155.2 117.5 155.5 118.0C155.9 118.7 129.8 119.0 78.1 119.0L0.0 119.0L0.1 94.8ZM166.5 118.0C166.8 117.5 167.7 117.0 168.4 117.0C170.4 117.0 173.8 112.8 175.4 108.5L176.9 104.5L176.9 111.8L177.0 119.0L171.4 119.0C168.0 119.0 166.1 118.6 166.5 118.0ZM175.2 97.6C174.3 94.2 172.1 91.3 165.3 84.3C145.1 63.7 120.7 49.7 94.0 43.2C79.7 39.8 75.1 39.0 69.5 39.0C64.1 39.0 61.9 37.9 64.2 36.3C66.9 34.6 74.9 33.1 84.5 32.4C96.1 31.7 99.8 30.2 103.7 25.2C109.8 17.1 105.4 4.1 95.5 1.1C93.6 0.5 107.3 0.1 134.8 0.1L177.0 0.0L177.0 51.0C177.0 79.0 176.9 102.0 176.7 102.0C176.5 102.0 175.9 100.0 175.2 97.6ZM0.0 30.2L0.0 0.0L38.2 0.1C59.5 0.2 74.1 0.6 71.0 1.0C54.1 3.3 33.8 13.9 20.9 27.4C15.4 33.1 7.0 44.8 7.0 46.6C7.0 47.2 6.4 48.3 5.6 48.9C4.8 49.6 3.8 51.6 3.4 53.3C3.1 55.1 2.1 57.4 1.4 58.5C0.2 60.2 0.0 56.1 0.0 30.2Z"/></symbol>
</defs></svg>

<section class="hero-ref">
    <span class="sur">Nos réalisations</span>
    <h1 class="t1">Des campagnes visibles. Des expériences mémorables. Des marques renforcées.</h1>
    <p>Plus de trente ans à accompagner les entreprises, institutions et grandes marques dans la conception et le déploiement de campagnes qui associent visibilité, créativité, expérience de marque et impact terrain.</p>
</section>

<section class="intro-ref">
    <div class="entete rev">
        <span class="sur">Notre savoir-faire en action</span>
        <h2 class="t1">De l'affichage à l'expérience de marque.</h2>
        <p>Nos réalisations couvrent l'ensemble de la chaîne de valeur : stratégie média, affichage, activation terrain, street marketing, production audiovisuelle, création digitale, architecture événementielle et pilotage de campagne. Chaque projet est conçu pour rapprocher la marque de ses audiences et atteindre un objectif concret.</p>
    </div>
</section>

<section class="travaux">
    {{-- Filtres : la clé de chaque bouton correspond aux valeurs de
         `filtres` déclarées dans CibleController::projets(). Un filtre
         sans réalisation ne serait pas rendu (garde-fou ci-dessous). --}}
    @php
        $filtres = [
            'affichage-regie'         => 'Affichage & régie',
            'brand-experience'        => 'Brand experience',
            'street-marketing'        => 'Street marketing',
            'digital-contenus'        => 'Digital & contenus',
            'production-audiovisuelle'=> 'Production audiovisuelle',
            'design-evenementiel'     => 'Design & événementiel',
        ];
        $utilises = collect($projets)->pluck('filtres')->flatten()->unique()->all();
    @endphp
    <div class="filtres" role="group" aria-label="Filtrer les réalisations">
        <button type="button" class="filtre" data-filtre="toutes" aria-pressed="true">Toutes les réalisations</button>
        @foreach($filtres as $cle => $label)
            @if(in_array($cle, $utilises, true))
                <button type="button" class="filtre" data-filtre="{{ $cle }}" aria-pressed="false">{{ $label }}</button>
            @endif
        @endforeach
    </div>

    <div class="grille rev" id="grille-realisations">
        @foreach($projets as $slug => $p)
            <article class="carte" style="--c:{{ $p['couleur'] }}" data-filtres="{{ implode(' ', $p['filtres']) }}">
                <a href="{{ route('realisation', $slug) }}" style="display:flex;flex-direction:column;height:100%">
                    <div class="vign">
                        @if(file_exists(public_path($p['image'])))
                            <img class="photo" src="{{ asset($p['image']) }}" alt="{{ $p['nom'] }} — {{ $p['cat'] }}" loading="lazy">
                        @else
                            <div class="slot slot--sombre">{{ $p['nom'] }} · {{ $p['cat'] }}<small>{{ $p['image'] }}</small></div>
                        @endif
                    </div>
                    <h3>{{ $p['nom'] }}</h3>
                    <div class="meta"><span class="pastille"></span>{{ $p['cat'] }}</div>
                    <p class="accroche">{{ $p['titre'] }}</p>
                    <p class="desc">{{ $p['texte'] }}</p>
                    <p class="services">{{ $p['services'] }}</p>
                    <span class="lien">Voir le projet<svg viewBox="0 0 177 119"><use href="#ref-fleche"/></svg></span>
                </a>
            </article>
        @endforeach
    </div>
    <p class="filtre-vide" id="filtre-vide" hidden>Aucune réalisation dans cette catégorie pour le moment.</p>
</section>

{{-- ═══ NOTRE IMPACT ═══ --}}
<section class="impact">
    <div class="entete rev">
        <span class="sur">Ce que nos campagnes cherchent à produire</span>
        <h2 class="t1">Chaque réalisation répond à un objectif de marque.</h2>
    </div>
    <div class="impact-grid rev">
        @foreach([
            ['Accroître la visibilité',    "Renforcer la présence d'une marque sur les axes, les territoires et les canaux fréquentés par ses audiences.",          'var(--rouge)'],
            ["Créer de l'engagement",      "Donner aux publics une raison d'interagir, de participer, de tester, de partager ou de se rapprocher de la marque.",     'var(--jaune)'],
            ['Construire la mémorisation', 'Concevoir des messages, des visuels et des expériences suffisamment distinctifs pour rester dans les esprits.',           'var(--vert)'],
            ["Générer de l'action",        "Soutenir un lancement, une visite en point de vente, une prise de contact, une inscription ou une décision d'achat.",     'var(--bleu)'],
        ] as [$t, $d, $c])
            <div class="impact-card" style="--c:{{ $c }}">
                <h4>{{ $t }}</h4>
                <p>{{ $d }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══ MÉTHODE & PREUVES ═══ --}}
<section class="preuves">
    <div class="preuves-wrap">
        <div class="rev">
            <span class="sur">De l'idée à la preuve</span>
            <h2 class="t1">Des campagnes conçues, exécutées et documentées.</h2>
            <p class="body">Selon la nature du projet, nous assurons la stratégie, la création, la production, le déploiement terrain, le suivi des opérations et le reporting. Les campagnes d'affichage peuvent notamment être documentées par des photos horodatées et géolocalisées.</p>
        </div>
        <ul class="preuves-list rev">
            @foreach([
                ['Objectifs et audiences clairement définis',    'var(--rouge)'],
                ['Dispositifs adaptés aux usages et aux territoires', 'var(--jaune)'],
                ['Coordination des équipes et des prestataires', 'var(--vert)'],
                ["Contrôle de l'exécution et reporting",         'var(--bleu)'],
            ] as [$label, $c])
                <li style="--c:{{ $c }}"><i></i>{{ $label }}</li>
            @endforeach
        </ul>
    </div>
</section>

<section class="clients-band">
    <div class="entete rev">
        <span class="sur">Ils nous ont fait confiance</span>
        <h2 class="t1">Des marques, institutions et organisations accompagnées à travers la Côte d'Ivoire.</h2>
        <p>De la visibilité extérieure aux expériences de marque, nous collaborons avec des acteurs issus de secteurs variés pour concevoir des campagnes adaptées à leurs enjeux.</p>
    </div>
    <div class="clients-grid rev">
        @foreach([
            ['file'=>'client-banque-atlantique.png','alt'=>'Banque Atlantique'],
            ['file'=>'client-bgfibank.png','alt'=>'BGFIBank'],
            ['file'=>'client-danone.png','alt'=>'Danone'],
            ['file'=>'client-moov.png','alt'=>'Moov Africa'],
            ['file'=>'client-rimco.png','alt'=>'Rimco Motors'],
            ['file'=>'client-sipra.png','alt'=>'SIPRA'],
            ['file'=>'client-autre-1.png','alt'=>'Client partenaire'],
            ['file'=>'client-autre-2.png','alt'=>'Client partenaire'],
            ['file'=>'client-autre-3.png','alt'=>'Client partenaire'],
            ['file'=>'client-autre-4.png','alt'=>'Client partenaire'],
            ['file'=>'client-autre-5.png','alt'=>'Client partenaire'],
            ['file'=>'client-autre-6.png','alt'=>'Client partenaire'],
        ] as $c)
            <div class="clogo">
                @php $p = public_path('images/cible/' . $c['file']); @endphp
                @if(file_exists($p))
                    <img src="{{ asset('images/cible/' . $c['file']) }}" alt="{{ $c['alt'] }}" loading="lazy">
                @else
                    <div class="clogo-txt">{{ $c['alt'] }}</div>
                @endif
            </div>
        @endforeach
    </div>
</section>

<section class="cta-ref">
    <span class="sur">Et votre prochain projet ?</span>
    <h2 class="t1">Faisons de votre prochaine campagne une réalisation qui compte.</h2>
    <p>Partagez-nous votre objectif, votre audience et votre ambition. Nous imaginerons le dispositif de visibilité, d'activation et de communication le plus pertinent pour votre marque.</p>
    <div style="margin-top:32px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a class="bouton b-rouge" href="{{ route('contact') }}">Parler de mon projet</a>
        <a class="bouton b-ligne" href="{{ route('services') }}">Découvrir nos services</a>
    </div>
</section>

@endsection

@push('page-js')
<script>
(function(){
    const boutons = [...document.querySelectorAll('.filtre')];
    const cartes  = [...document.querySelectorAll('#grille-realisations .carte')];
    const vide    = document.getElementById('filtre-vide');
    if (!boutons.length || !cartes.length) return;

    function filtrer(cle){
        let visibles = 0;
        cartes.forEach(c => {
            const tags = (c.dataset.filtres || '').split(' ');
            const ok = cle === 'toutes' || tags.includes(cle);
            c.hidden = !ok;
            if (ok) visibles++;
        });
        if (vide) vide.hidden = visibles > 0;
    }

    boutons.forEach(b => b.addEventListener('click', () => {
        boutons.forEach(o => o.setAttribute('aria-pressed', String(o === b)));
        filtrer(b.dataset.filtre);
    }));
})();
</script>
@endpush
