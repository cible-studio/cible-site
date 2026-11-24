{{-- Engagements affichés sous un bouton d'action (audit PPC.io : dire ce
     qui se passe après le clic). Mêmes deux lignes partout, tirées de
     contact.reass_liste (espace admin).
     <x-mq.reassurance /> --}}
@props(['nombre' => 2])
<ul {{ $attributes->class(['reassurance']) }}>
    @foreach(array_slice(\App\Support\Contenu::lignes('contact.reass_liste'), 0, $nombre) as $r)
        <li><svg class="ic" aria-hidden="true"><use href="#i-check"/></svg>{{ $r }}</li>
    @endforeach
</ul>
