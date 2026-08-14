@extends('admin._layout', ['titre' => 'Chiffres clés', 'connecte' => true, 'onglet' => 'chiffres'])

@section('contenu')

<h1>Chiffres clés</h1>
<p class="intro">Ces nombres alimentent les compteurs animés de l'accueil, de la page Qui sommes-nous et de la page Réseau.</p>

<div class="alerte a-info" style="margin-top:22px">
    Saisissez <strong>uniquement le nombre</strong>. Le « + » de « +400 panneaux » est ajouté
    par le site : s'il était saisi ici, l'animation du compteur l'effacerait.
</div>

<div class="alerte a-attention">
    Ce sont des affirmations publiques, reprises dans les pages, les descriptions
    Google et le formulaire. Ne les modifiez que sur une information confirmée.
</div>

<form method="POST" action="{{ route('admin.chiffres.enregistrer') }}">
    @csrf

    <div class="carte">
        <div class="duo">
            <div class="champ">
                <label for="panneaux">Panneaux exploités</label>
                <input id="panneaux" type="text" inputmode="numeric" name="panneaux" value="{{ old('panneaux', $valeurs['panneaux']) }}" required>
                <div class="note">Affiché « +{{ $valeurs['panneaux'] }} panneaux » sur le site.</div>
                @error('panneaux')<div class="erreur">{{ $message }}</div>@enderror
            </div>
            <div class="champ">
                <label for="communes">Communes et villes couvertes</label>
                <input id="communes" type="text" inputmode="numeric" name="communes" value="{{ old('communes', $valeurs['communes']) }}" required>
                <div class="note">Doit correspondre au nombre de points de la carte du réseau.</div>
                @error('communes')<div class="erreur">{{ $message }}</div>@enderror
            </div>
            <div class="champ">
                <label for="annees">Années d'expertise</label>
                <input id="annees" type="text" inputmode="numeric" name="annees" value="{{ old('annees', $valeurs['annees']) }}" required>
                <div class="note">CIBLE a été fondée en 1994.</div>
                @error('annees')<div class="erreur">{{ $message }}</div>@enderror
            </div>
            <div class="champ">
                <label for="distinctions">Distinctions d'État</label>
                <input id="distinctions" type="text" inputmode="numeric" name="distinctions" value="{{ old('distinctions', $valeurs['distinctions']) }}" required>
                <div class="note">2016, 2019 et 2020.</div>
                @error('distinctions')<div class="erreur">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="actions">
        <button type="submit" class="bt bt-rouge">Enregistrer</button>
        <a class="bt bt-gris" href="{{ route('admin.tableau') }}">Annuler</a>
    </div>
</form>

@endsection
