{{-- Bande contact jaune, commune à toutes les pages (même place, même forme),
     sauf la page contact.
     <x-mq.bande-contact surtitre="…" titre="…" texte="…" bouton="…" /> --}}
@props(['surtitre' => null, 'titre', 'texte' => null, 'bouton' => 'Recevoir ma recommandation média', 'formulaire' => true])
<section class="section section--serre" aria-labelledby="bande-contact-titre">
    <div class="wrap">
        <div class="bande-contact rv">
            <div>
                @if($surtitre)<span class="sur">{{ $surtitre }}</span>@endif
                <h2 class="t2" id="bande-contact-titre">{{ $titre }}</h2>
                @if($texte)<p class="chapo">{{ $texte }}</p>@endif
                @if($formulaire)
                    <div class="actions">
                        <a class="btn btn--rouge" href="{{ route('marquette.contact') }}">{{ $bouton }}<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg></a>
                    </div>
                @endif
                <x-mq.reassurance />
            </div>
            <x-mq.coordonnees />
        </div>
    </div>
</section>
