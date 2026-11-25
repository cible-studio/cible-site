{{-- Double authentification (2026-11-25) : saisie du code reçu par e-mail.
     Même présentation que la page de connexion. --}}
@extends('admin._layout', ['titre' => 'Vérification'])

@section('contenu')

<div style="max-width:420px;margin:8vh auto 0">
    <div style="text-align:center;margin-bottom:26px">
        <img src="{{ asset('images/icone-192.png') }}" alt="CIBLE" style="height:64px;width:auto;display:inline-block">
        <h1 style="margin-top:16px;font-size:22px">Vérification</h1>
        <p class="intro">Un code à 6 chiffres vient d'être envoyé à <strong>{{ $email }}</strong>. Il est valable {{ $minutes }} minutes.</p>
    </div>

    <div class="carte" style="margin-top:0">
        @if($errors->has('code'))
            <div class="alerte a-ko">{{ $errors->first('code') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.verifier') }}">
            @csrf
            <div class="champ">
                <label for="code">Code de connexion</label>
                {{-- one-time-code : le téléphone propose le code reçu par e-mail. --}}
                <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9 ]*" maxlength="9"
                       autocomplete="one-time-code" required autofocus
                       style="font-size:26px;letter-spacing:8px;text-align:center;font-weight:700">
            </div>
            <button type="submit" class="bt bt-rouge" style="width:100%;justify-content:center">Valider et se connecter</button>
        </form>

        <form method="POST" action="{{ route('admin.renvoyer') }}" style="margin-top:14px;text-align:center">
            @csrf
            <button type="submit" class="bt" style="background:none;color:#111;text-decoration:underline;padding:10px 12px">Je n'ai rien reçu : renvoyer un code</button>
        </form>
    </div>

    <p style="text-align:center;margin-top:20px;font-size:13px;color:#999">
        Pensez à vérifier les courriers indésirables.<br>
        <a href="{{ route('admin.login') }}" style="color:#999">← Revenir à la connexion</a>
    </p>
</div>

@endsection
