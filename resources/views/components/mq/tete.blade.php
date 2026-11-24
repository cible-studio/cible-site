{{-- En-tête de section : surtitre (trait rouge), titre, introduction.
     <x-mq.tete surtitre="…" titre="…" intro="…" centre />
     Le titre accepte du HTML déjà échappé via le slot nommé « titre » quand
     il faut l'emphase de Contenu::riche(). --}}
@props(['surtitre' => null, 'titre' => null, 'intro' => null, 'centre' => false, 'niveau' => 'h2', 'idTitre' => null])
<div {{ $attributes->class(['tete', 'tete--centre' => $centre, 'rv']) }}>
    @if($surtitre)<span class="sur">{{ $surtitre }}</span>@endif
    <{{ $niveau }} class="{{ $niveau === 'h1' ? 't1' : 't2' }} riche" @if($idTitre) id="{{ $idTitre }}" @endif>{{ $titre ?? '' }}{{ $slot ?? '' }}</{{ $niveau }}>
    @if($intro)<p class="chapo" @if($centre) style="margin-inline:auto" @endif>{{ $intro }}</p>@endif
</div>
