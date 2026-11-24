{{-- ═══════════════════════════════════════════════════════════════════
     Maquette — CONTACT. Textes : Contenu::get('contact.*') et
     Contenu::section('coordonnees') (espace admin), inchangés ; libellés
     du formulaire repris mot pour mot de resources/views/contact.blade.php.

     Le formulaire est le VRAI : il poste sur la route /devis, avec les
     mêmes noms de champs, les mêmes options (CibleController::formOptions())
     et les mêmes protections (pots de miel, jeton _ts, Turnstile). En cas
     d'erreur, Laravel revient ici (back()) et l'étape fautive se rouvre ;
     un envoi réussi part vers /contact/merci, page de conversion de GTM.

       1. héro compact : sur ordinateur, le formulaire apparaît dès le
          premier écran ;
       2. formulaire en 4 étapes à gauche ; à droite coordonnées,
          engagements et arguments. La colonne la plus courte accompagne le
          défilement (contact.js) : une étape courte ne laisse plus de vide
          face à une colonne longue — le défaut qui avait fait abandonner
          les deux colonnes le 2026-08-13.
     Pas de bande contact en bas : on est sur la page contact.
════════════════════════════════════════════════════════════════════ --}}
@extends('marquette.layout')
@use('App\Support\Contenu')
@use('App\Support\Turnstile')
@use('App\Http\Controllers\CibleController')

@section('titre', 'Contact — CIBLE · Recevoir une recommandation média')
@section('description', 'Décrivez votre projet : objectifs, audiences, zones, services et budget. Notre équipe vous répond sous 24 heures ouvrées avec une recommandation média personnalisée.')

@php
    $opt  = CibleController::formOptions();
    $nbsp = "\u{00A0}";

    // Erreur d'un groupe de choix : portée par le champ (`periode`) ou par
    // un élément du tableau (`objectif.0`) — cf. vue actuelle.
    $errChoix = fn (string $cle) => $errors->first($cle) ?: $errors->first($cle . '.*');

    // Ancienne saisie d'un champ texte. Un tableau (envoi forgé) ne doit
    // pas faire planter la page au retour d'erreur.
    $ancien = fn (string $cle) => is_string($v = old($cle)) ? $v : '';

    // Messages de la vérification côté navigateur : ceux que renverrait le
    // serveur (lang/fr/validation.php), jamais un texte écrit pour l'occasion.
    $nomChamp = fn (string $cle) => __("validation.attributes.$cle");
    $msgRequis = fn (string $cle) => __('validation.required', ['attribute' => $nomChamp($cle)]);

    // Documents : mêmes formats et même poids que CibleController
    // (DOC_EXT, DOC_MAX_KO — constantes privées) et que la vue actuelle.
    $formats = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'ai', 'eps', 'svg', 'zip', 'doc', 'docx', 'ppt', 'pptx'];
    $maxKo   = 10240;

    // Informations de contact : [nom, libellé, type, longueur max,
    // exemple, autocomplete, inputmode, pleine largeur].
    $champsContact = [
        ['nom',        'Nom et prénom', 'text',  100, 'Ex. Jean Dupont',          'name',               null,    false],
        ['entreprise', 'Entreprise',    'text',  150, 'Nom de votre entreprise',  'organization',       null,    false],
        ['poste',      'Fonction',      'text',  100, 'Ex. Directrice Marketing', 'organization-title', null,    true],
        ['email',      'Adresse email', 'email', 150, 'nom@entreprise.ci',        'email',              'email', false],
        ['tel',        'Téléphone',     'tel',   30,  '+225 …',                   'tel',                'tel',   false],
    ];

    // Les 4 étapes et 7 blocs de la vue actuelle :
    // étape => [nom court, blocs[ [type, n°, titre, groupes de choix] ]].
    $etapes = [
        1 => ['Vous', [
            ['contact', 1, 'Informations sur le contact', []],
        ]],
        2 => ['Votre projet', [
            ['choix', 2, 'Parlons de votre projet', [
                'objectif' => "Quel est votre objectif principal{$nbsp}?",
                'cible'    => "Qui souhaitez-vous toucher{$nbsp}?",
                'zone'     => "Où souhaitez-vous communiquer{$nbsp}?",
                'periode'  => "Quand souhaitez-vous lancer votre campagne{$nbsp}?",
            ]],
        ]],
        3 => ['Vos besoins', [
            ['choix', 3, 'Les services recherchés', ['services' => "Quels services vous intéressent{$nbsp}?"]],
            ['choix', 4, 'Budget', ['budget' => "Quel budget envisagez-vous{$nbsp}?"]],
        ]],
        4 => ['Votre brief', [
            ['message',   5, 'Description', []],
            ['documents', 6, 'Vos documents', []],
            // Pas de question : le titre du bloc sert d'intitulé au groupe.
            ['choix',     7, "Comment nous avez-vous connus{$nbsp}?", ['provenance' => null]],
        ]],
    ];
    $radios = ['periode', 'budget', 'provenance'];

    // Retour d'erreur du serveur : on rouvre directement l'étape fautive.
    $champsParEtape = [
        1 => ['nom', 'entreprise', 'poste', 'email', 'tel'],
        2 => ['objectif', 'cible', 'zone', 'periode'],
        3 => ['services', 'budget'],
        4 => ['message', 'doc_brief', 'doc_logo', 'doc_charte', 'doc_cahier', 'provenance', 'consentement', 'turnstile'],
    ];
    $etapeDepart = 1;
    foreach ($champsParEtape as $n => $champs) {
        foreach ($champs as $champ) {
            if ($errors->has($champ) || $errors->has($champ . '.*')) { $etapeDepart = $n; break 2; }
        }
    }
    $aDesErreurs = $errors->any() || session('devis_error');

    // Pastilles de « Pourquoi CIBLE » : cycle des 5 couleurs (vue actuelle).
    $tons = ['var(--rouge)', 'var(--jaune)', 'var(--vert)', 'var(--bleu)', 'var(--violet)'];
@endphp

@push('css')
    <link rel="stylesheet" href="{{ \App\Support\Marquette::asset('pages/contact.css') }}">
@endpush

@push('js')
    @if(Turnstile::actif())
        {{-- Widget anti-robot, chargé seulement si les clés sont renseignées. --}}
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    <script src="{{ \App\Support\Marquette::asset('pages/contact.js') }}" defer></script>
@endpush

@section('contenu')

{{-- ═══ 1. HÉRO ═══ Titre à gauche, texte à droite : le héro reste bas et
     le formulaire remonte dans le premier écran. --}}
<section class="page-hero contact-hero" aria-labelledby="contact-titre">
    <span class="decor decor--fleche page-hero__fleche" aria-hidden="true"></span>
    <div class="wrap">
        <x-mq.fil :etapes="['Contact']" />
        <div class="contact-hero__grille">
            <div class="rv">
                <span class="sur">{{ Contenu::get('contact.hero_surtitre') }}</span>
                <h1 class="t1 riche" id="contact-titre">{!! Contenu::riche('contact.hero_titre') !!}</h1>
            </div>
            <div class="rv" style="--d:.08s">
                <p class="chapo">{{ Contenu::get('contact.hero_texte') }}</p>
                <p class="accroche riche">{!! Contenu::riche('contact.hero_accroche') !!}</p>
            </div>
        </div>

        <ul class="benefices rv" style="--d:.16s">
            @foreach(Contenu::lignes('contact.benefices') as $b)
                <li><span class="rond" aria-hidden="true"><svg class="ic"><use href="#i-check"/></svg></span>{{ $b }}</li>
            @endforeach
        </ul>
    </div>
</section>

{{-- ═══ 2. FORMULAIRE + COORDONNÉES ═══ --}}
<section class="section section--motif contact-corps" aria-labelledby="form-titre">
    <div class="wrap contact-grille">

        {{-- ── Formulaire (en premier : sur mobile, il passe avant les coordonnées) ── --}}
        <div class="contact-grille__form" id="formulaire">
            <div class="form-carte">
                @if(session('devis_sent'))
                    {{-- Affiché si la maquette sert un jour /contact/merci. --}}
                    <div class="succes" role="status">
                        <span class="succes__rond" aria-hidden="true"><svg class="ic"><use href="#i-check"/></svg></span>
                        <h2 id="form-titre">Votre demande a bien été reçue !</h2>
                        <p>{{ Contenu::get('contact.form_succes') }}</p>
                    </div>
                @else
                    <h2 id="form-titre">{{ Contenu::get('contact.form_titre') }}</h2>
                    <p class="form-carte__sous">{{ Contenu::get('contact.form_sous') }}</p>

                    @if($aDesErreurs)
                        <div class="alerte" id="form-alerte" role="alert" tabindex="-1">
                            <span class="alerte__signe" aria-hidden="true">!</span>
                            <div>
                                @if(session('devis_error'))<p>{{ session('devis_error') }}</p>@endif
                                @if($errors->any())<p>Merci de vérifier les champs indiqués avant de valider votre demande.</p>@endif
                            </div>
                        </div>
                    @endif

                    {{-- enctype obligatoire : le bloc 6 accepte des documents.
                         novalidate : la vérification est faite étape par étape
                         (contact.js), le serveur reste seul juge. --}}
                    <form method="POST" action="{{ route('devis.submit') }}" enctype="multipart/form-data" novalidate
                          id="form-brief" data-etape-depart="{{ $etapeDepart }}"
                          data-formats="{{ implode(',', $formats) }}" data-max-ko="{{ $maxKo }}">
                        @csrf

                        {{-- Pièges anti-robot (identiques à la vue actuelle) : deux
                             champs aux noms plausibles, hors écran, ignorés par le
                             clavier et les gestionnaires de mots de passe. --}}
                        <div class="honeypot" aria-hidden="true" style="position:absolute;left:-9999px;visibility:hidden">
                            <label>Ne pas remplir <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                            <label>Ne pas remplir <input type="text" name="company_url" tabindex="-1" autocomplete="off"></label>
                        </div>

                        {{-- Piège temporel : instant d'ouverture chiffré côté serveur. --}}
                        <input type="hidden" name="_ts" value="{{ \App\Support\AntiSpam::jeton() }}">

                        {{-- Progression : affichée par contact.js (sans lui, les 4
                             étapes sont visibles d'un seul tenant). --}}
                        <div class="progres" id="progres" hidden>
                            <p class="progres__texte" id="et-compte" aria-live="polite"></p>
                            <ol class="progres__pas" aria-hidden="true">
                                @foreach($etapes as $n => [$nomEtape])
                                    <li data-pas="{{ $n }}">
                                        <span class="progres__barre"></span>
                                        <span class="progres__nom"><span class="num">{{ $n }}</span>{{ $nomEtape }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        @foreach($etapes as $n => [$nomEtape, $blocs])
                            <div class="etape" data-etape="{{ $n }}" data-nom="{{ $nomEtape }}">
                                @foreach($blocs as [$type, $num, $titre, $groupes])
                                    <div class="bloc">
                                        <h3 class="bloc__titre" id="bloc-{{ $num }}" tabindex="-1">
                                            <span class="bloc__n num" aria-hidden="true">{{ $num }}</span>
                                            <span>{{ $titre }}@if($type === 'documents') <span class="bloc__option">— facultatif</span>@endif</span>
                                        </h3>

                                        @if($type === 'contact')
                                            {{-- ── 1 · Informations de contact ── --}}
                                            <div class="champs">
                                                @foreach($champsContact as [$cle, $libelle, $typeChamp, $max, $exemple, $auto, $mode, $large])
                                                    <div class="champ {{ $large ? 'champ--large' : '' }} @error($cle) est-erreur @enderror">
                                                        <label for="f-{{ $cle }}">{{ $libelle }} <span class="req" aria-hidden="true">*</span></label>
                                                        <input id="f-{{ $cle }}" type="{{ $typeChamp }}" name="{{ $cle }}" value="{{ $ancien($cle) }}"
                                                               required maxlength="{{ $max }}" placeholder="{{ $exemple }}" autocomplete="{{ $auto }}"
                                                               @if($mode) inputmode="{{ $mode }}" @endif
                                                               @if($typeChamp === 'email') autocapitalize="off" spellcheck="false" data-msg-format="{{ __('validation.email') }}" @endif
                                                               data-msg-requis="{{ $msgRequis($cle) }}"
                                                               @error($cle) aria-invalid="true" aria-describedby="err-{{ $cle }}" @enderror>
                                                        @error($cle)<p class="erreur" id="err-{{ $cle }}">{{ $message }}</p>@enderror
                                                    </div>
                                                @endforeach
                                            </div>

                                        @elseif($type === 'choix')
                                            {{-- ── Choix en pastilles : cases (plusieurs réponses) ou boutons radio (une seule) ── --}}
                                            @foreach($groupes as $cle => $question)
                                                @php($radio = in_array($cle, $radios, true))
                                                @php($e = $errChoix($cle))
                                                <fieldset class="groupe {{ $e ? 'est-erreur' : '' }}"
                                                          @if(!$question) aria-labelledby="bloc-{{ $num }}" @endif
                                                          @if($e) aria-describedby="err-{{ $cle }}" @endif>
                                                    @if($question)<legend class="groupe__legende">{{ $question }}</legend>@endif
                                                    <div class="choix-groupe">
                                                        @foreach($opt[$cle] as $val => $label)
                                                            <label class="choix">
                                                                <input type="{{ $radio ? 'radio' : 'checkbox' }}" name="{{ $radio ? $cle : $cle . '[]' }}" value="{{ $val }}"
                                                                       @checked($radio ? old($cle) === $val : in_array($val, (array) old($cle, []), true))>
                                                                <span class="choix__pastille">
                                                                    <span class="choix__case" aria-hidden="true"><svg class="ic"><use href="#i-check"/></svg></span>{{ $label }}
                                                                </span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    @if($e)<p class="erreur" id="err-{{ $cle }}">{{ $e }}</p>@endif
                                                </fieldset>
                                            @endforeach

                                        @elseif($type === 'message')
                                            {{-- ── 5 · Description ── --}}
                                            <div class="champ @error('message') est-erreur @enderror">
                                                <label for="f-message">Décrivez votre projet</label>
                                                <span class="champ__aide" id="aide-message">Quels sont vos objectifs ? Quelles sont vos contraintes ? Y a-t-il une date importante ?</span>
                                                <textarea id="f-message" name="message" maxlength="4000" rows="6"
                                                          placeholder="Expliquez votre besoin, votre cible, votre période de campagne et toute information utile."
                                                          aria-describedby="aide-message @error('message') err-message @enderror"
                                                          @error('message') aria-invalid="true" @enderror>{{ $ancien('message') }}</textarea>
                                                @error('message')<p class="erreur" id="err-message">{{ $message }}</p>@enderror
                                            </div>

                                        @elseif($type === 'documents')
                                            {{-- ── 6 · Documents : rien n'est stocké, ils partent en pièce jointe ── --}}
                                            <p class="bloc__intro" id="aide-docs">Nous faire gagner du temps : joignez ce dont vous disposez déjà. PDF, images, AI, ZIP ou Office — 10 Mo maximum par fichier.</p>
                                            <div class="docs">
                                                @foreach(CibleController::DOCUMENTS as $champ => $label)
                                                    <div class="doc-zone @error($champ) est-erreur @enderror">
                                                        <label class="doc" for="f-{{ $champ }}">
                                                            <span class="doc__nom">{{ $label }}</span>
                                                            <input id="f-{{ $champ }}" type="file" name="{{ $champ }}"
                                                                   accept="{{ '.' . implode(',.', $formats) }}"
                                                                   aria-describedby="aide-docs @error($champ) err-{{ $champ }} @enderror"
                                                                   data-msg-taille="{{ __('validation.max.file', ['attribute' => $nomChamp($champ), 'max' => $maxKo]) }}"
                                                                   data-msg-format="{{ __('validation.extensions', ['attribute' => $nomChamp($champ), 'values' => implode(', ', $formats)]) }}"
                                                                   @error($champ) aria-invalid="true" @enderror>
                                                            {{-- Nom du fichier choisi (contact.js) : le texte natif,
                                                                 tronqué en plein milieu dans une case étroite, est masqué. --}}
                                                            <span class="doc__fichier" aria-hidden="true"></span>
                                                        </label>
                                                        @error($champ)<p class="erreur" id="err-{{ $champ }}">{{ $message }}</p>@enderror
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                @if($n === 4)
                                    {{-- Consentement. Le message est celui de CibleController::submitDevis(). --}}
                                    <div class="consentement @error('consentement') est-erreur @enderror">
                                        <label>
                                            <input type="checkbox" name="consentement" value="1" required @checked(old('consentement'))
                                                   data-msg-requis="Merci d'accepter d'être recontacté pour que nous puissions traiter votre demande."
                                                   @error('consentement') aria-invalid="true" aria-describedby="err-consentement" @enderror>
                                            <span>J'accepte que mes informations soient utilisées pour être recontacté dans le cadre de ma demande.</span>
                                        </label>
                                        @error('consentement')<p class="erreur" id="err-consentement">{{ $message }}</p>@enderror
                                    </div>

                                    {{-- Cloudflare Turnstile : rendu seulement si les clés sont
                                         configurées ; invisible pour un visiteur ordinaire. --}}
                                    @if(Turnstile::actif())
                                        <div class="turnstile">
                                            <div class="cf-turnstile" data-sitekey="{{ Turnstile::siteKey() }}" data-language="fr" data-theme="light"></div>
                                            @error('turnstile')<p class="erreur">{{ $message }}</p>@enderror
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach

                        {{-- Navigation. Sans JavaScript, seul le bouton d'envoi est
                             visible, sous les 4 étapes affichées d'un tenant. --}}
                        <div class="etape-nav">
                            <button type="button" class="btn btn--ligne etape-nav__retour" id="et-prec" hidden>
                                <svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg><span class="etape-nav__txt">Retour</span>
                            </button>
                            <button type="button" class="btn btn--rouge etape-nav__suite" id="et-suiv" hidden>
                                Continuer<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg>
                            </button>
                            <button type="submit" class="btn btn--rouge etape-nav__suite" id="et-envoi">
                                {{ Contenu::get('contact.form_bouton') }}<svg class="ic" aria-hidden="true"><use href="#i-fleche"/></svg>
                            </button>
                            <p class="etape-nav__note" id="et-note">Vos données ne sont utilisées que pour vous répondre. Aucun démarchage tiers.</p>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- ── Colonne d'appui : coordonnées, engagements, arguments ── --}}
        <div class="contact-cote">
            {{-- Coordonnées : même composant que la bande contact des autres pages,
                 complété du secrétariat et de l'adresse. --}}
            <section class="cote-coord rv" aria-labelledby="coord-titre">
                <h2 class="sur sur--noir" id="coord-titre">Contact</h2>
                <x-mq.coordonnees complet />
            </section>

            {{-- Engagements --}}
            <section class="cote-reass rv" aria-labelledby="reass-titre">
                <span class="sur">{{ Contenu::get('contact.reass_surtitre') }}</span>
                <h2 class="riche" id="reass-titre">{!! Contenu::riche('contact.reass_titre') !!}</h2>
                <p>{{ Contenu::get('contact.reass_texte') }}</p>
                <ul class="coches">
                    @foreach(Contenu::lignes('contact.reass_liste') as $r)
                        <li><span class="rond" aria-hidden="true"><svg class="ic"><use href="#i-check"/></svg></span>{{ $r }}</li>
                    @endforeach
                </ul>
            </section>

            {{-- Pourquoi CIBLE --}}
            <section class="cote-pourquoi rv" aria-labelledby="pourquoi-titre">
                <span class="decor decor--plume decor--masque-mobile" aria-hidden="true" style="--c:var(--blanc);--o:.07;width:170px;right:-42px;bottom:-40px;transform:rotate(-18deg)"></span>
                <h2 class="riche" id="pourquoi-titre">{!! Contenu::riche('contact.pourquoi_titre') !!}</h2>
                <ul>
                    @foreach(Contenu::lignes('contact.pourquoi_liste') as $i => $l)
                        <li style="--c:{{ $tons[$i % 5] }}">{{ $l }}</li>
                    @endforeach
                </ul>
            </section>
        </div>

    </div>
</section>

@endsection
