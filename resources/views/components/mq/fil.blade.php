{{-- Fil d'Ariane des pages intérieures.
     <x-mq.fil :etapes="['Nos services']" /> — l'accueil est ajouté d'office. --}}
@props(['etapes' => []])
<nav class="fil" aria-label="Fil d'Ariane">
    <a href="{{ route('marquette.accueil') }}">Accueil</a>
    @foreach($etapes as $etape)
        <span aria-hidden="true">/</span>
        <span @if($loop->last) aria-current="page" @endif>{{ $etape }}</span>
    @endforeach
</nav>
