{{-- Bande contact jaune, commune à toutes les pages (même place, même forme).
     <x-mq.bande-contact surtitre="…" titre="…" texte="…" bouton="…" />
     Coordonnées en grandes zones tactiles (≥ 64 px) : l'audit relevait des
     liens téléphone de 24 px de haut sur mobile. --}}
@props(['surtitre' => null, 'titre', 'texte' => null, 'bouton' => 'Recevoir ma recommandation média', 'formulaire' => true])
@php
    $co  = \App\Support\Contenu::section('coordonnees');
    $tel = fn (string $n) => 'tel:+' . preg_replace('#\D#', '', $n);
    $reass = array_slice(\App\Support\Contenu::lignes('contact.reass_liste'), 0, 2);
@endphp
<section class="section section--serre" aria-labelledby="bande-contact-titre">
    <div class="wrap">
        <div class="bande-contact rv">
            <div>
                @if($surtitre)<span class="sur">{{ $surtitre }}</span>@endif
                <h2 class="t2" id="bande-contact-titre">{{ $titre }}</h2>
                @if($texte)<p class="chapo">{{ $texte }}</p>@endif
                @if($formulaire)
                    <div class="actions" style="margin-top:28px">
                        <a class="btn btn--rouge" href="{{ route('marquette.contact') }}">{{ $bouton }}<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></a>
                    </div>
                @endif
                <ul class="reassurance">
                    @foreach($reass as $r)<li><svg class="ic" aria-hidden="true"><use href="#i-check"/></svg>{{ $r }}</li>@endforeach
                </ul>
            </div>
            <div class="coordonnees">
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
                    <span><small>Service commercial</small>{{ $co['email_commercial'] }}</span>
                </a>
            </div>
        </div>
    </div>
</section>
