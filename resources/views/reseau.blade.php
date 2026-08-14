@extends('_layout', [
    'seo_title'       => 'Notre parc média — CIBLE · +400 panneaux dans 31 communes',
    'seo_description' => 'Le parc publicitaire CIBLE : +400 panneaux exploités à Abidjan et dans les principales villes de Côte d\'Ivoire. Carte interactive, formats, couverture territoriale.',
])

@push('page-css')
    .reseau-hero{background:var(--bleu);color:#fff;padding:clamp(60px,8vw,110px) var(--pad)}
    .reseau-hero .sur{color:rgba(255,255,255,.85)}
    .reseau-hero h1{margin-top:14px;color:#fff;max-width:24ch}
    .reseau-hero p{margin-top:22px;max-width:60ch;color:rgba(255,255,255,.9);font-size:18px}
    .stats{display:flex;gap:44px;flex-wrap:wrap;margin-top:44px}
    .stats .v{font-family:var(--titre);font-weight:900;font-size:clamp(46px,6vw,80px);line-height:.86;letter-spacing:-.04em}
    .stats .l{font-family:var(--titre);font-weight:600;font-size:13px;opacity:.9;margin-top:10px;text-transform:uppercase;letter-spacing:.08em}

    /* Approche média — 3 cartes */
    .approche-med{padding:clamp(56px,8vw,100px) var(--pad)}
    .approche-med .entete{max-width:860px;margin:0 auto 48px;text-align:center}
    .approche-med .sur{color:var(--violet)}
    .approche-med .t1{margin-top:12px}
    .approche-med .entete p{margin-top:20px;color:#444}
    .app-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(18px,3vw,32px)}
    @media(max-width:900px){.app-grid{grid-template-columns:1fr}}
    .app-card{background:#fff;border:1px solid var(--gris);border-radius:16px;padding:28px 26px;border-top:6px solid var(--c)}
    .app-card h3{font-family:var(--titre);font-weight:800;font-size:19px;color:var(--c);margin-bottom:10px}
    .app-card p{font-size:14.5px;color:#555;line-height:1.6}

    .carte-section{padding:clamp(56px,8vw,100px) var(--pad);background:var(--gris)}
    .carte-section .entete{max-width:860px;margin:0 auto 44px;text-align:center}
    .carte-section .sur{color:var(--bleu)}
    .carte-section .t1{margin-top:12px}
    .carte-section .entete p{margin-top:18px;color:#444}
    .carte-slot{aspect-ratio:16/10;border-radius:26px;overflow:hidden;background:#fff;position:relative;box-shadow:0 20px 60px -30px rgba(0,0,0,.3)}
    .carte-slot .note{position:absolute;bottom:16px;left:16px;right:16px;background:rgba(255,255,255,.94);padding:12px 16px;border-radius:10px;font-size:13px;color:#666;font-family:var(--titre);font-weight:600;z-index:1000;pointer-events:none}
    .carte-cta{margin-top:32px;text-align:center}
    /* Carte Leaflet 2026-08-04 — remplace le placeholder */
    #reseau-map{width:100%;height:100%}
    #reseau-map-loading{
        position:absolute;inset:0;display:flex;align-items:center;
        justify-content:center;flex-direction:column;gap:10px;
        background:var(--gris);color:#666;font-family:var(--titre);
        font-weight:700;font-size:14px;z-index:500;
    }
    #reseau-map-loading .spinner{
        width:36px;height:36px;border:3px solid rgba(0,0,0,.1);
        border-top-color:var(--bleu);border-radius:50%;
        animation:reseau-spin .9s linear infinite;
    }
    @keyframes reseau-spin{to{transform:rotate(360deg)}}
    #reseau-map-loading.hidden{display:none}
    /* Style des pins CIBLE — pastilles jaune/dorées avec chiffre panneaux */
    .cible-pin{
        background:var(--jaune);color:var(--noir);
        padding:6px 12px;border-radius:20px;
        font-family:var(--titre);font-weight:800;font-size:13px;
        white-space:nowrap;box-shadow:0 3px 10px rgba(0,0,0,.25);
        border:2px solid #fff;
        transform:translate(-50%,-50%);
    }
    .cible-pin b{color:var(--rouge)}
    .leaflet-popup-content-wrapper{
        border-radius:12px;font-family:var(--corps);
    }
    .leaflet-popup-content{
        margin:14px 16px;font-size:13px;line-height:1.5;
    }
    .leaflet-popup-content strong{
        display:block;font-family:var(--titre);font-weight:800;
        font-size:15px;color:var(--bleu);margin-bottom:4px;
    }

    .communes{padding:clamp(56px,8vw,100px) var(--pad)}
    .communes .entete{max-width:860px;margin:0 auto 44px;text-align:center}
    .communes .sur{color:var(--vert)}
    .communes .t1{margin-top:12px}
    .communes-grid{display:grid;grid-template-columns:1fr 1fr;gap:clamp(20px,3vw,40px)}
    @media(max-width:800px){.communes-grid{grid-template-columns:1fr}}
    .zone{background:#fff;border:1px solid var(--gris);border-radius:16px;padding:32px;border-top:5px solid var(--c)}
    .zone h3{font-family:var(--titre);font-weight:900;font-size:26px;color:var(--c);margin-bottom:6px}
    .zone .zone-sub{font-family:var(--titre);font-weight:600;font-size:14px;color:#666;margin-bottom:20px}
    .zone-list{list-style:none;display:grid;grid-template-columns:repeat(2,1fr);gap:8px 20px;font-size:14.5px;color:#333}
    .zone-list li{padding:6px 0;border-bottom:1px dashed #E4E4E4;font-family:var(--titre);font-weight:600}
    .zone-note{margin-top:18px;font-size:13.5px;color:#777;line-height:1.55}

    /* Formats du parc */
    .formats-sec{padding:clamp(56px,8vw,100px) var(--pad);background:var(--gris)}
    .formats-sec .entete{max-width:860px;margin:0 auto 44px;text-align:center}
    .formats-sec .sur{color:var(--jaune)}
    .formats-sec .t1{margin-top:12px}
    .formats-sec .entete p{margin-top:18px;color:#444}
    .fmt-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:clamp(14px,2vw,24px)}
    @media(max-width:900px){.fmt-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:520px){.fmt-grid{grid-template-columns:1fr}}
    .fmt-card{background:#fff;border-radius:16px;padding:26px 24px;border-left:5px solid var(--c)}
    .fmt-card h4{font-family:var(--titre);font-weight:800;font-size:17px;color:#111;margin-bottom:10px}
    .fmt-card p{font-size:14px;color:#555;line-height:1.6}

    /* Media Intelligence (fond noir) */
    .data-sec{padding:clamp(56px,8vw,100px) var(--pad);background:var(--noir);color:#fff}
    .data-sec .entete{max-width:860px;margin:0 auto 48px;text-align:center}
    .data-sec .sur{color:var(--jaune)}
    .data-sec .t1{margin-top:12px;color:#fff}
    .data-sec .entete p{margin-top:20px;color:rgba(255,255,255,.75)}
    .data-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
    @media(max-width:900px){.data-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:520px){.data-grid{grid-template-columns:1fr}}
    .data-card{padding:24px 22px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:14px;border-top:3px solid var(--c)}
    .data-card h4{font-family:var(--titre);font-weight:800;font-size:16px;color:var(--c);margin-bottom:10px}
    .data-card p{font-size:13.5px;color:rgba(255,255,255,.72);line-height:1.6}

    .qualite{padding:clamp(56px,8vw,100px) var(--pad)}
    .qualite .entete{max-width:860px;margin:0 auto 48px;text-align:center}
    .qualite .sur{color:var(--rouge)}
    .qualite .t1{margin-top:12px}
    .q-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:clamp(16px,2vw,26px)}
    @media(max-width:900px){.q-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:560px){.q-grid{grid-template-columns:1fr}}
    .qcard{padding:28px 24px;background:#fff;border:1px solid var(--gris);border-radius:16px}
    .qcard h4{font-family:var(--titre);font-weight:800;font-size:17px;margin-bottom:10px;color:var(--c)}
    .qcard p{font-size:14px;color:#555;line-height:1.6}

    /* Cas d'usage */
    .usages{padding:clamp(56px,8vw,100px) var(--pad);background:var(--gris)}
    .usages .entete{max-width:860px;margin:0 auto 44px;text-align:center}
    .usages .sur{color:var(--violet)}
    .usages .t1{margin-top:12px}
    .usages-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:1000px;margin:0 auto}
    @media(max-width:700px){.usages-grid{grid-template-columns:1fr}}
    .usage-item{display:flex;align-items:center;gap:14px;background:#fff;border-radius:12px;padding:18px 20px;font-family:var(--titre);font-weight:700;font-size:15px;color:#222;transition:transform .2s}
    .usage-item:hover{transform:translateX(4px)}
    .usage-item i{width:10px;height:10px;border-radius:50%;background:var(--c);flex-shrink:0}

    .cta-res{padding:clamp(60px,8vw,120px) var(--pad);text-align:center}
    .cta-res .sur{color:var(--rouge)}
    .cta-res .t1{max-width:24ch;margin:14px auto 0}
    .cta-res p{margin-top:20px;max-width:58ch;margin-left:auto;margin-right:auto;color:#666}
@endpush

@section('content')

<section class="reseau-hero a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:#fff;--op:.13;top:4%;left:38%;width:150px;--r:22deg;--dur:25s;--del:.2s'],
        ['f-plume',  '--c:#fff;--op:.11;bottom:6%;right:4%;width:120px;--r:-12deg;--dur:29s;--del:.5s'],
        ['f-fleche', '--c:#fff;--op:.16;top:16%;right:14%;width:110px;--r:8deg;--dur:21s;--del:.8s'],
    ]" />
    <div>
        <span class="sur">Notre parc publicitaire</span>
        <h1 class="t1">Votre audience est quelque part. Notre réseau vous permet de l'atteindre.</h1>
        <p>CIBLE exploite un parc de +400 panneaux répartis à Abidjan et dans les principales villes de Côte d'Ivoire. Notre connaissance du terrain nous permet de recommander les zones, les axes et les formats les plus adaptés à vos objectifs de visibilité.</p>
        {{-- Répartition 180 / 184 retirée 2026-08-10 : leur somme (364)
             contredisait le volume global désormais annoncé en "+400".
             Les deux zones restent décrites dans la section Couverture. --}}
        <div class="stats">
            <div><div class="v num"><span aria-hidden="true">+</span><span data-cible="{{ \App\Support\Contenu::get('chiffres.panneaux') }}">0</span></div><div class="l">Panneaux exploités</div></div>
            <div><div class="v num" data-cible="{{ \App\Support\Contenu::get('chiffres.communes') }}">0</div><div class="l">Communes et villes<br>couvertes</div></div>
        </div>
        <a class="bouton b-blanc" style="margin-top:36px" href="{{ route('contact') }}">Trouver mes emplacements</a>
    </div>
</section>

{{-- ═══ NOTRE APPROCHE MÉDIA ═══ --}}
<section class="approche-med a-decor">
    <x-decor :formes="[
        ['f-fleche', '--c:var(--violet);--op:.12;top:6%;left:3%;width:120px;--r:-14deg;--dur:24s'],
        ['f-plume',  '--c:var(--bleu);--op:.10;bottom:4%;right:4%;width:110px;--r:18deg;--dur:30s;--del:.5s'],
    ]" />
    <div class="entete rev">
        <span class="sur">Plus qu'une carte</span>
        <h2 class="t1">Des emplacements sélectionnés selon vos audiences et vos objectifs.</h2>
        <p>Une campagne extérieure performante ne repose pas uniquement sur le nombre de panneaux. Elle dépend de la pertinence des zones, de la visibilité du support, du sens de circulation, du format, de la durée d'exposition et de la cohérence avec les habitudes de votre cible.</p>
    </div>
    <div class="app-grid">
        <div class="app-card rev" style="--c:var(--rouge)">
            <h3>Vos objectifs</h3>
            <p>Notoriété, lancement de produit, couverture nationale, proximité commerciale ou génération de trafic.</p>
        </div>
        <div class="app-card rev" style="--c:var(--vert)">
            <h3>Vos audiences</h3>
            <p>Nous identifions les zones de présence, de déplacement et de concentration des publics que vous souhaitez atteindre.</p>
        </div>
        <div class="app-card rev" style="--c:var(--bleu)">
            <h3>Votre dispositif</h3>
            <p>Nous construisons une sélection cohérente d'emplacements, de formats et de périodes en fonction de votre budget.</p>
        </div>
    </div>
</section>

{{-- ═══ CARTE INTERACTIVE ═══ --}}
<section class="carte-section">
    <div class="entete rev">
        <span class="sur">Notre couverture</span>
        <h2 class="t1">Explorez les zones couvertes par CIBLE.</h2>
        <p>Consultez les principales villes et zones dans lesquelles notre parc publicitaire est déployé.</p>
    </div>
    <div class="carte-slot rev">
        <div id="reseau-map" aria-label="Carte interactive du réseau CIBLE"></div>
        <div id="reseau-map-loading" role="status">
            <div class="spinner" aria-hidden="true"></div>
            <div>Chargement de la carte…</div>
        </div>
        <div class="note">Sélectionnez une zone pour découvrir sa couverture générale. Les emplacements disponibles et les recommandations détaillées vous seront communiqués sur demande.</div>
    </div>
    <div class="carte-cta">
        <a class="bouton b-rouge" href="{{ route('contact') }}">Recevoir une sélection d'emplacements</a>
    </div>
</section>

{{-- ═══ COUVERTURE TERRITORIALE ═══ --}}
<section class="communes">
    <div class="entete rev">
        <span class="sur">Couverture territoriale</span>
        <h2 class="t1">De la capitale économique aux principaux bassins de consommation.</h2>
    </div>
    <div class="communes-grid">
        <div class="zone rev" style="--c:var(--rouge)">
            <h3>Grand Abidjan</h3>
            <div class="zone-sub">Principaux axes, communes et zones commerciales du Grand Abidjan.</div>
            <ul class="zone-list">
                <li>Plateau</li><li>Cocody</li>
                <li>Yopougon</li><li>Abobo</li>
                <li>Marcory</li><li>Treichville</li>
                <li>Koumassi</li><li>Port-Bouët</li>
                <li>Attécoubé</li><li>Adjamé</li>
                <li>Bingerville</li><li>Songon</li>
                <li>Anyama</li>
            </ul>
            <p class="zone-note">Ainsi que les principales zones de la Riviera et d'Angré.</p>
        </div>
        <div class="zone rev" style="--c:var(--vert)">
            <h3>Intérieur du pays</h3>
            <div class="zone-sub">17 villes stratégiques de Côte d'Ivoire.</div>
            <ul class="zone-list">
                <li>Bouaké</li><li>San-Pédro</li>
                <li>Yamoussoukro</li><li>Korhogo</li>
                <li>Man</li><li>Daloa</li>
                <li>Gagnoa</li><li>Divo</li>
                <li>Bondoukou</li><li>Odienné</li>
                <li>Séguéla</li><li>Ferkessédougou</li>
                <li>Dabou</li><li>Grand-Bassam</li>
                <li>Aboisso</li><li>Soubré</li>
            </ul>
            <p class="zone-note">Et autres zones selon disponibilité.</p>
        </div>
    </div>
</section>

{{-- ═══ FORMATS ═══ --}}
<section class="formats-sec">
    <div class="entete rev">
        <span class="sur">Des formats pour chaque ambition</span>
        <h2 class="t1">De la proximité à l'impact spectaculaire.</h2>
        <p>Notre parc comprend des dispositifs adaptés aux campagnes locales, urbaines, nationales et événementielles.</p>
    </div>
    <div class="fmt-grid rev">
        @foreach([
            ['Affichage classique',              'Des formats efficaces pour construire une couverture régulière et répétée sur les principaux axes.',                                   'var(--rouge)'],
            ['Grands et très grands formats',    'Des dispositifs à fort impact visuel pour les lancements, les prises de parole institutionnelles et les campagnes de notoriété.',       'var(--jaune)'],
            ['Dispositifs lumineux et digitaux', 'Des supports conçus pour renforcer la visibilité du message, notamment en soirée et dans les zones à forte fréquentation.',              'var(--bleu)'],
            ['Affichage de proximité',           'Des formats implantés près des commerces, des lieux de passage et des zones de consommation.',                                           'var(--violet)'],
        ] as [$t, $d, $c])
            <div class="fmt-card" style="--c:{{ $c }}">
                <h4>{{ $t }}</h4>
                <p>{{ $d }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══ MEDIA INTELLIGENCE ═══ --}}
<section class="data-sec a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:#fff;--op:.06;top:5%;left:4%;width:150px;--r:18deg;--dur:31s'],
        ['f-plume',  '--c:#fff;--op:.05;bottom:3%;right:5%;width:120px;--r:-20deg;--dur:35s;--del:.4s'],
        ['f-fleche', '--c:var(--jaune);--op:.14;top:32%;right:13%;width:110px;--r:6deg;--dur:23s;--del:.7s'],
    ]" />
    <div class="entete rev">
        <span class="sur">Media Intelligence</span>
        <h2 class="t1">Un parc référencé, piloté et documenté.</h2>
        <p>Notre réseau est progressivement digitalisé afin d'améliorer la sélection des emplacements, la planification des campagnes, le contrôle des poses et la transmission des preuves de diffusion.</p>
    </div>
    <div class="data-grid rev">
        @foreach([
            ['Inventaire centralisé', 'Chaque support est référencé selon sa localisation, son format, ses caractéristiques et son statut d\'exploitation.', 'var(--jaune)'],
            ['Sélection média',       'Les emplacements sont proposés selon les zones ciblées, les objectifs de campagne et les contraintes budgétaires.',   'var(--vert)'],
            ['Suivi terrain',         'Les opérations de pose sont documentées par des photos horodatées et géolocalisées.',                                  'var(--bleu)'],
            ['Reporting',             'Les annonceurs disposent d\'une vue claire des supports activés et des preuves d\'exécution de leur campagne.',        'var(--rouge)'],
        ] as [$t, $d, $c])
            <div class="data-card" style="--c:{{ $c }}">
                <h4>{{ $t }}</h4>
                <p>{{ $d }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══ QUALITÉ DU PARC ═══ --}}
<section class="qualite">
    <div class="entete rev">
        <span class="sur">Notre exigence terrain</span>
        <h2 class="t1">Un réseau exploité avec rigueur, du choix de l'emplacement au suivi de la campagne.</h2>
    </div>
    <div class="q-grid">
        @foreach([
            ['Maîtrise opérationnelle',    'Nos équipes pilotent la planification, la pose, le contrôle et le suivi des dispositifs activés.',                                                       'var(--rouge)'],
            ['Entretien du réseau',        'Nos supports font l\'objet de contrôles et d\'interventions afin de préserver la qualité de présentation des campagnes.',                                 'var(--jaune)'],
            ['Preuves de diffusion',       'Chaque campagne est documentée à l\'aide de photos terrain horodatées et géolocalisées.',                                                                 'var(--vert)'],
            ['Recommandation personnalisée','Nous ne vous remettons pas simplement une liste de panneaux : nous construisons un dispositif correspondant à vos objectifs et à vos audiences.',        'var(--bleu)'],
        ] as [$t, $d, $c])
            <div class="qcard rev" style="--c:{{ $c }}">
                <h4>{{ $t }}</h4>
                <p>{{ $d }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══ CAS D'USAGE ═══ --}}
<section class="usages a-decor">
    <x-decor :formes="[
        ['f-fleche', '--c:var(--violet);--op:.12;top:7%;left:4%;width:115px;--r:-16deg;--dur:26s'],
        ['f-plume',  '--c:var(--vert);--op:.12;bottom:5%;right:4%;width:110px;--r:20deg;--dur:32s;--del:.5s'],
    ]" />
    <div class="entete rev">
        <span class="sur">Pour quels objectifs ?</span>
        <h2 class="t1">Un réseau adapté aux différents temps forts de votre marque.</h2>
    </div>
    <div class="usages-grid rev">
        @foreach([
            ['Lancement de produit ou de service',   'var(--rouge)'],
            ['Campagne nationale de notoriété',      'var(--jaune)'],
            ['Ouverture d\'un point de vente',       'var(--vert)'],
            ['Communication institutionnelle',       'var(--bleu)'],
            ['Couverture d\'une zone commerciale',   'var(--violet)'],
            ['Campagne promotionnelle',              'var(--rouge)'],
            ['Génération de trafic en magasin',      'var(--jaune)'],
            ['Prise de parole événementielle',       'var(--vert)'],
        ] as [$label, $c])
            <div class="usage-item" style="--c:{{ $c }}"><i></i>{{ $label }}</div>
        @endforeach
    </div>
</section>

{{-- ═══ CTA FINAL ═══ --}}
<section class="cta-res a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:var(--bleu);--op:.12;top:10%;left:7%;width:110px;--r:16deg;--dur:28s'],
        ['f-fleche', '--c:var(--rouge);--op:.16;bottom:12%;right:8%;width:120px;--r:-10deg;--dur:22s;--del:.4s'],
    ]" />
    <span class="sur">Votre plan média commence ici</span>
    <h2 class="t1">Identifions les emplacements qui donneront le plus de force à votre campagne.</h2>
    <p>Indiquez-nous vos objectifs, vos audiences, vos zones prioritaires, votre période et votre budget. Notre équipe vous préparera une sélection personnalisée de formats et d'emplacements.</p>
    <div style="margin-top:32px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a class="bouton b-rouge" href="{{ route('contact') }}">Recevoir une recommandation média</a>
    </div>
</section>

@endsection

@push('head')
    {{-- Leaflet 1.9.4 — CSS chargé dans le head, JS déféré en bas de page
         via @push('page-js'). --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@push('page-js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""
        defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapEl = document.getElementById('reseau-map');
    if (!mapEl || typeof L === 'undefined') return;

    // Init map — centre Abidjan, bounds Côte d'Ivoire, tiles CARTO light.
    const map = L.map(mapEl, {
        center: [5.36, -4.01],
        zoom: 11,
        minZoom: 6,
        maxZoom: 15,
        maxBounds: L.latLngBounds(L.latLng(4.3, -8.6), L.latLng(10.7, -2.5)),
        maxBoundsViscosity: 0.85,
        scrollWheelZoom: false, // désactivé côté vitrine — évite le zoom accidentel en scroll
        zoomControl: true,
    });
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> · © <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(map);

    // Réactive la molette souris uniquement quand la carte a le focus
    // (clic dessus). Évite les scrolls "captés" involontairement.
    mapEl.addEventListener('click', () => map.scrollWheelZoom.enable());
    mapEl.addEventListener('mouseleave', () => map.scrollWheelZoom.disable());

    // Chargement des pins agrégés par commune
    fetch("{{ route('api.reseau-map') }}", { headers: { 'Accept': 'application/json' } })
        .then(r => r.ok ? r.json() : Promise.reject(r.status))
        .then(data => {
            const pins = Array.isArray(data.pins) ? data.pins : [];
            if (pins.length === 0) {
                document.getElementById('reseau-map-loading').innerHTML =
                    '<div>Aucune donnée à afficher pour le moment.</div>';
                return;
            }

            // 2026-08-10 : les pins n'affichent plus le nombre de panneaux
            // par commune. Le parc est désormais communiqué en volume global
            // arrondi (+400) et la note de carte renvoie le détail des
            // emplacements au commercial. Le JSON garde ses `total` (non
            // utilisés à l'affichage) pour un usage interne ultérieur.
            const bounds = L.latLngBounds();
            pins.forEach(pin => {
                if (pin.lat == null || pin.lng == null) return;
                const icon = L.divIcon({
                    html: '<div class="cible-pin">' + pin.commune + '</div>',
                    className: 'cible-pin-wrapper',
                    iconSize: null, // laisse le contenu déterminer la taille
                });
                const marker = L.marker([pin.lat, pin.lng], { icon: icon }).addTo(map);
                const zoneLabel = (pin.city && pin.city !== pin.commune) ? pin.city : (pin.region || '');
                marker.bindPopup(
                    '<strong>' + pin.commune + '</strong>' +
                    (zoneLabel ? '<em style="color:#666;font-style:normal">' + zoneLabel + '</em><br>' : '') +
                    'Zone couverte par le réseau CIBLE'
                );
                bounds.extend([pin.lat, pin.lng]);
            });

            // Cadre auto sur l'ensemble des pins (pas trop serré → padding).
            if (bounds.isValid()) {
                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 12 });
            }

            document.getElementById('reseau-map-loading').classList.add('hidden');
        })
        .catch(err => {
            console.warn('[cible/reseau] map load failed', err);
            const loader = document.getElementById('reseau-map-loading');
            if (loader) {
                loader.innerHTML = '<div>Chargement de la carte impossible.<br><small>Notre équipe reste disponible : commercial@cible-ci.com</small></div>';
            }
        });
});
</script>
@endpush
