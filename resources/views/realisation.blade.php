{{-- ═══════════════════════════════════════════════════════════════════
     Détail d'une réalisation — /references/{slug}
     Contenu issu à 100 % du document textes (REF.Cx.*) via
     CibleController::projets(). Aucune donnée inventée : la page
     n'affiche que nom, catégorie, titre, descriptif et services.
════════════════════════════════════════════════════════════════════ --}}
@extends('_layout', [
    'seo_title'       => $projet['nom'] . ' — Réalisation CIBLE · ' . $projet['cat'],
    'seo_description' => $projet['titre'] . ' ' . $projet['texte'],
])

@push('page-css')
    .proj-hero{padding:clamp(44px,6vw,80px) var(--pad) 0}
    .proj-fil{font-family:var(--titre);font-weight:700;font-size:13px;color:#888}
    .proj-fil a:hover{color:var(--c)}
    .proj-hero .sur{color:var(--c);display:block;margin-top:24px}
    .proj-hero h1{margin-top:12px;max-width:20ch}
    .proj-visuel{margin:clamp(32px,4vw,52px) var(--pad) 0;border-radius:26px;overflow:hidden;aspect-ratio:16/9;background:var(--gris);position:relative}
    .proj-visuel::after{content:"";position:absolute;inset:auto 0 0 0;height:8px;background:var(--c)}
    .proj-visuel img{width:100%;height:100%;object-fit:cover}

    .proj-corps{padding:clamp(44px,6vw,80px) var(--pad)}
    .proj-grid{display:grid;grid-template-columns:1.3fr .7fr;gap:clamp(30px,5vw,70px);align-items:start;max-width:1200px;margin:0 auto}
    @media(max-width:860px){.proj-grid{grid-template-columns:1fr}}
    .proj-accroche{font-family:var(--titre);font-weight:800;font-size:clamp(20px,2.4vw,28px);line-height:1.3;color:#111}
    .proj-texte{margin-top:20px;font-size:17px;line-height:1.75;color:#444}
    .proj-aside{background:var(--gris);border-radius:18px;padding:28px 26px;border-top:6px solid var(--c)}
    .proj-aside h2{font-family:var(--titre);font-weight:800;font-size:13px;text-transform:uppercase;letter-spacing:.1em;color:#777;margin-bottom:16px}
    .proj-aside ul{list-style:none}
    .proj-aside li{padding:10px 0;border-bottom:1px dashed #D5D5D5;font-family:var(--titre);font-weight:700;font-size:14.5px;color:#222}
    .proj-aside li:last-child{border-bottom:0}
    .proj-aside .cat{font-family:var(--titre);font-weight:800;font-size:16px;color:var(--c);margin-bottom:22px;line-height:1.35}

    .proj-nav{padding:0 var(--pad) clamp(44px,6vw,80px);max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap}
    .proj-nav a{font-family:var(--titre);font-weight:800;font-size:14px;color:#555;display:inline-flex;align-items:center;gap:10px;padding:14px 0}
    .proj-nav a:hover{color:var(--c)}
    .proj-nav small{display:block;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:#AAA}

    .proj-cta{padding:clamp(56px,8vw,110px) var(--pad);text-align:center;background:var(--noir);color:#fff}
    .proj-cta .t1{color:#fff;max-width:24ch;margin:0 auto}
    .proj-cta p{margin-top:20px;max-width:56ch;margin-left:auto;margin-right:auto;color:rgba(255,255,255,.72)}
@endpush

@section('content')

@php $projets = \App\Http\Controllers\CibleController::projets(); @endphp

<div style="--c:{{ $projet['couleur'] }}">

    <section class="proj-hero">
        <nav class="proj-fil" aria-label="Fil d'Ariane">
            <a href="{{ route('references') }}">Nos réalisations</a> · <span>{{ $projet['nom'] }}</span>
        </nav>
        <span class="sur">{{ $projet['cat'] }}</span>
        <h1 class="t1">{{ $projet['nom'] }}</h1>
    </section>

    <div class="proj-visuel rev">
        @if(file_exists(public_path($projet['image'])))
            <img src="{{ asset($projet['image']) }}" alt="{{ $projet['nom'] }} — {{ $projet['cat'] }}">
        @else
            <div class="slot slot--sombre">{{ $projet['nom'] }} · {{ $projet['cat'] }}<small>{{ $projet['image'] }}</small></div>
        @endif
    </div>

    <section class="proj-corps">
        <div class="proj-grid">
            <div class="rev">
                <p class="proj-accroche">{{ $projet['titre'] }}</p>
                <p class="proj-texte">{{ $projet['texte'] }}</p>
            </div>
            <aside class="proj-aside rev">
                <h2>Nature du projet</h2>
                <div class="cat">{{ $projet['cat'] }}</div>
                <h2>Prestations</h2>
                <ul>
                    {{-- `services` est une énumération séparée par des points
                         médians dans le document textes. --}}
                    @foreach(array_map('trim', explode('·', $projet['services'])) as $service)
                        <li>{{ $service }}</li>
                    @endforeach
                </ul>
            </aside>
        </div>
    </section>

    <nav class="proj-nav" aria-label="Autres réalisations">
        <a href="{{ route('realisation', $precedent) }}">
            <span>←</span>
            <span><small>Réalisation précédente</small>{{ $projets[$precedent]['nom'] }}</span>
        </a>
        <a href="{{ route('realisation', $suivant) }}" style="text-align:right">
            <span><small>Réalisation suivante</small>{{ $projets[$suivant]['nom'] }}</span>
            <span>→</span>
        </a>
    </nav>

</div>

<section class="proj-cta">
    <h2 class="t1">Faisons de votre prochaine campagne une réalisation qui compte.</h2>
    <p>Partagez-nous votre objectif, votre audience et votre ambition. Nous imaginerons le dispositif de visibilité, d'activation et de communication le plus pertinent pour votre marque.</p>
    <div style="margin-top:32px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a class="bouton b-rouge" href="{{ route('contact') }}">Parler de mon projet</a>
        <a class="bouton b-ligne" style="border-color:#fff;color:#fff" href="{{ route('references') }}">Toutes les réalisations</a>
    </div>
</section>

@endsection
