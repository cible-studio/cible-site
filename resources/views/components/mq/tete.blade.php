{{-- En-tête de section : surtitre (trait rouge), titre, introduction.
     <x-mq.tete surtitre="…" titre="…" intro="…" centre />
     · centre : centré sur grand écran (aligné à gauche sur mobile, cf. mq.css) ;
     · ligne  : titre à gauche, introduction à droite sur grand écran.
     Texte déjà échappé (emphase de Contenu::riche, <em>) : passer le titre
     par le slot par défaut, l'introduction par le slot nommé « intro ». --}}
@props(['surtitre' => null, 'titre' => null, 'intro' => null, 'centre' => false, 'ligne' => false, 'niveau' => 'h2', 'idTitre' => null])
@php
    $classeTitre = ($niveau === 'h1' ? 't1' : 't2') . ' riche';
    $aIntro = $intro !== null && (string) $intro !== '';
@endphp
<div {{ $attributes->class(['tete', 'tete--centre' => $centre, 'tete--ligne' => $ligne, 'rv']) }}>
    @if($ligne)<div>@endif
    @if($surtitre)<span class="sur">{{ $surtitre }}</span>@endif
    <{{ $niveau }} class="{{ $classeTitre }}" @if($idTitre) id="{{ $idTitre }}" @endif>{{ $titre ?? '' }}{{ $slot }}</{{ $niveau }}>
    @if($ligne)</div>@endif
    @if($aIntro)<p class="chapo riche">{{ $intro }}</p>@endif
</div>
