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
    .approche .entete{max-width:720px;margin:0 auto 48px;text-align:center}
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
    .pole h2 em{color:var(--c);font-style:normal}
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
    .phygital .entete{max-width:720px;margin:0 auto 52px;text-align:center}
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
    .workflow .entete{max-width:720px;margin:0 auto 60px;text-align:center}
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
    .objectifs .entete{max-width:680px;margin:0 auto 44px;text-align:center}
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
@endpush

@section('content')

<section class="hero-serv">
    <span class="sur">Nos expertises</span>
    <h1 class="t1">De la visibilité à l'expérience. <em>De l'expérience à l'impact.</em></h1>
    <p>CIBLE conçoit et déploie des campagnes qui rendent les marques visibles, présentes et mémorables. Affichage, communication mobile, digital, street marketing, création de contenus et Media Intelligence : nous connectons les annonceurs à leurs audiences, dans la rue comme en ligne.</p>
    <p class="accroche">Un seul partenaire pour imaginer votre stratégie, activer les bons points de contact, déployer votre campagne et en suivre l'exécution.</p>
    <div class="actions">
        <a class="bouton b-rouge" href="{{ route('contact') }}">Construire ma campagne</a>
        <a class="bouton b-ligne" href="{{ route('references') }}">Découvrir nos réalisations</a>
    </div>
</section>

{{-- ═══════════════════ NOTRE APPROCHE ═══════════════════ --}}
<section class="approche">
    <div class="entete rev">
        <span class="sur">Notre approche</span>
        <h2 class="t1">Créer une rencontre entre votre marque et ses publics.</h2>
        <p>Une campagne performante ne consiste pas seulement à diffuser un message. Elle doit apparaître au bon endroit, au bon moment, dans le bon format et susciter une réaction. Nous combinons patrimoine média, connaissance du terrain, créativité, activation et données pour construire des expériences de marque capables de générer visibilité, engagement et impact.</p>
    </div>
    <div class="preuves-grid">
        <div class="preuve-card rev" style="--c:var(--rouge)">
            <h3>Être vu</h3>
            <p>Positionner votre marque sur des emplacements, des supports et des canaux adaptés à vos audiences.</p>
        </div>
        <div class="preuve-card rev" style="--c:var(--jaune)">
            <h3>Être vécu</h3>
            <p>Transformer une prise de parole en interaction grâce au street marketing, aux activations et aux expériences digitales.</p>
        </div>
        <div class="preuve-card rev" style="--c:var(--bleu)">
            <h3>Être mesuré</h3>
            <p>Suivre le déploiement de la campagne, documenter son exécution et exploiter les données disponibles pour améliorer les prochaines actions.</p>
        </div>
    </div>
</section>

{{-- ═══════════════════ PÔLE 01 · RÉGIE ═══════════════════ --}}
<section id="regie" class="pole" style="--c:var(--rouge)">
    <div class="wrap">
        <div class="rev">
            <span class="pole-tag">Pôle 01 · Régie publicitaire &amp; visibilité extérieure</span>
            <h2>La puissance d'un <em>réseau national</em> au service de votre visibilité.</h2>
            <p class="body">
                Le plus grand maillage territorial. Avec <strong>+400 panneaux en exploitation</strong>
                à Abidjan et à l'intérieur du pays, CIBLE accompagne les annonceurs dans la sélection
                des emplacements, des formats et des zones les plus pertinents au regard de leurs
                objectifs, de leurs audiences et de leur budget.
            </p>
            <p class="accroche">Notre rôle ne se limite pas à mettre un espace à disposition : nous construisons des plans de visibilité cohérents avec les déplacements, les habitudes et les zones de concentration de vos cibles.</p>

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
                @php $img = 'images/cible/regie-lumipub.jpg'; @endphp
                @if(file_exists(public_path($img)))
                    <img src="{{ asset($img) }}" alt="Panneau publicitaire CIBLE — pôle régie" loading="lazy">
                @else
                    <div class="slot">Panneau publicitaire CIBLE<small>images/cible/regie-lumipub.jpg</small></div>
                @endif
            </div>
            <div class="dispositifs-mini">
                <h4>Nos dispositifs d'affichage</h4>
                <ul>
                    <li>Panneaux classiques</li>
                    <li>Lumipub (caissons éclairés)</li>
                    <li>Trivision (3 visuels en rotation)</li>
                    <li>Panoramiques grand format</li>
                    <li>Écrans digitaux</li>
                    <li>Écrans en magasins</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ PÔLE 02 · MOBILE ═══════════════════ --}}
<section id="mobile" class="pole rtl" style="--c:var(--jaune);background:#FFFCF3">
    <div class="wrap">
        <div class="rev">
            <span class="pole-tag" style="color:#111">Pôle 02 · Communication mobile &amp; présence urbaine</span>
            <h2>Votre message ne reste pas immobile. <em>Il va à la rencontre de son audience.</em></h2>
            <p class="body">
                La communication mobile transforme les rues, les axes de circulation, les marchés,
                les quartiers et les événements en espaces d'expression pour votre marque. Nous
                concevons des dispositifs capables d'aller vers les publics, de multiplier les points
                de contact et d'amplifier la couverture d'une campagne.
            </p>
            <p class="accroche">Une solution particulièrement adaptée aux lancements, opérations promotionnelles, ouvertures de points de vente, campagnes de proximité et prises de parole événementielles.</p>

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
                @php $img = 'images/cible/mobile-camion.jpg'; @endphp
                @if(file_exists(public_path($img)))
                    <img src="{{ asset($img) }}" alt="Camion publicitaire CIBLE — écran LED mobile" loading="lazy">
                @else
                    <div class="slot">Camion publicitaire CIBLE<small>images/cible/mobile-camion.jpg</small></div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ PÔLE 03 · BRAND EXPERIENCE ═══════════════════ --}}
<section id="globale" class="pole" style="--c:var(--violet)">
    <div class="wrap">
        <div class="rev">
            <span class="pole-tag">Pôle 03 · Brand experience &amp; communication intégrée</span>
            <h2>Faire voir votre marque. <em>Mais surtout, la faire vivre.</em></h2>
            <p class="body">
                Nous créons des expériences capables de rapprocher les marques de leurs publics.
                Dans la rue, sur un événement, en point de vente ou en ligne, chaque activation
                est pensée pour susciter l'attention, provoquer l'interaction et laisser une
                empreinte durable.
            </p>

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
                @php $img = 'images/cible/campagne-5.jpg'; @endphp
                @if(file_exists(public_path($img)))
                    <img src="{{ asset($img) }}" alt="Activation de marque CIBLE — stand expérientiel" loading="lazy">
                @else
                    <div class="slot slot--sombre">Activation de marque CIBLE<small>images/cible/campagne-5.jpg</small></div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ PÔLE 04 · MEDIA INTELLIGENCE ═══════════════════ --}}
<section id="media-intelligence" class="pole rtl" style="--c:var(--bleu);background:#F7FAFD">
    <div class="wrap">
        <div class="rev">
            <span class="pole-tag">Pôle 04 · Media Intelligence</span>
            <h2>Une visibilité <em>pilotée par la donnée</em> et renforcée par la preuve.</h2>
            <p class="body">
                La Media Intelligence permet de passer d'une logique de diffusion à une logique
                de pilotage. Nos outils centralisent les informations liées aux emplacements, aux
                poses, aux périodes de diffusion et au suivi terrain afin d'apporter davantage de
                transparence, de contrôle et de précision aux annonceurs.
            </p>
            <p class="accroche">L'objectif : mieux préparer les campagnes, mieux suivre leur exécution et capitaliser sur les données disponibles pour améliorer les décisions média.</p>

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
                @php $img = 'images/cible/pole-1-affichage.jpg'; @endphp
                @if(file_exists(public_path($img)))
                    <img src="{{ asset($img) }}" alt="Suivi terrain d'un panneau CIBLE" loading="lazy">
                @else
                    <div class="slot">Suivi terrain CIBLE<small>images/cible/pole-1-affichage.jpg</small></div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ CAMPAGNES PHYGITALES ═══════════════════ --}}
<section class="phygital">
    <div class="entete rev">
        <span class="sur">De la rue au digital</span>
        <h2 class="t1">Une même expérience de marque, sur tous les points de contact.</h2>
        <p>Les audiences ne vivent plus dans un seul espace. Elles circulent, consultent, interagissent, partagent et achètent. Nous concevons des campagnes phygitales qui relient l'affichage, le terrain, les réseaux sociaux, les contenus et les points de vente pour créer un parcours de marque cohérent.</p>
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
<section class="workflow">
    <div class="entete rev">
        <span class="sur">Notre méthode</span>
        <h2>De l'objectif <em>à l'impact.</em></h2>
        <p>Une méthode intégrée, un interlocuteur unique et une traçabilité complète à chaque étape de votre campagne.</p>
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
<section class="objectifs">
    <div class="entete rev">
        <span class="sur">Vos ambitions, nos dispositifs</span>
        <h2 class="t1">Une expertise adaptée à chaque objectif de marque.</h2>
    </div>
    <div class="obj-grid rev">
        @foreach([
            ['Accroître la notoriété d\'une marque',                'var(--rouge)'],
            ['Lancer un produit ou un service',                     'var(--jaune)'],
            ['Générer du trafic vers un point de vente',            'var(--vert)'],
            ['Toucher une audience dans une zone précise',          'var(--bleu)'],
            ['Créer une interaction directe avec les consommateurs','var(--violet)'],
            ['Amplifier une campagne en ligne et sur le terrain',   'var(--rouge)'],
            ['Valoriser une institution ou une entreprise',         'var(--jaune)'],
            ['Déployer une campagne nationale',                     'var(--vert)'],
        ] as [$label, $c])
            <div class="obj-item" style="--c:{{ $c }}"><i></i>{{ $label }}</div>
        @endforeach
    </div>
</section>

{{-- ═══════════════════ CTA ═══════════════════ --}}
<section class="cta-serv">
    <span class="sur">Votre prochaine campagne commence ici</span>
    <h2 class="t1">Construisons une expérience de marque que votre audience remarquera et retiendra.</h2>
    <p>Partagez-nous vos objectifs, vos cibles, vos zones prioritaires et votre calendrier. Notre équipe vous proposera une stratégie de visibilité, d'activation et de déploiement adaptée à votre ambition.</p>
    <div style="margin-top:32px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a class="bouton b-rouge" href="{{ route('contact') }}">Parler de mon projet</a>
        <a class="bouton b-ligne" href="{{ route('references') }}">Découvrir nos réalisations</a>
    </div>
</section>

@endsection
