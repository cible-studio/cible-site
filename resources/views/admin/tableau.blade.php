@extends('admin._layout', ['titre' => 'Tableau de bord', 'connecte' => true, 'onglet' => 'tableau'])

@section('contenu')

<h1>Tableau de bord</h1>
<p class="intro">Modifiez le contenu du site. Les changements sont visibles immédiatement, sans redéploiement.</p>

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
@endunless

<div class="liste" style="margin-top:24px">
    @foreach([
        ['coordonnees',  'Coordonnées',   'Téléphones, emails et adresse. Affichés sur toutes les pages.', 'var(--rouge)'],
        ['chiffres',     'Chiffres clés', 'Panneaux, communes, années d\'expertise, distinctions.',        'var(--jaune)'],
        ['realisations', 'Réalisations',  'Les 6 projets : textes, visuels et couleur.',                   'var(--violet)'],
    ] as [$cle, $titre, $desc, $couleur])
        <a class="ligne" href="{{ route('admin.' . $cle) }}">
            <span class="pastille" style="background:{{ $couleur }}"></span>
            <span>
                <span class="nom">{{ $titre }}</span>
                <span class="cat" style="display:block">{{ $desc }}</span>
            </span>
            <span class="fleche">›</span>
        </a>
    @endforeach
</div>

<div class="carte">
    <h2>État du contenu</h2>
    <p class="aide">
        Une section « personnalisée » a été modifiée depuis cet espace. Une section
        « d'origine » affiche encore le contenu livré avec le site.
    </p>
    @foreach($surchargees as $section => $modifiee)
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-top:1px solid var(--gris)">
            <span style="font-weight:700;text-transform:capitalize;min-width:140px">{{ $section }}</span>
            <span style="font-size:13.5px;color:{{ $modifiee ? 'var(--vert)' : '#999' }}">
                {{ $modifiee ? 'Personnalisée' : "Contenu d'origine" }}
            </span>
            @if($modifiee)
                <form method="POST" action="{{ route('admin.reinitialiser', $section) }}" style="margin-left:auto"
                      onsubmit="return confirm('Revenir au contenu d\'origine du site ? Vos modifications de cette section seront perdues.')">
                    @csrf
                    <button type="submit" class="bt bt-lien" style="padding:4px">Réinitialiser</button>
                </form>
            @endif
        </div>
    @endforeach
</div>

@endsection
