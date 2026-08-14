@extends('admin._layout', ['titre' => 'Tableau de bord', 'connecte' => true, 'onglet' => 'tableau'])

@section('contenu')

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

<h2 style="font-size:15px;font-weight:800;margin-top:30px;text-transform:uppercase;letter-spacing:.06em;color:#777">Les pages du site</h2>
<div class="liste" style="margin-top:14px">
    @foreach(\App\Support\Schema::pages() as $cle => $page)
        <a class="ligne" href="{{ route('admin.page', $cle) }}">
            <span class="pastille" style="background:{{ $page['couleur'] }}"></span>
            <span>
                <span class="nom">{{ $page['titre'] }}</span>
                <span class="cat" style="display:block">{{ $page['resume'] }}</span>
            </span>
            <span class="cat" style="margin-left:auto;white-space:nowrap">
                {{ count(\App\Support\Schema::champs($cle)) }} champs
                @if($surchargees[$cle] ?? false)
                    · <span style="color:var(--vert)">modifiée</span>
                @endif
            </span>
            <span class="fleche">›</span>
        </a>
    @endforeach
</div>

<h2 style="font-size:15px;font-weight:800;margin-top:34px;text-transform:uppercase;letter-spacing:.06em;color:#777">Contenus transversaux</h2>
<p class="intro" style="font-size:13.5px">Repris sur plusieurs pages à la fois.</p>
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
