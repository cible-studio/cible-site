{{-- ═══════════════════════════════════════════════════════════════════
     Écran d'édition générique, construit à partir du schéma.

     Un seul gabarit pour toutes les pages du site : les champs, leurs
     libellés, leurs types et leurs aides viennent de config/admin-schema.php.
     Rendre un contenu éditable ne demande donc pas de toucher à ce fichier.

     La page d'accueil compte 76 champs répartis en 13 blocs. D'où deux
     ajouts qui ne sont pas cosmétiques : un sommaire collant pour atteindre
     un bloc sans faire défiler, et une barre d'enregistrement collante —
     sans elle, il faut redescendre jusqu'en bas pour valider une correction
     faite en haut de page.
════════════════════════════════════════════════════════════════════ --}}
@extends('admin._layout', [
    'titre'     => $schema['titre'],
    'connecte'  => true,
    'onglet'    => $cle,
    'lienPage'  => $schema['url'] ?? null,
])

@section('contenu')

<style>
    .sommaire{
        position:sticky;top:60px;z-index:20;
        background:#F4F4F2;padding:14px 0 12px;margin-bottom:-8px;
        display:flex;gap:8px;overflow-x:auto;scrollbar-width:thin;
    }
    .sommaire a{
        flex:0 0 auto;padding:7px 14px;border-radius:999px;
        background:#fff;border:1px solid var(--gris);
        font-size:13px;font-weight:600;color:#555;text-decoration:none;white-space:nowrap;
    }
    .sommaire a:hover{border-color:#BBB;color:var(--noir)}

    .barre-enr{
        position:sticky;bottom:0;z-index:20;margin-top:26px;
        background:rgba(255,255,255,.94);backdrop-filter:blur(8px);
        border:1px solid var(--gris);border-radius:14px;
        padding:14px 18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;
        box-shadow:0 -6px 24px -18px rgba(0,0,0,.4);
    }
    .barre-enr .compte{margin-left:auto;font-size:13px;color:#777}

    .apercu-img{width:100%;max-width:320px;border-radius:10px;display:block;
        margin-bottom:12px;border:1px solid var(--gris)}
</style>

<h1>{{ $schema['titre'] }}</h1>
<p class="intro">{{ $schema['resume'] }}</p>

{{-- Sommaire : au-delà de 3 blocs, le défilement devient le vrai coût
     d'utilisation de l'écran. --}}
@if(count($schema['groupes']) > 3)
    <nav class="sommaire" aria-label="Blocs de la page">
        @foreach($schema['groupes'] as $i => $groupe)
            <a href="#bloc-{{ $i }}">{{ $groupe['titre'] }}</a>
        @endforeach
    </nav>
@endif

<form method="POST" action="{{ route('admin.page.enregistrer', $cle) }}" enctype="multipart/form-data">
    @csrf

    @foreach($schema['groupes'] as $i => $groupe)
        <div class="carte" id="bloc-{{ $i }}">
            <h2>{{ $groupe['titre'] }}</h2>
            @if(!empty($groupe['aide']))
                <p class="aide">{{ $groupe['aide'] }}</p>
            @endif

            @foreach($groupe['champs'] as $nom => $def)
                @php
                    $type   = $def['type'] ?? 'texte';
                    $valeur = old($nom, $valeurs[$nom] ?? ($def['defaut'] ?? ''));
                @endphp

                @if($type === 'image')
                    <div class="champ">
                        <label for="c-{{ $nom }}">{{ $def['label'] }}</label>
                        @if($valeur && \App\Support\Contenu::imageExiste($valeur))
                            <img src="{{ \App\Support\Contenu::urlImage($valeur) }}" alt="" class="apercu-img">
                        @else
                            <div class="alerte a-attention" style="margin-bottom:12px">Visuel actuel introuvable.</div>
                        @endif
                        <input id="c-{{ $nom }}" type="file" name="{{ $nom }}" accept=".jpg,.jpeg,.png,.webp">
                        <div class="note">{{ $def['aide'] ?? 'Laissez vide pour conserver l\'image actuelle.' }}</div>
                        @error($nom)<div class="erreur">{{ $message }}</div>@enderror
                    </div>

                @elseif($type === 'zone')
                    <div class="champ">
                        <label for="c-{{ $nom }}">{{ $def['label'] }}</label>
                        <textarea id="c-{{ $nom }}" name="{{ $nom }}" required
                                  style="min-height:{{ mb_strlen($valeur) > 260 ? 130 : 80 }}px">{{ $valeur }}</textarea>
                        @if(!empty($def['aide']))<div class="note">{{ $def['aide'] }}</div>@endif
                        @error($nom)<div class="erreur">{{ $message }}</div>@enderror
                    </div>

                @elseif($type === 'liste')
                    <div class="champ">
                        <label for="c-{{ $nom }}">{{ $def['label'] }}</label>
                        <textarea id="c-{{ $nom }}" name="{{ $nom }}"
                                  style="min-height:130px;font-family:ui-monospace,monospace;font-size:14px">{{ $valeur }}</textarea>
                        <div class="note">{{ $def['aide'] ?? 'Une entrée par ligne.' }}</div>
                        @error($nom)<div class="erreur">{{ $message }}</div>@enderror
                    </div>

                @elseif($type === 'nombre')
                    <div class="champ">
                        <label for="c-{{ $nom }}">{{ $def['label'] }}</label>
                        <input id="c-{{ $nom }}" type="text" inputmode="numeric" name="{{ $nom }}" value="{{ $valeur }}" required>
                        @if(!empty($def['aide']))<div class="note">{{ $def['aide'] }}</div>@endif
                        @error($nom)<div class="erreur">{{ $message }}</div>@enderror
                    </div>

                @else
                    <div class="champ">
                        <label for="c-{{ $nom }}">{{ $def['label'] }}</label>
                        <input id="c-{{ $nom }}" type="text" name="{{ $nom }}" value="{{ $valeur }}"
                               @if(!($def['facultatif'] ?? false)) required @endif>
                        @if(!empty($def['aide']))<div class="note">{{ $def['aide'] }}</div>@endif
                        @error($nom)<div class="erreur">{{ $message }}</div>@enderror
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach

    <div class="barre-enr">
        <button type="submit" class="bt bt-rouge">Enregistrer</button>
        <a class="bt bt-gris bt-petit" href="{{ route('admin.tableau') }}">Retour</a>
        <span class="compte">{{ count(\App\Support\Schema::champs($cle)) }} champs sur cette page</span>
    </div>
</form>

@endsection
