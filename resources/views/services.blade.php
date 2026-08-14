@extends('_layout', [
    'seo_title'       => 'Nos services — CIBLE · Régie, mobile, brand experience, Media Intelligence',
    'seo_description' => 'Quatre pôles complémentaires : régie publicitaire (+400 panneaux, 6 formats), communication mobile, brand experience & communication 360°, Media Intelligence et pilotage de campagne.',
])

@push('page-css')
    /* Hero */
    .hero-serv{padding:clamp(60px,8vw,110px) var(--pad);background:linear-gradient(180deg,#fff,#F9F9F5)}
    .hero-serv .sur{color:var(--bleu)}
    .hero-serv h1{margin-top:14px;max-width:22ch}
    .hero-serv h1 em{color:var(--rouge);font-style:normal}
    .hero-serv p{margin-top:24px;max-width:62ch;font-size:19px;color:#444}
    .hero-serv .accroche{margin-top:16px;max-width:58ch;font-size:16px;color:#666}
    .hero-serv .actions{margin-top:32px;display:flex;gap:12px;flex-wrap:wrap}

    /* Section d'introduction — notre modèle */
    .approche{padding:clamp(56px,8vw,100px) var(--pad);background:var(--gris)}
    .approche .entete{max-width:860px;margin:0 auto 48px;text-align:center}
    .approche .sur{color:var(--violet)}
    .approche .t1{margin-top:12px}
    .approche .entete p{margin-top:20px;color:#444}
    .preuves-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(18px,3vw,32px)}
    @media(max-width:820px){.preuves-grid{grid-template-columns:1fr}}
    .preuve-card{background:#fff;border-radius:16px;padding:30px 28px;border-top:6px solid var(--c)}
    .preuve-card h3{font-family:var(--titre);font-weight:800;font-size:20px;color:var(--c);margin-bottom:10px}
    .preuve-card p{font-size:14.5px;color:#555;line-height:1.6}

    /* Pôles — sections principales */
    .pole{padding:clamp(60px,8vw,110px) var(--pad);border-top:8px solid var(--c);position:relative;overflow:hidden}
    .pole-tag{display:inline-block;background:var(--c);color:#fff;padding:6px 14px;border-radius:999px;font-family:var(--titre);font-weight:800;font-size:11px;letter-spacing:.14em;text-transform:uppercase;margin-bottom:16px}
    .pole h2{font-family:var(--titre);font-weight:900;font-size:clamp(28px,3.6vw,44px);line-height:1.08;letter-spacing:-.025em}
    /* em conservé pour l'historique, strong = emphase saisie dans l'admin */
    .pole h2 em,
    .pole h2 strong{color:var(--c);font-style:normal}
    .pole > .wrap{display:grid;grid-template-columns:1fr 1fr;gap:clamp(30px,5vw,80px);align-items:start;max-width:1300px;margin:0 auto}
    .pole.rtl > .wrap{direction:rtl}
    .pole.rtl > .wrap > *{direction:ltr}
    @media(max-width:900px){.pole > .wrap{grid-template-columns:1fr}.pole.rtl > .wrap{direction:ltr}}
    .pole p.body{margin-top:18px;max-width:52ch;line-height:1.7;color:#333;font-size:16px}
    .pole p.accroche{margin-top:16px;max-width:52ch;line-height:1.65;color:#555;font-size:15px;padding-left:16px;border-left:3px solid var(--c)}
    .pole .visu-col{display:flex;flex-direction:column;gap:20px}
    .pole .visu{aspect-ratio:4/5;border-radius:22px;overflow:hidden;background:var(--gris)}
    .pole .visu img{width:100%;height:100%;object-fit:cover}

    /* Bloc formats (pôle 1) */
    .block-h3{font-family:var(--titre);font-weight:800;font-size:20px;margin-top:32px;color:#111}
    .formats-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px}
    @media(max-width:520px){.formats-grid{grid-template-columns:1fr}}
    .format-tile{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:16px 18px;background:#fff;border:1.5px solid var(--gris);border-radius:12px;transition:border-color .2s,transform .2s}
    .format-tile:hover{border-color:var(--c);transform:translateY(-2px)}
    .format-tile strong{display:block;font-family:var(--titre);font-weight:800;font-size:15px;color:#111}
    .format-tile small{display:block;color:#666;font-size:13px;margin-top:2px}
    .format-tile span{font-family:var(--titre);font-weight:900;font-size:15px;color:var(--c);white-space:nowrap}

    /* Dispositifs mini (pôle 1 côté visuel) */
    .dispositifs-mini{background:#111;color:#fff;border-radius:16px;padding:24px 26px}
    .dispositifs-mini h4{font-family:var(--titre);font-weight:800;font-size:16px;color:var(--jaune);margin-bottom:14px}
    .dispositifs-mini ul{list-style:none}
    .dispositifs-mini li{padding:8px 0;border-bottom:1px dashed rgba(255,255,255,.15);font-size:14px;position:relative;padding-left:22px}
    .dispositifs-mini li::before{content:"▸";position:absolute;left:0;color:var(--jaune);font-weight:800}
    .dispositifs-mini li:last-child{border-bottom:0}

    /* Liste dispositifs (pôle 2) */
    .dispositifs-list{display:flex;flex-direction:column;gap:18px;margin-top:28px}
    .dispositifs-list > div{display:grid;grid-template-columns:60px 1fr;gap:18px;padding:20px;background:#fff;border-radius:14px;border:1px solid var(--gris);transition:border-color .2s,transform .2s}
    .dispositifs-list > div:hover{border-color:var(--c);transform:translateX(4px)}
    .dispositifs-list > div > span{font-size:32px;text-align:center;line-height:1}
    .dispositifs-list strong{font-family:var(--titre);font-weight:800;font-size:16px;color:#111;display:block;margin-bottom:6px}
    .dispositifs-list p{font-size:14px;color:#555;line-height:1.55}

    /* Offres (pôles 3 et 4) */
    .offer-list{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:28px}
    @media(max-width:640px){.offer-list{grid-template-columns:1fr}}
    .offer-item{padding:22px 20px;background:var(--gris);border-radius:14px;border-left:4px solid var(--c);transition:transform .2s,box-shadow .2s}
    .offer-item:hover{transform:translateY(-3px);box-shadow:0 12px 24px -12px rgba(0,0,0,.12)}
    .offer-item h4{font-family:var(--titre);font-weight:800;font-size:16px;margin-bottom:8px;color:#111}
    .offer-item p{font-size:14px;color:#555;line-height:1.55}

    /* Section phygitale — 4 temps du parcours */
    .phygital{padding:clamp(56px,8vw,100px) var(--pad)}
    .phygital .entete{max-width:860px;margin:0 auto 52px;text-align:center}
    .phygital .sur{color:var(--vert)}
    .phygital .t1{margin-top:12px}
    .phygital .entete p{margin-top:20px;color:#444}
    .phy-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:clamp(14px,2vw,24px)}
    @media(max-width:900px){.phy-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:520px){.phy-grid{grid-template-columns:1fr}}
    .phy-step{position:relative;padding:28px 24px;border-radius:16px;background:var(--c);color:#fff;overflow:hidden}
    .phy-step .n{font-family:var(--titre);font-weight:900;font-size:52px;line-height:1;opacity:.28}
    .phy-step h4{font-family:var(--titre);font-weight:900;font-size:21px;margin-top:6px}
    .phy-step p{font-size:14px;line-height:1.55;margin-top:10px;opacity:.95}

    /* Workflow */
    .workflow{background:var(--noir);color:#fff;padding:clamp(60px,8vw,110px) var(--pad)}
    /* Centrage 2026-08-04 : cohérence avec les autres entêtes du site */
    .workflow .entete{max-width:860px;margin:0 auto 60px;text-align:center}
    .workflow .sur{color:var(--jaune)}
    .workflow h2{font-family:var(--titre);font-weight:900;font-size:clamp(28px,3.8vw,50px);line-height:1.05;letter-spacing:-.025em;color:#fff;margin-top:12px}
    .workflow h2 em{color:var(--jaune);font-style:normal}
    .workflow p{margin-top:20px;color:rgba(255,255,255,.75);font-size:17px;max-width:56ch;margin-left:auto;margin-right:auto}
    .workflow-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
    @media(max-width:900px){.workflow-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:500px){.workflow-grid{grid-template-columns:1fr}}
    .wf-step{padding:24px 22px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:14px;transition:transform .25s,border-color .25s;position:relative;overflow:hidden}
    .wf-step:hover{transform:translateY(-4px);border-color:var(--c)}
    .wf-step::before{content:"";position:absolute;top:0;left:0;right:0;height:3px;background:var(--c);transform:scaleX(0);transform-origin:left;transition:transform .3s}
    .wf-step:hover::before{transform:scaleX(1)}
    .wf-step .n{font-family:var(--titre);font-weight:900;font-size:40px;color:var(--c);line-height:1;margin-bottom:14px}
    .wf-step h4{font-family:var(--titre);font-weight:800;font-size:17px;margin-bottom:8px;color:#fff}
    .wf-step p{font-size:13.5px;color:rgba(255,255,255,.7);line-height:1.55;margin-top:0;max-width:none}

    /* Section objectifs */
    .objectifs{padding:clamp(56px,8vw,100px) var(--pad);background:var(--gris)}
    .objectifs .entete{max-width:860px;margin:0 auto 44px;text-align:center}
    .objectifs .sur{color:var(--rouge)}
    .objectifs .t1{margin-top:12px}
    .obj-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:1000px;margin:0 auto}
    @media(max-width:700px){.obj-grid{grid-template-columns:1fr}}
    .obj-item{display:flex;align-items:center;gap:14px;background:#fff;border-radius:12px;padding:18px 20px;font-family:var(--titre);font-weight:700;font-size:15px;color:#222;transition:transform .2s}
    .obj-item:hover{transform:translateX(4px)}
    .obj-item i{width:10px;height:10px;border-radius:50%;background:var(--c);flex-shrink:0}

    /* CTA */
    .cta-serv{padding:clamp(60px,8vw,120px) var(--pad);text-align:center}
    .cta-serv .sur{color:var(--rouge)}
    .cta-serv .t1{max-width:24ch;margin:14px auto 0}
    .cta-serv p{margin-top:20px;max-width:58ch;margin-left:auto;margin-right:auto;color:#666}

    /* Filigrane de pôle (2026-08-13) : une forme géante et très pâle dans
       la couleur du pôle, ancrée en bord de section. Les 4 pôles se
       ressemblaient trop — c'est ce qui donne le rythme visuel de la page
       et permet de repérer d'un coup d'œil dans quel pôle on se trouve. */
    .pole > .filigrane{width:clamp(260px,34vw,460px);--c:var(--c);--op:.055}
    .pole:not(.rtl) > .filigrane{right:-6%;top:8%}
    .pole.rtl > .filigrane{left:-6%;top:8%}
    @media(max-width:900px){.pole > .filigrane{opacity:.035}}
@endpush

@section('content')

<section class="hero-serv a-decor">
    <x-decor :formes="[
        ['f-fleche', '--c:var(--bleu);--op:.16;top:12%;right:5%;width:120px;--r:-8deg;--dur:20s;--del:.4s'],
        ['f-plume',  '--c:var(--rouge);--op:.10;bottom:-8%;left:4%;width:110px;--r:16deg;--dur:26s;--del:.7s'],
    ]" />
    <span class="sur">{{ \App\Support\Contenu::get('services.hero_surtitre') }}</span>
    <h1 class="t1">{{ \App\Support\Contenu::get('services.hero_titre_1') }} <em>{{ \App\Support\Contenu::get('services.hero_titre_2') }}</em></h1>
    <p>{{ \App\Support\Contenu::get('services.hero_texte') }}</p>
    <p class="accroche">{{ \App\Support\Contenu::get('services.hero_accroche') }}</p>
    <div class="actions">
        <a class="bouton b-rouge" href="{{ route('contact') }}">{{ \App\Support\Contenu::get('services.hero_cta1') }}</a>
        <a class="bouton b-ligne" href="{{ route('references') }}">{{ \App\Support\Contenu::get('services.hero_cta2') }}</a>
    </div>
</section>

{{-- ═══════════════════ NOTRE APPROCHE ═══════════════════ --}}
<section class="approche a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:var(--violet);--op:.11;top:4%;left:3%;width:130px;--r:12deg;--dur:28s'],
        ['f-fleche', '--c:var(--vert);--op:.13;bottom:6%;right:4%;width:110px;--r:-18deg;--dur:22s;--del:.5s'],
    ]" />
    <div class="entete rev">
        <span class="sur">{{ \App\Support\Contenu::get('services.approche_surtitre') }}</span>
        <h2 class="t1">{{ \App\Support\Contenu::get('services.approche_titre') }}</h2>
        <p>{{ \App\Support\Contenu::get('services.approche_texte') }}</p>
    </div>
    <div class="preuves-grid">
        <div class="preuve-card rev" style="--c:var(--rouge)">
            <h3>{{ \App\Support\Contenu::get('services.approche_1_titre') }}</h3>
            <p>{{ \App\Support\Contenu::get('services.approche_1_texte') }}</p>
        </div>
        <div class="preuve-card rev" style="--c:var(--jaune)">
            <h3>{{ \App\Support\Contenu::get('services.approche_2_titre') }}</h3>
            <p>{{ \App\Support\Contenu::get('services.approche_2_texte') }}</p>
        </div>
        <div class="preuve-card rev" style="--c:var(--bleu)">
            <h3>{{ \App\Support\Contenu::get('services.approche_3_titre') }}</h3>
            <p>{{ \App\Support\Contenu::get('services.approche_3_texte') }}</p>
        </div>
    </div>
</section>

{{-- ═══════════════════ PÔLE 01 · RÉGIE ═══════════════════ --}}
<section id="regie" class="pole a-decor" style="--c:var(--rouge)">
    <span class="filigrane dessin f-fleche" aria-hidden="true" style="transform:rotate(12deg)"></span>
    <div class="wrap">
        <div class="rev">
            <span class="pole-tag">{{ \App\Support\Contenu::get('services.p1_tag') }}</span>
            {{-- riche() : escape tout puis ne réintroduit que <strong>, à partir
                 des **astérisques** saisies dans l'admin. Cf. Contenu::riche(). --}}
            <h2>{!! \App\Support\Contenu::riche('services.p1_titre') !!}</h2>
            <p class="body">{!! \App\Support\Contenu::riche('services.p1_texte') !!}</p>
            <p class="accroche">{{ \App\Support\Contenu::get('services.p1_accroche') }}</p>

            <h3 class="block-h3">Formats disponibles</h3>
            <p style="color:#666;font-size:14.5px;margin-top:6px">Du mobilier urbain de proximité aux dispositifs panoramiques, nous adaptons le format à votre message, à votre audience et à votre ambition de visibilité.</p>

            <div class="formats-grid">
                @foreach([
                    ['Petit format',        '2×1m à 5×2m',     '2 → 10 m²'],
                    ['Classique',           '4×3m',            '12 m²'],
                    ['Grande dimension',    '6×3m à 6×4m',     '18 → 24 m²'],
                    ['Grand format',        '8×4m à 6×6m',     '32 → 36 m²'],
                    ['Très grand format',   '10×5m à 9×6m',    '50 → 54 m²'],
                    ['Panoramique',         '14×5m',           '70 m²'],
                ] as [$name, $dim, $surface])
                    <div class="format-tile">
                        <div>
                            <strong>{{ $name }}</strong>
                            <small>{{ $dim }}</small>
                        </div>
                        <span>{{ $surface }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="visu-col rev">
            <div class="visu">
                @php $img = \App\Support\Contenu::get('services.p1_image'); @endphp
                @if(\App\Support\Contenu::imageExiste($img))
                    <img src="{{ \App\Support\Contenu::urlImage($img) }}" alt="Panneau publicitaire CIBLE — pôle régie" loading="lazy">
                @else
                    <div class="slot">Panneau publicitaire CIBLE<small>{{ $img }}</small></div>
                @endif
            </div>
            <div class="dispositifs-mini">
                <h4>Nos dispositifs d'affichage</h4>
                <ul>
                    @foreach(\App\Support\Contenu::lignes('services.p1_dispositifs') as $d)
                        <li>{{ $d }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ PÔLE 02 · MOBILE ═══════════════════ --}}
<section id="mobile" class="pole rtl a-decor" style="--c:var(--jaune);background:#FFFCF3">
    <span class="filigrane dessin f-plume" aria-hidden="true" style="transform:rotate(-14deg)"></span>
    <div class="wrap">
        <div class="rev">
            <span class="pole-tag" style="color:#111">{{ \App\Support\Contenu::get('services.p2_tag') }}</span>
            <h2>{!! \App\Support\Contenu::riche('services.p2_titre') !!}</h2>
            <p class="body">{{ \App\Support\Contenu::get('services.p2_texte') }}</p>
            <p class="accroche">{{ \App\Support\Contenu::get('services.p2_accroche') }}</p>

            <div class="dispositifs-list">
                <div><span>🚛</span><div><strong>Camions publicitaires</strong><p>Des surfaces d'affichage mobiles à fort impact pour accompagner un lancement, annoncer une opération ou occuper plusieurs zones au cours d'une même campagne.</p></div></div>
                <div><span>🏍</span><div><strong>Motos publicitaires</strong><p>Des dispositifs agiles pour circuler dans les zones denses, toucher les quartiers de proximité et accéder aux espaces difficiles à couvrir avec de grands véhicules.</p></div></div>
                <div><span>🚗</span><div><strong>Branding véhicules</strong><p>Habillage partiel ou intégral de véhicules d'entreprise, de flottes commerciales ou de véhicules spécifiquement dédiés à une campagne.</p></div></div>
                <div><span>🚕</span><div><strong>Branding taxis &amp; cars</strong><p>Une présence répétée dans les flux urbains pour renforcer la mémorisation et augmenter la fréquence d'exposition au message.</p></div></div>
                <div><span>🪧</span><div><strong>Chevalets et dispositifs de proximité</strong><p>Des supports tactiques déployés autour des marchés, points de vente, événements et lieux de forte affluence.</p></div></div>
                <div><span>🚐</span><div><strong>Roadshows et caravanes</strong><p>Des campagnes itinérantes combinant visibilité mobile, animation, prise de parole et interaction directe avec les publics.</p></div></div>
            </div>
        </div>
        <div class="visu-col rev">
            <div class="visu">
                @php $img = \App\Support\Contenu::get('services.p2_image'); @endphp
                @if(\App\Support\Contenu::imageExiste($img))
                    <img src="{{ \App\Support\Contenu::urlImage($img) }}" alt="Camion publicitaire CIBLE — écran LED mobile" loading="lazy">
                @else
                    <div class="slot">Camion publicitaire CIBLE<small>{{ $img }}</small></div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ PÔLE 03 · BRAND EXPERIENCE ═══════════════════ --}}
<section id="globale" class="pole a-decor" style="--c:var(--violet)">
    <span class="filigrane dessin f-plume" aria-hidden="true" style="transform:rotate(12deg)"></span>
    <div class="wrap">
        <div class="rev">
            <span class="pole-tag">{{ \App\Support\Contenu::get('services.p3_tag') }}</span>
            <h2>{!! \App\Support\Contenu::riche('services.p3_titre') !!}</h2>
            <p class="body">{{ \App\Support\Contenu::get('services.p3_texte') }}</p>

            {{-- 8 offres. Le document source numérote deux fois "OFFRE.4" et
                 saute "OFFRE.7" : l'ordre retenu ici suit celui du document,
                 la numérotation d'origine n'étant pas affichée à l'écran. --}}
            <div class="offer-list">
                @foreach([
                    ['Création graphique &amp; identité visuelle', 'Conception de campagnes, créations publicitaires, adaptations aux différents formats, supports print et contenus digitaux.'],
                    ['Stratégie de communication', 'Définition du concept, des messages, des audiences, des canaux et du plan de déploiement selon vos objectifs de notoriété, d\'engagement ou de conversion.'],
                    ['Street marketing', 'Distributions ciblées, animations commerciales, échantillonnage, campagnes de proximité et dispositifs d\'interaction avec les consommateurs.'],
                    ['Digital &amp; réseaux sociaux', 'Campagnes sociales, contenus, community management, activations digitales, amplification en ligne et dispositifs drive-to-store.'],
                    ['Relations presse &amp; influence', 'Coordination des prises de parole, relations avec les médias, dispositifs d\'influence et amplification des temps forts de la campagne.'],
                    ['Production audiovisuelle', 'Films institutionnels, spots publicitaires, capsules digitales, contenus de marque, motion design et productions audio.'],
                    ['Architecture événementielle', 'Conception et réalisation de stands, espaces de marque, corners, signalétique et dispositifs événementiels.'],
                    ['Activations et expériences de marque', 'Pop-up stores, roadshows, stands expérientiels, lancements, dispositifs immersifs et animations permettant au public de découvrir, tester et vivre la marque.'],
                ] as [$name, $desc])
                    <div class="offer-item">
                        <h4>{!! $name !!}</h4>
                        <p>{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="visu-col rev">
            <div class="visu">
                @php $img = \App\Support\Contenu::get('services.p3_image'); @endphp
                @if(\App\Support\Contenu::imageExiste($img))
                    <img src="{{ \App\Support\Contenu::urlImage($img) }}" alt="Activation de marque CIBLE — stand expérientiel" loading="lazy">
                @else
                    <div class="slot slot--sombre">Activation de marque CIBLE<small>{{ $img }}</small></div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ PÔLE 04 · MEDIA INTELLIGENCE ═══════════════════ --}}
<section id="media-intelligence" class="pole rtl a-decor" style="--c:var(--bleu);background:#F7FAFD">
    <span class="filigrane dessin f-fleche" aria-hidden="true" style="transform:rotate(12deg)"></span>
    <div class="wrap">
        <div class="rev">
            <span class="pole-tag">{{ \App\Support\Contenu::get('services.p4_tag') }}</span>
            <h2>{!! \App\Support\Contenu::riche('services.p4_titre') !!}</h2>
            <p class="body">{{ \App\Support\Contenu::get('services.p4_texte') }}</p>
            <p class="accroche">{{ \App\Support\Contenu::get('services.p4_accroche') }}</p>

            <div class="offer-list">
                @foreach([
                    ['Inventaire média digitalisé', 'Visualisation et référencement des emplacements selon les communes, les axes, les formats, les disponibilités et les caractéristiques des supports.'],
                    ['Sélection intelligente des emplacements', 'Recommandation des zones et dispositifs selon les objectifs de campagne, les audiences visées, les flux et les contraintes budgétaires.'],
                    ['Suivi terrain digitalisé', 'Contrôle du déploiement grâce à des photos horodatées et géolocalisées, associées à chaque opération de pose.'],
                    ['Reporting de campagne', 'Centralisation des preuves de diffusion, synthèse des emplacements activés et partage d\'un bilan clair aux équipes annonceurs.'],
                    ['Espace client', 'Accès à une vue consolidée des campagnes en cours, des supports mobilisés et des éléments de suivi disponibles.'],
                    ['Analyse et optimisation', 'Exploitation progressive des données de campagne pour améliorer les choix de zones, de formats, de périodes et de dispositifs.'],
                ] as [$name, $desc])
                    <div class="offer-item">
                        <h4>{{ $name }}</h4>
                        <p>{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="visu-col rev">
            <div class="visu">
                @php $img = \App\Support\Contenu::get('services.p4_image'); @endphp
                @if(\App\Support\Contenu::imageExiste($img))
                    <img src="{{ \App\Support\Contenu::urlImage($img) }}" alt="Suivi terrain d'un panneau CIBLE" loading="lazy">
                @else
                    <div class="slot">Suivi terrain CIBLE<small>{{ $img }}</small></div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ CAMPAGNES PHYGITALES ═══════════════════ --}}
<section class="phygital">
    <div class="entete rev">
        <span class="sur">{{ \App\Support\Contenu::get('services.phy_surtitre') }}</span>
        <h2 class="t1">{{ \App\Support\Contenu::get('services.phy_titre') }}</h2>
        <p>{{ \App\Support\Contenu::get('services.phy_texte') }}</p>
    </div>
    <div class="phy-grid rev">
        @foreach([
            ['01', 'Attirer',  'Créer la visibilité et capter l\'attention grâce aux dispositifs outdoor, mobiles et digitaux.',              'var(--rouge)'],
            ['02', 'Engager',  'Faire participer les publics grâce aux activations, aux animations, aux contenus et aux expériences interactives.', 'var(--jaune)'],
            ['03', 'Convertir','Générer du trafic, des prises de contact, des visites en point de vente, des essais ou des actions mesurables.',    'var(--vert)'],
            ['04', 'Amplifier','Prolonger l\'expérience en ligne grâce aux réseaux sociaux, aux contenus et aux dispositifs de partage.',          'var(--violet)'],
        ] as [$n, $t, $d, $c])
            <div class="phy-step" style="--c:{{ $c }}{{ $c === 'var(--jaune)' ? ';color:#111' : '' }}">
                <div class="n">{{ $n }}</div>
                <h4>{{ $t }}</h4>
                <p>{{ $d }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══════════════════ WORKFLOW ═══════════════════ --}}
<section class="workflow a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:#fff;--op:.06;top:5%;left:5%;width:150px;--r:20deg;--dur:30s'],
        ['f-plume',  '--c:#fff;--op:.05;bottom:4%;right:6%;width:120px;--r:-18deg;--dur:34s;--del:.5s'],
        ['f-fleche', '--c:var(--jaune);--op:.14;top:30%;right:12%;width:110px;--r:6deg;--dur:24s;--del:.8s'],
    ]" />
    <div class="entete rev">
        <span class="sur">{{ \App\Support\Contenu::get('services.work_surtitre') }}</span>
        <h2>{{ \App\Support\Contenu::get('services.work_titre_1') }} <em>{{ \App\Support\Contenu::get('services.work_titre_2') }}</em></h2>
        <p>{{ \App\Support\Contenu::get('services.work_intro') }}</p>
    </div>

    <div class="workflow-grid">
        @foreach([
            ['1', 'Cadrage',                  'Nous clarifions vos objectifs, vos cibles, vos zones prioritaires, votre calendrier et votre budget.',              'var(--rouge)'],
            ['2', 'Analyse',                  'Nous étudions les points de contact, les emplacements, les formats et les dispositifs les plus pertinents.',        'var(--jaune)'],
            ['3', 'Recommandation',           'Nous construisons une stratégie de visibilité et d\'activation adaptée à votre campagne.',                           'var(--vert)'],
            ['4', 'Création',                 'Nous développons ou adaptons les visuels, les contenus et l\'expérience de marque.',                                 'var(--bleu)'],
            ['5', 'Planification',            'Nous organisons les équipes, les supports, les itinéraires, les délais et les opérations terrain.',                  'var(--violet)'],
            ['6', 'Déploiement',              'Nos équipes assurent la pose, la diffusion, l\'animation et l\'exécution opérationnelle de la campagne.',            'var(--rouge)'],
            ['7', 'Contrôle',                 'Chaque opération est suivie et documentée par des preuves terrain horodatées et géolocalisées.',                     'var(--jaune)'],
            ['8', 'Reporting &amp; Optimisations','Nous partageons le bilan de la campagne et identifions les leviers d\'amélioration pour les prochaines activations.', 'var(--vert)'],
        ] as [$n, $t, $d, $c])
            <div class="wf-step" style="--c:{{ $c }}">
                <div class="n">{{ $n }}</div>
                <h4>{!! $t !!}</h4>
                <p>{{ $d }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══════════════════ POUR QUELS OBJECTIFS ═══════════════════ --}}
<section class="objectifs a-decor">
    <x-decor :formes="[
        ['f-fleche', '--c:var(--rouge);--op:.12;top:6%;left:4%;width:120px;--r:-12deg;--dur:25s'],
        ['f-plume',  '--c:var(--jaune);--op:.14;bottom:5%;right:3%;width:115px;--r:22deg;--dur:31s;--del:.4s'],
    ]" />
    <div class="entete rev">
        <span class="sur">{{ \App\Support\Contenu::get('services.obj_surtitre') }}</span>
        <h2 class="t1">{{ \App\Support\Contenu::get('services.obj_titre') }}</h2>
    </div>
    <div class="obj-grid rev">
        @php
                // Cycle de 5 tons : reproduit la séquence d'origine et
                // supporte n'importe quel nombre de lignes saisi dans l'admin.
                $tons = ['var(--rouge)','var(--jaune)','var(--vert)','var(--bleu)','var(--violet)'];
            @endphp
        @foreach(\App\Support\Contenu::lignes('services.obj_liste') as $i => $label)
            <div class="obj-item" style="--c:{{ $tons[$i % 5] }}"><i></i>{{ $label }}</div>
        @endforeach
    </div>
</section>

{{-- ═══════════════════ CTA ═══════════════════ --}}
<section class="cta-serv a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:var(--violet);--op:.12;top:10%;left:7%;width:110px;--r:18deg;--dur:27s'],
        ['f-fleche', '--c:var(--rouge);--op:.16;bottom:12%;right:8%;width:120px;--r:-10deg;--dur:21s;--del:.4s'],
    ]" />
    <span class="sur">{{ \App\Support\Contenu::get('services.cta_surtitre') }}</span>
    <h2 class="t1">{{ \App\Support\Contenu::get('services.cta_titre') }}</h2>
    <p>{{ \App\Support\Contenu::get('services.cta_texte') }}</p>
    <div style="margin-top:32px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a class="bouton b-rouge" href="{{ route('contact') }}">{{ \App\Support\Contenu::get('services.cta_bouton1') }}</a>
        <a class="bouton b-ligne" href="{{ route('references') }}">{{ \App\Support\Contenu::get('services.cta_bouton2') }}</a>
    </div>
</section>

@endsection
