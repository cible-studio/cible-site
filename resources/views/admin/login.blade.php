@extends('admin._layout', ['titre' => 'Connexion'])

@section('contenu')

<div style="max-width:420px;margin:8vh auto 0">
    <div style="text-align:center;margin-bottom:26px">
        <img src="{{ asset('images/icone-192.png') }}" alt="CIBLE" style="height:64px;width:auto;display:inline-block">
        <h1 style="margin-top:16px;font-size:22px">Administration du site</h1>
        <p class="intro">Réservé à l'équipe CIBLE.</p>
    </div>

    <div class="carte" style="margin-top:0">
        @if($errors->has('email'))
            <div class="alerte a-ko">{{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.authentifier') }}">
            @csrf
            <div class="champ">
                <label for="email">Adresse email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" autofocus>
            </div>
            <div class="champ">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="bt bt-rouge" style="width:100%;justify-content:center">Se connecter</button>
        </form>
    </div>

    <p style="text-align:center;margin-top:20px;font-size:13px;color:#999">
        <a href="{{ route('home') }}" style="color:#999">← Retour au site</a>
    </p>
</div>

@endsection
