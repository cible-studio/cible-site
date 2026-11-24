{{-- Coordonnées de CIBLE en grandes zones tactiles (≥ 64 px) : l'audit
     relevait des liens téléphone de 24 px de haut sur mobile.
     <x-mq.coordonnees />          → mobile, fixe, service commercial
     <x-mq.coordonnees complet />  → + secrétariat et adresse (page contact)
     Une adresse e-mail ne se coupe qu'avant « @ », jamais au tiret de
     « cible-ci » (le navigateur coupait « commercial@cible- / ci.com »). --}}
@props(['complet' => false])
@php
    $co  = \App\Support\Contenu::section('coordonnees');
    $tel = fn (string $n) => 'tel:+' . preg_replace('#\D#', '', $n);
    $courriel = fn (string $a) => new \Illuminate\Support\HtmlString(
        e(\Illuminate\Support\Str::before($a, '@'))
        . '<wbr><span class="insecable">@' . e(\Illuminate\Support\Str::after($a, '@')) . '</span>'
    );
@endphp
<div {{ $attributes->class(['coordonnees']) }}>
    <a class="coord" href="{{ $tel($co['tel_mobile']) }}">
        <span class="rond"><svg class="ic" aria-hidden="true"><use href="#i-tel"/></svg></span>
        <span><small>Mobile</small><span class="num">{{ $co['tel_mobile'] }}</span></span>
    </a>
    @if($co['tel_fixe'])
        <a class="coord" href="{{ $tel($co['tel_fixe']) }}">
            <span class="rond"><svg class="ic" aria-hidden="true"><use href="#i-tel"/></svg></span>
            <span><small>Fixe</small><span class="num">{{ $co['tel_fixe'] }}</span></span>
        </a>
    @endif
    <a class="coord" href="mailto:{{ $co['email_commercial'] }}">
        <span class="rond"><svg class="ic" aria-hidden="true"><use href="#i-mail"/></svg></span>
        <span><small>Service commercial</small>{{ $courriel($co['email_commercial']) }}</span>
    </a>
    @if($complet && $co['email_secretariat'])
        <a class="coord" href="mailto:{{ $co['email_secretariat'] }}">
            <span class="rond"><svg class="ic" aria-hidden="true"><use href="#i-mail"/></svg></span>
            <span><small>Secrétariat</small>{{ $courriel($co['email_secretariat']) }}</span>
        </a>
    @endif
    @if($complet)
        <div class="coord coord--adresse">
            <span class="rond"><svg class="ic" aria-hidden="true"><use href="#i-lieu"/></svg></span>
            <span><small>Adresse</small>{{ $co['adresse_rue'] }}@if($co['adresse_complement'])<span class="coord__suite">{{ $co['adresse_complement'] }}</span>@endif</span>
        </div>
    @endif
</div>
