@extends('admin._layout', ['titre' => 'Tableau de bord', 'connecte' => true, 'onglet' => 'tableau'])

@section('contenu')

<style>
    .chiffres{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:22px}
    @media(max-width:760px){.chiffres{grid-template-columns:1fr}}
    .chiffre{background:#fff;border:1px solid var(--gris);border-radius:14px;padding:20px 22px}
    .chiffre .v{font-size:30px;font-weight:800;line-height:1.1;letter-spacing:-.02em}
    .chiffre .l{font-size:12.5px;color:#777;margin-top:5px;font-weight:600}
    .chiffre.ok .v{color:var(--vert)}
    .chiffre.ko .v{color:var(--rouge);font-size:20px;padding-top:7px}

    .cartes{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:14px}
    @media(max-width:760px){.cartes{grid-template-columns:1fr}}
    .page-carte{
        display:block;background:#fff;border:1px solid var(--gris);border-radius:14px;
        padding:18px 20px;text-decoration:none;border-top:4px solid var(--c);
    }
    .page-carte:hover{border-color:#BBB;border-top-color:var(--c)}
    .page-carte .nom{font-weight:800;font-size:16px}
    .page-carte .desc{font-size:13px;color:#777;margin-top:5px;line-height:1.5}
    .page-carte .pied{display:flex;align-items:center;gap:8px;margin-top:12px;font-size:12.5px;color:#999}
    .page-carte .badge{background:#dcfce7;color:#166534;border-radius:999px;padding:2px 9px;font-weight:700}
    .titre-sec{font-size:13px;font-weight:800;margin-top:34px;text-transform:uppercase;
        letter-spacing:.08em;color:#888}
</style>

<h1>Tableau de bord</h1>
<p class="intro">Modifiez le contenu du site. Les changements sont visibles immédiatement, sans redéploiement ni intervention technique.</p>

@unless($stockage)
    {{-- Avertissement capital : sans volume persistant, le conteneur étant
         éphémère, toute modification disparaît au déploiement suivant. Mieux
         vaut le dire franchement que laisser croire que c'est enregistré. --}}
    <div class="alerte a-attention" style="margin-top:22px">
        ⚠ <strong>Stockage persistant indisponible.</strong><br>
        Les modifications ne peuvent pas être enregistrées. Dans Coolify, ajoutez un
        volume persistant monté sur <code>/app/storage/app/contenu</code>, puis
        redémarrez l'application. Le site public continue de fonctionner normalement
        avec son contenu d'origine.
    </div>
@else
    {{-- Piège plus discret que le précédent : le dossier est inscriptible,
         donc tout semble fonctionner, mais il vit dans la couche du conteneur
         et repart à zéro au déploiement suivant. C'est ce qui a effacé le mot
         de passe administrateur. On le dit avant que du travail soit perdu. --}}
    @if($persistant === false)
        <div class="alerte a-attention" style="margin-top:22px">
            ⚠ <strong>Vos modifications ne survivront pas au prochain déploiement.</strong><br>
            L'enregistrement fonctionne, mais le dossier de contenu n'est pas un volume
            persistant : il est recréé à neuf à chaque mise en ligne. Dans Coolify,
            onglet <em>Storages</em>, ajoutez un volume monté sur
            <code>/app/storage/app/contenu</code>, puis redéployez.
        </div>
    @endif
@endunless

@php
    $pages    = \App\Support\Schema::pages();
    $nbChamps = collect($pages)->sum(fn ($p, $c) => count(\App\Support\Schema::champs($c)));
    $nbModif  = collect($surchargees)->filter()->count();
@endphp

<div class="chiffres">
    <div class="chiffre">
        <div class="v">{{ count($pages) }}</div>
        <div class="l">Pages modifiables</div>
    </div>
    <div class="chiffre">
        <div class="v">{{ $nbChamps }}</div>
        <div class="l">Textes et visuels éditables</div>
    </div>
    <div class="chiffre {{ $stockage && $persistant !== false ? 'ok' : 'ko' }}">
        <div class="v">{{ $stockage && $persistant !== false ? 'OK' : 'À corriger' }}</div>
        <div class="l">Enregistrement des modifications</div>
    </div>
</div>

<div class="titre-sec">Les pages du site</div>
<div class="cartes">
    @foreach($pages as $cle => $page)
        @php $date = \App\Support\Contenu::derniereModification($cle); @endphp
        <a class="page-carte" href="{{ route('admin.page', $cle) }}" style="--c:{{ $page['couleur'] }}">
            <span class="nom">{{ $page['titre'] }}</span>
            <span class="desc" style="display:block">{{ $page['resume'] }}</span>
            <span class="pied">
                {{ count(\App\Support\Schema::champs($cle)) }} champs
                @if($date)
                    <span class="badge">modifiée</span>
                    <span>le {{ $date->format('d/m/Y à H\hi') }}</span>
                @endif
            </span>
        </a>
    @endforeach
</div>

<div class="titre-sec">Commun à tout le site</div>
<p class="intro" style="font-size:13.5px;margin-top:4px">Repris sur plusieurs pages à la fois.</p>
<div class="liste" style="margin-top:14px">
    @foreach([
        ['coordonnees',  'Coordonnées',   'Téléphones, emails et adresse. Pied de page, contact et données Google.', 'var(--rouge)'],
        ['chiffres',     'Chiffres clés', 'Panneaux, communes, années, distinctions. Alimente les compteurs.',       'var(--jaune)'],
        ['realisations', 'Réalisations',  'Les 6 projets : textes, visuels et couleur.',                             'var(--violet)'],
    ] as [$cle, $titre, $desc, $couleur])
        <a class="ligne" href="{{ route('admin.' . $cle) }}">
            <span class="pastille" style="background:{{ $couleur }}"></span>
            <span>
                <span class="nom">{{ $titre }}</span>
                <span class="cat" style="display:block">{{ $desc }}</span>
            </span>
            @if($surchargees[$cle] ?? false)
                <span class="cat" style="margin-left:auto;color:var(--vert)">modifié</span>
            @endif
            <span class="fleche">›</span>
        </a>
    @endforeach
</div>

<div class="carte">
    <h2>Revenir au contenu d'origine</h2>
    <p class="aide">
        Une section « modifiée » a été retouchée depuis cet espace. La réinitialiser
        rétablit le contenu livré avec le site — utile si une modification a mal
        tourné. L'opération ne touche qu'une section à la fois.
    </p>
    @php $modifiees = collect($surchargees)->filter()->keys(); @endphp

    @if($modifiees->isEmpty())
        <p style="color:#999;font-size:14px">Aucune section modifiée pour l'instant : le site affiche son contenu d'origine.</p>
    @else
        @foreach($modifiees as $section)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-top:1px solid var(--gris)">
                <span style="font-weight:700;text-transform:capitalize">{{ \App\Support\Schema::page($section)['titre'] ?? $section }}</span>
                <form method="POST" action="{{ route('admin.reinitialiser', $section) }}" style="margin-left:auto"
                      onsubmit="return confirm('Revenir au contenu d\'origine ? Vos modifications de cette section seront perdues.')">
                    @csrf
                    <button type="submit" class="bt bt-lien" style="padding:4px">Réinitialiser</button>
                </form>
            </div>
        @endforeach
    @endif
</div>

@endsection
