{{-- Chiffre clé. La valeur finale est écrite en clair dans le HTML : Google,
     les lecteurs d'écran et un navigateur sans JavaScript lisent le bon
     nombre (l'audit relevait « 0 communes atteintes »). mq.js anime ensuite
     de 0 à la valeur, seulement si le mouvement est permis.
     <x-mq.chiffre :valeur="400" plus libelle="Panneaux en exploitation" /> --}}
@props(['valeur', 'libelle', 'plus' => false, 'anime' => true])
<div {{ $attributes->class(['chiffre']) }}>
    <div class="chiffre__v num">@if($plus)<span class="plus" aria-hidden="true">+</span>@endif<span @if($anime && is_numeric($valeur)) data-compte="{{ $valeur }}" @endif>{{ $valeur }}</span></div>
    <div class="chiffre__l">@if($plus)<span class="vh">Plus de </span>@endif{{ $libelle }}</div>
</div>
