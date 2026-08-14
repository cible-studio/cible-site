@extends('admin._layout', ['titre' => $projet['nom'], 'connecte' => true, 'onglet' => 'realisations'])

@section('contenu')

<p style="font-size:13.5px;color:#888;margin-bottom:6px">
    <a href="{{ route('admin.realisations') }}" style="color:#888">← Réalisations</a>
</p>
<h1>{{ $projet['nom'] }}</h1>
<p class="intro">
    Visible sur l'accueil, sur la page Réalisations et sur
    <a href="{{ route('realisation', $slug) }}" target="_blank" rel="noopener">sa page de détail ↗</a>.
</p>

<form method="POST" action="{{ route('admin.realisation.enregistrer', $slug) }}" enctype="multipart/form-data">
    @csrf

    <div class="carte">
        <h2>Identité</h2>

        <div class="duo">
            <div class="champ">
                <label for="nom">Nom du client</label>
                <input id="nom" type="text" name="nom" value="{{ old('nom', $projet['nom']) }}" required>
                @error('nom')<div class="erreur">{{ $message }}</div>@enderror
            </div>
            <div class="champ">
                <label for="couleur">Couleur d'accent</label>
                <select id="couleur" name="couleur" required>
                    @foreach([
                        'var(--rouge)'  => 'Rouge',
                        'var(--jaune)'  => 'Jaune',
                        'var(--vert)'   => 'Vert',
                        'var(--bleu)'   => 'Bleu',
                        'var(--violet)' => 'Violet',
                    ] as $val => $nom)
                        <option value="{{ $val }}" @selected(old('couleur', $projet['couleur']) === $val)>{{ $nom }}</option>
                    @endforeach
                </select>
                <div class="note">Limitée à la palette CIBLE.</div>
                @error('couleur')<div class="erreur">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="champ">
            <label for="cat">Nature du projet</label>
            <input id="cat" type="text" name="cat" value="{{ old('cat', $projet['cat']) }}" required>
            <div class="note">Ex. « Brand experience &amp; activation terrain ».</div>
            @error('cat')<div class="erreur">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="carte">
        <h2>Textes</h2>

        <div class="champ">
            <label for="titre">Accroche</label>
            <input id="titre" type="text" name="titre" value="{{ old('titre', $projet['titre']) }}" required>
            <div class="note">Une phrase, terminée par un point.</div>
            @error('titre')<div class="erreur">{{ $message }}</div>@enderror
        </div>

        <div class="champ">
            <label for="texte">Description</label>
            <textarea id="texte" name="texte" required>{{ old('texte', $projet['texte']) }}</textarea>
            <div class="note">2 à 3 phrases. Texte simple : la mise en forme vient du site.</div>
            @error('texte')<div class="erreur">{{ $message }}</div>@enderror
        </div>

        <div class="champ">
            <label for="services">Prestations</label>
            <input id="services" type="text" name="services" value="{{ old('services', $projet['services']) }}" required>
            <div class="note">Séparées par des points médians « · » — c'est ce qui découpe la liste sur la page de détail.</div>
            @error('services')<div class="erreur">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="carte">
        <h2>Visuel</h2>
        <p class="aide">Format paysage recommandé, au moins 1200 px de large. JPG, PNG ou WebP, 5 Mo maximum.</p>

        @if(\App\Support\Contenu::imageExiste($projet['image']))
            <img src="{{ \App\Support\Contenu::urlImage($projet['image']) }}" alt=""
                 style="width:100%;max-width:420px;border-radius:10px;display:block;margin-bottom:16px">
        @else
            <div class="alerte a-attention">Le visuel actuel est introuvable.</div>
        @endif

        <div class="champ">
            <label for="image">Remplacer le visuel</label>
            <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
            <div class="note">Laissez vide pour conserver l'image actuelle.</div>
            @error('image')<div class="erreur">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="actions">
        <button type="submit" class="bt bt-rouge">Enregistrer</button>
        <a class="bt bt-gris" href="{{ route('admin.realisations') }}">Annuler</a>
    </div>
</form>

@endsection
