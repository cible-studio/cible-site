{{-- ═══════════════════════════════════════════════════════════════════
     Écran d'édition générique, construit à partir du schéma.

     Un seul gabarit pour toutes les pages du site : les champs, leurs
     libellés, leurs types et leurs aides viennent de config/admin-schema.php.
     Rendre un contenu éditable ne demande donc pas de toucher à ce fichier.
════════════════════════════════════════════════════════════════════ --}}
@extends('admin._layout', ['titre' => $schema['titre'], 'connecte' => true, 'onglet' => $cle])

@section('contenu')

<div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap">
    <div style="flex:1;min-width:260px">
        <h1>{{ $schema['titre'] }}</h1>
        <p class="intro">{{ $schema['resume'] }}</p>
    </div>
    @if(!empty($schema['url']))
        <a class="bt bt-gris" href="{{ $schema['url'] }}" target="_blank" rel="noopener">Voir la page ↗</a>
    @endif
</div>

<form method="POST" action="{{ route('admin.page.enregistrer', $cle) }}" enctype="multipart/form-data">
    @csrf

    @foreach($schema['groupes'] as $groupe)
        <div class="carte">
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
                            <img src="{{ \App\Support\Contenu::urlImage($valeur) }}" alt=""
                                 style="width:100%;max-width:320px;border-radius:10px;display:block;margin-bottom:12px;border:1px solid var(--gris)">
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

    <div class="actions">
        <button type="submit" class="bt bt-rouge">Enregistrer</button>
        <a class="bt bt-gris" href="{{ route('admin.tableau') }}">Retour</a>
    </div>
</form>

@endsection
