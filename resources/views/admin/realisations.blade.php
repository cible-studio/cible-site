@extends('admin._layout', ['titre' => 'Réalisations', 'connecte' => true, 'onglet' => 'realisations'])

@section('contenu')

<h1>Réalisations</h1>
<p class="intro">Les 6 projets affichés sur l'accueil, sur la page Réalisations et sur leur page de détail.</p>

<div class="liste" style="margin-top:24px">
    @foreach($projets as $slug => $p)
        <a class="ligne" href="{{ route('admin.realisation.editer', $slug) }}">
            @if(\App\Support\Contenu::imageExiste($p['image']))
                <img class="vign" src="{{ \App\Support\Contenu::urlImage($p['image']) }}" alt="">
            @else
                <span class="vign"></span>
            @endif
            <span>
                <span class="nom">{{ $p['nom'] }}</span>
                <span class="cat" style="display:block">{{ $p['cat'] }}</span>
            </span>
            <span class="pastille" style="background:{{ $p['couleur'] }}"></span>
            <span class="fleche">›</span>
        </a>
    @endforeach
</div>

<div class="carte">
    <h2>Ajouter ou supprimer une réalisation</h2>
    <p class="aide" style="margin-bottom:0">
        Les 6 réalisations sont fixes pour l'instant : leurs filtres, leur ordre et
        leurs adresses web sont définis dans le code. Vous pouvez modifier librement
        leurs textes, leur visuel et leur couleur. Pour en ajouter ou en retirer,
        passez par le studio — cela demande aussi de rattacher le projet aux bons
        filtres de la page Réalisations.
    </p>
</div>

@endsection
