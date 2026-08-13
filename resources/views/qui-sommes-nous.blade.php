@extends('_layout', [
    'seo_title'       => 'Qui sommes-nous — CIBLE · 30 ans de communication extérieure',
    'seo_description' => 'CIBLE, régie publicitaire ivoirienne fondée en 1994. Trois décennies au service des marques, trois distinctions officielles de l\'État.',
])

@push('page-css')
    .hero-qui{padding:clamp(60px,8vw,110px) var(--pad);text-align:center;background:linear-gradient(180deg,#fff 0%,#F9F9F5 100%)}
    .hero-qui .sur{color:var(--violet)}
    .hero-qui h1{margin-top:14px}
    .hero-qui p{margin-top:24px;max-width:64ch;margin-left:auto;margin-right:auto;font-size:19px;color:#444}

    .recit{padding:clamp(56px,8vw,100px) var(--pad)}
    .recit-grid{display:grid;grid-template-columns:1fr 1fr;gap:clamp(30px,5vw,80px);align-items:start}
    @media(max-width:900px){.recit-grid{grid-template-columns:1fr}}
    .recit article{padding:26px 0;border-bottom:1px solid #E4E4E4}
    .recit article:last-child{border-bottom:0}
    .recit article h3{font-family:var(--titre);font-weight:800;font-size:22px;margin-bottom:10px;color:var(--rouge)}
    .recit article p{color:#444;line-height:1.7}

    .distinctions{background:var(--gris);padding:clamp(56px,8vw,100px) var(--pad)}
    /* Centrage 2026-08-04 : trop d'espace vide à droite */
    .distinctions .entete{max-width:860px;margin:0 auto 48px;text-align:center}
    .distinctions .sur{color:var(--vert)}
    .distinctions .t1{margin-top:12px}
    .dist-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(18px,3vw,44px)}
    @media(max-width:820px){.dist-grid{grid-template-columns:1fr}}
    .dist{background:#fff;border-radius:16px;padding:28px 26px;border-top:6px solid var(--c)}
    .dist .an{font-family:var(--titre);font-weight:900;font-size:34px;color:var(--c);line-height:1}
    .dist h4{font-family:var(--titre);font-weight:800;font-size:17px;margin-top:8px;line-height:1.35}
    .dist p{font-size:14px;color:#666;margin-top:10px;line-height:1.6}

    .stats-qui{padding:clamp(56px,8vw,100px) var(--pad);background:var(--noir);color:#fff;text-align:center}
    /* Centrage 2026-08-04 (max-width élargi 22→40ch : 22 forçait 3
       lignes serrées avec superposition, 40ch tient sur 1-2 lignes propres) */
    .stats-qui .entete{max-width:860px;margin:0 auto 48px}
    .stats-qui .sur{color:var(--jaune)}
    .stats-qui .t1{color:#fff;margin-top:12px}
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:clamp(20px,3vw,40px)}
    @media(max-width:900px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
    .stat{text-align:center}
    .stat .v{font-family:var(--titre);font-weight:900;font-size:clamp(46px,6vw,80px);line-height:.86;letter-spacing:-.04em;color:var(--jaune)}
    .stat .l{font-family:var(--titre);font-weight:600;font-size:13px;opacity:.85;margin-top:12px;text-transform:uppercase;letter-spacing:.08em}
@endpush

@section('content')

<section class="hero-qui">
    <span class="sur">Depuis 1994</span>
    <h1 class="t1">Plus de trente ans à rendre les marques visibles, fortes et mémorables.</h1>
    <p>Née dans l'affichage publicitaire, CIBLE est devenue un acteur majeur de la communication en Côte d'Ivoire. Nous conjuguons aujourd'hui expertise terrain, créativité et intelligence média pour transformer la visibilité des marques en impact durable.</p>
</section>

<section class="recit">
    <div class="recit-grid">
        <article class="rev">
            <h3>Notre origine — l'expertise de la visibilité</h3>
            <p>CIBLE est née en 1994 avec une conviction forte : une marque ne peut grandir que si elle est vue. Grâce à notre maîtrise de l'affichage publicitaire et à notre connaissance du territoire ivoirien, nous avons progressivement construit un réseau puissant et une expertise reconnue de la communication extérieure et du digital.</p>
        </article>
        <article class="rev">
            <h3>Notre évolution — de l'affichage à la communication intégrée</h3>
            <p>Les audiences, les usages et les attentes des annonceurs ont évolué. CIBLE aussi. À notre expertise historique de l'affichage, nous avons ajouté la stratégie, la création, le digital, la production audiovisuelle, la communication mobile et les activations terrain afin d'accompagner les marques sur l'ensemble de leurs points de contact.</p>
        </article>
        <article class="rev">
            <h3>Notre force — le terrain, la créativité et la donnée</h3>
            <p>Trente ans de connaissance du terrain ivoirien fusionnés avec une approche moderne. La seule régie à posséder son réseau et l'outil qui le pilote.</p>
        </article>
        <article class="rev">
            <h3>Notre engagement — apporter des preuves, pas seulement des promesses</h3>
            <p>Chaque campagne fait l'objet d'un suivi rigoureux : planification des poses, contrôle des emplacements, photos horodatées et géolocalisées, suivi de diffusion et reporting. Nos clients disposent ainsi d'une visibilité claire sur le déploiement réel de leurs campagnes.</p>
        </article>
    </div>
</section>

<section class="stats-qui rev">
    <div class="entete">
        <span class="sur">En chiffres</span>
        <h2 class="t1">Trente ans concentrés en quatre nombres.</h2>
    </div>
    <div class="stats-grid">
        <div class="stat"><div class="v num" data-cible="30">0</div><div class="l">Ans d'expertise</div></div>
        <div class="stat"><div class="v num"><span aria-hidden="true">+</span><span data-cible="400">0</span></div><div class="l">Panneaux en propre</div></div>
        <div class="stat"><div class="v num" data-cible="31">0</div><div class="l">Communes couvertes</div></div>
        <div class="stat"><div class="v num" data-cible="3">0</div><div class="l">Distinctions d'État</div></div>
    </div>
</section>

<section class="distinctions">
    <div class="entete rev">
        <span class="sur">Reconnaissances officielles</span>
        <h2 class="t1">Trois distinctions de l'État ivoirien.</h2>
    </div>
    <div class="dist-grid">
        <div class="dist rev" style="--c:var(--jaune)">
            <div class="an num">2016</div>
            <h4>2ᵉ prix du meilleur publicitaire</h4>
            <p>Distinction professionnelle du secteur de la publicité ivoirienne.</p>
        </div>
        <div class="dist rev" style="--c:var(--vert)">
            <div class="an num">2019</div>
            <h4>Chevalier de l'Ordre du Mérite de la Communication</h4>
            <p>Reconnaissance de la contribution à la structuration du métier en Côte d'Ivoire.</p>
        </div>
        <div class="dist rev" style="--c:var(--violet)">
            <div class="an num">2020</div>
            <h4>Officier de l'Ordre du Mérite National</h4>
            <p>Distinction républicaine pour services rendus au pays.</p>
        </div>
    </div>
</section>

<section style="padding:clamp(60px,8vw,100px) var(--pad);text-align:center">
    <h2 class="t2" style="max-width:24ch;margin:0 auto">Construisons ensemble la prochaine campagne qui fera grandir votre marque.</h2>
    <div style="margin-top:28px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a class="bouton b-rouge" href="{{ route('contact') }}">Parler de mon projet</a>
        <a class="bouton b-ligne" href="{{ route('references') }}">Découvrir nos réalisations</a>
    </div>
</section>

@endsection
