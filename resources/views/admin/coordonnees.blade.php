@extends('admin._layout', ['titre' => 'Coordonnées', 'connecte' => true, 'onglet' => 'coordonnees'])

@section('contenu')

<h1>Coordonnées</h1>
<p class="intro">Ces informations apparaissent dans le pied de page de toutes les pages, sur la page Contact et dans les données structurées lues par Google.</p>

<form method="POST" action="{{ route('admin.coordonnees.enregistrer') }}">
    @csrf

    <div class="carte">
        <h2>Téléphones</h2>
        <p class="aide">Saisissez-les au format international, espaces compris : c'est ce format qui s'affiche, et le lien d'appel est construit automatiquement.</p>

        <div class="duo">
            <div class="champ">
                <label for="tel_mobile">Mobile (principal)</label>
                <input id="tel_mobile" type="text" name="tel_mobile" value="{{ old('tel_mobile', $valeurs['tel_mobile']) }}" required>
                @error('tel_mobile')<div class="erreur">{{ $message }}</div>@enderror
            </div>
            <div class="champ">
                <label for="tel_fixe">Fixe</label>
                <input id="tel_fixe" type="text" name="tel_fixe" value="{{ old('tel_fixe', $valeurs['tel_fixe']) }}">
                @error('tel_fixe')<div class="erreur">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="carte">
        <h2>Adresses email</h2>
        <p class="aide">L'email commercial est celui affiché aux visiteurs. Il ne détermine pas où arrivent les demandes du formulaire — cela se règle dans les variables d'environnement.</p>

        <div class="duo">
            <div class="champ">
                <label for="email_commercial">Commercial</label>
                <input id="email_commercial" type="email" name="email_commercial" value="{{ old('email_commercial', $valeurs['email_commercial']) }}" required>
                @error('email_commercial')<div class="erreur">{{ $message }}</div>@enderror
            </div>
            <div class="champ">
                <label for="email_secretariat">Secrétariat</label>
                <input id="email_secretariat" type="email" name="email_secretariat" value="{{ old('email_secretariat', $valeurs['email_secretariat']) }}">
                @error('email_secretariat')<div class="erreur">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="carte">
        <h2>Adresse postale</h2>

        <div class="champ">
            <label for="adresse_rue">Rue</label>
            <input id="adresse_rue" type="text" name="adresse_rue" value="{{ old('adresse_rue', $valeurs['adresse_rue']) }}" required>
            @error('adresse_rue')<div class="erreur">{{ $message }}</div>@enderror
        </div>

        <div class="champ">
            <label for="adresse_complement">Complément</label>
            <input id="adresse_complement" type="text" name="adresse_complement" value="{{ old('adresse_complement', $valeurs['adresse_complement']) }}">
            <div class="note">Quartier, boîte postale, ville.</div>
            @error('adresse_complement')<div class="erreur">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="actions">
        <button type="submit" class="bt bt-rouge">Enregistrer</button>
        <a class="bt bt-gris" href="{{ route('admin.tableau') }}">Annuler</a>
    </div>
</form>

@endsection
