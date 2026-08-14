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
    .intro-ref .entete{max-width:860px;margin:0 auto;text-align:center}
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
    .carte .lien i{width:15px;aspect-ratio:282.95/195.83;background-color:currentColor;transition:transform .22s}
    .carte:hover .lien i{transform:translateX(4px)}
    .filtre-vide{text-align:center;color:#888;font-family:var(--titre);font-weight:600;padding:40px 0}

    /* Impact */
    .impact{padding:clamp(56px,8vw,100px) var(--pad);background:var(--noir);color:#fff}
    .impact .entete{max-width:860px;margin:0 auto 48px;text-align:center}
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
    .clients-band .entete{max-width:860px;margin:0 auto 44px;text-align:center}
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

<section class="hero-ref a-decor">
    <x-decor :formes="[
        ['f-fleche', '--c:var(--jaune);--op:.18;top:14%;right:5%;width:120px;--r:-8deg;--dur:20s;--del:.4s'],
        ['f-plume',  '--c:var(--violet);--op:.10;bottom:-8%;left:4%;width:110px;--r:16deg;--dur:27s;--del:.7s'],
    ]" />
    <span class="sur">{{ \App\Support\Contenu::get('references.hero_surtitre') }}</span>
    <h1 class="t1">{{ \App\Support\Contenu::get('references.hero_titre') }}</h1>
    <p>{{ \App\Support\Contenu::get('references.hero_texte') }}</p>
</section>

<section class="intro-ref a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:var(--violet);--op:.13;top:-6%;left:6%;width:140px;--r:14deg;--dur:26s'],
        ['f-plume',  '--c:var(--rouge);--op:.10;bottom:-10%;right:8%;width:120px;--r:-20deg;--dur:31s;--del:.4s'],
        ['f-fleche', '--c:var(--vert);--op:.14;top:22%;right:4%;width:105px;--r:-12deg;--dur:22s;--del:.7s'],
    ]" />
    <div class="entete rev">
        <span class="sur">{{ \App\Support\Contenu::get('references.intro_surtitre') }}</span>
        <h2 class="t1">{{ \App\Support\Contenu::get('references.intro_titre') }}</h2>
        <p>{{ \App\Support\Contenu::get('references.intro_texte') }}</p>
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
                        @if(\App\Support\Contenu::imageExiste($p['image']))
                            <img class="photo" src="{{ \App\Support\Contenu::urlImage($p['image']) }}" alt="{{ $p['nom'] }} — {{ $p['cat'] }}" loading="lazy">
                        @else
                            <div class="slot slot--sombre">{{ $p['nom'] }} · {{ $p['cat'] }}<small>{{ $p['image'] }}</small></div>
                        @endif
                    </div>
                    <h3>{{ $p['nom'] }}</h3>
                    <div class="meta"><span class="pastille"></span>{{ $p['cat'] }}</div>
                    <p class="accroche">{{ $p['titre'] }}</p>
                    <p class="desc">{{ $p['texte'] }}</p>
                    <p class="services">{{ $p['services'] }}</p>
                    <span class="lien">Voir le projet<i class="dessin f-fleche" aria-hidden="true"></i></span>
                </a>
            </article>
        @endforeach
    </div>
    <p class="filtre-vide" id="filtre-vide" hidden>Aucune réalisation dans cette catégorie pour le moment.</p>
</section>

{{-- ═══ NOTRE IMPACT ═══ --}}
<section class="impact a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:#fff;--op:.06;top:5%;left:4%;width:150px;--r:18deg;--dur:30s'],
        ['f-plume',  '--c:#fff;--op:.05;bottom:3%;right:5%;width:120px;--r:-16deg;--dur:34s;--del:.4s'],
        ['f-fleche', '--c:var(--jaune);--op:.14;top:34%;right:12%;width:110px;--r:8deg;--dur:24s;--del:.7s'],
    ]" />
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

<section class="clients-band a-decor">
    <x-decor :formes="[
        ['f-fleche', '--c:var(--vert);--op:.12;top:6%;left:3%;width:115px;--r:-14deg;--dur:25s'],
        ['f-plume',  '--c:var(--bleu);--op:.10;bottom:4%;right:4%;width:110px;--r:20deg;--dur:31s;--del:.5s'],
    ]" />
    <div class="entete rev">
        <span class="sur">{{ \App\Support\Contenu::get('references.cli_surtitre') }}</span>
        <h2 class="t1">{{ \App\Support\Contenu::get('references.cli_titre') }}</h2>
        <p>{{ \App\Support\Contenu::get('references.cli_texte') }}</p>
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

<section class="cta-ref a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:var(--violet);--op:.12;top:10%;left:7%;width:110px;--r:18deg;--dur:28s'],
        ['f-fleche', '--c:var(--rouge);--op:.16;bottom:12%;right:8%;width:120px;--r:-10deg;--dur:21s;--del:.4s'],
    ]" />
    <span class="sur">{{ \App\Support\Contenu::get('references.cta_surtitre') }}</span>
    <h2 class="t1">{{ \App\Support\Contenu::get('references.cta_titre') }}</h2>
    <p>{{ \App\Support\Contenu::get('references.cta_texte') }}</p>
    <div style="margin-top:32px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a class="bouton b-rouge" href="{{ route('contact') }}">{{ \App\Support\Contenu::get('references.cta_bouton1') }}</a>
        <a class="bouton b-ligne" href="{{ route('services') }}">{{ \App\Support\Contenu::get('references.cta_bouton2') }}</a>
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
