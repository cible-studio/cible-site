@extends('_layout', [
    'seo_title'       => 'Contact — CIBLE · Recevoir une recommandation média',
    'seo_description' => 'Décrivez votre projet : objectifs, audiences, zones, services et budget. Notre équipe vous répond sous 24 heures ouvrées avec une recommandation média personnalisée.',
])

@push('page-css')
    .contact-wrap{padding:clamp(60px,8vw,110px) var(--pad);background:var(--jaune);color:var(--noir);position:relative;overflow:hidden}
    .motif{position:absolute;inset:-10% -10%;background-image:var(--tuile);background-size:340px 340px;animation:motifDerive 60s linear infinite;pointer-events:none;z-index:0;opacity:.5}
    @keyframes motifDerive{from{background-position:0 0}to{background-position:340px 340px}}
    .contact-inner{position:relative;z-index:2;display:grid;grid-template-columns:1fr 1.25fr;gap:clamp(30px,5vw,70px);align-items:start}
    @media(max-width:980px){.contact-inner{grid-template-columns:1fr}}
    .contact-inner .intro .t1{max-width:18ch}
    .contact-inner .intro p{margin-top:20px;max-width:46ch;font-size:clamp(16px,1.6vw,19px)}
    .contact-inner .intro .accroche{margin-top:14px;font-size:15.5px;font-weight:700;font-family:var(--titre)}

    /* Bénéfices — ce que vous obtenez */
    .benefs{margin-top:32px;background:rgba(255,255,255,.55);border-radius:18px;padding:24px 26px}
    .benefs h2{font-family:var(--titre);font-weight:800;font-size:13px;text-transform:uppercase;letter-spacing:.1em;color:#7A5B00;margin-bottom:14px}
    .benefs ul{list-style:none;display:flex;flex-direction:column;gap:10px}
    .benefs li{display:flex;gap:12px;align-items:flex-start;font-size:14.5px;line-height:1.5;font-weight:600}
    .benefs li::before{content:"✓";color:var(--rouge);font-weight:900;flex-shrink:0}

    /* Carte coordonnées 2026-08-04 — refonte propre :
       lignes avec icône colorée + label uppercase + valeur. */
    .coord{
        margin-top:26px;background:var(--blanc);padding:8px;
        border-radius:20px;font-family:var(--titre);
        display:flex;flex-direction:column;
        box-shadow:0 12px 32px -12px rgba(0,0,0,.15);
    }
    .coord-row{
        display:flex;align-items:center;gap:16px;
        padding:16px 18px;border-radius:14px;
        text-decoration:none;color:var(--noir);
        transition:background .18s;
    }
    .coord-row + .coord-row{border-top:1px solid #F0F0F0}
    .coord-row:hover{background:#FAFAFA}
    .coord-icon{
        width:44px;height:44px;border-radius:12px;
        display:flex;align-items:center;justify-content:center;
        flex-shrink:0;font-size:18px;
    }
    .coord-icon-tel{background:rgba(226,6,19,.10);color:var(--rouge)}
    .coord-icon-mail{background:rgba(58,168,53,.10);color:var(--vert)}
    .coord-icon-addr{background:rgba(63,127,192,.10);color:var(--bleu)}
    .coord-txt{display:flex;flex-direction:column;gap:2px;min-width:0;flex:1}
    .coord-lbl{
        font-family:var(--titre);font-weight:700;
        font-size:10px;text-transform:uppercase;
        letter-spacing:.12em;color:#888;
    }
    .coord-val{
        font-family:var(--titre);font-weight:800;
        font-size:16px;color:var(--noir);line-height:1.3;
        overflow-wrap:anywhere;
    }
    .coord-row.coord-tel .coord-val{color:var(--rouge);font-size:18px}
    .coord-val-sub{
        font-family:var(--corps);font-weight:600;
        font-size:13.5px;color:#555;line-height:1.5;margin-top:2px;
    }

    /* ═══ formulaire mini-brief ═══ */
    .form-card{background:#fff;border-radius:24px;padding:clamp(26px,3.5vw,40px);box-shadow:0 20px 50px -20px rgba(0,0,0,.15)}
    .form-card h2{font-family:var(--titre);font-weight:800;font-size:24px;margin-bottom:8px}
    .form-card .sub{font-size:14px;color:#666;margin-bottom:8px}

    .bloc{border-top:1px solid var(--gris);margin-top:28px;padding-top:24px}
    .bloc:first-of-type{border-top:0;margin-top:20px;padding-top:0}
    .bloc-titre{
        font-family:var(--titre);font-weight:800;font-size:13px;
        text-transform:uppercase;letter-spacing:.1em;color:var(--rouge);
        display:flex;align-items:center;gap:10px;margin-bottom:18px;
    }
    .bloc-titre b{
        width:24px;height:24px;border-radius:50%;background:var(--rouge);color:#fff;
        display:inline-flex;align-items:center;justify-content:center;
        font-size:12px;font-weight:900;flex-shrink:0;
    }

    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    @media(max-width:600px){.form-row{grid-template-columns:1fr}}
    .form-field{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
    .form-field label{font-family:var(--titre);font-weight:700;font-size:12.5px;text-transform:uppercase;letter-spacing:.06em;color:#333}
    .form-field label .req{color:var(--rouge)}
    .form-field input[type=text],.form-field input[type=email],.form-field input[type=tel],.form-field select,.form-field textarea{
        border:1.5px solid var(--gris);border-radius:10px;padding:12px 14px;
        font-family:var(--corps);font-size:15px;background:#fff;color:var(--noir);
        transition:border-color .2s,box-shadow .2s
    }
    .form-field input:focus,.form-field select:focus,.form-field textarea:focus{
        outline:none;border-color:var(--rouge);box-shadow:0 0 0 3px rgba(226,6,19,.12)
    }
    .form-field textarea{min-height:130px;resize:vertical}
    .form-field.error input,.form-field.error select,.form-field.error textarea{border-color:var(--rouge);background:#fef2f2}
    /* Règle de base : les messages d'erreur apparaissent aussi dans les
       fieldsets de choix et les blocs de dépôt, hors de .form-field. */
    .err-msg{color:var(--rouge);font-size:13px;font-family:var(--titre);font-weight:600}
    .form-field .aide{font-size:12.5px;color:#888;font-family:var(--corps);font-weight:400;text-transform:none;letter-spacing:0}

    /* Groupes de cases / radios en pastilles cliquables */
    .choix-groupe{display:flex;flex-wrap:wrap;gap:8px}
    .choix{position:relative}
    .choix input{position:absolute;opacity:0;width:0;height:0}
    .choix span{
        display:inline-block;cursor:pointer;
        font-family:var(--titre);font-weight:600;font-size:13.5px;
        padding:10px 16px;border-radius:999px;
        border:1.5px solid var(--gris);background:#fff;color:#555;
        transition:border-color .18s,background .18s,color .18s;
    }
    .choix span:hover{border-color:#BBB}
    .choix input:checked + span{background:var(--noir);border-color:var(--noir);color:#fff}
    .choix input:focus-visible + span{outline:3px solid var(--rouge);outline-offset:2px}
    .choix-legende{font-family:var(--titre);font-weight:700;font-size:12.5px;text-transform:uppercase;letter-spacing:.06em;color:#333;margin-bottom:10px;display:block}
    fieldset{border:0;padding:0;margin:0 0 22px}
    fieldset:last-of-type{margin-bottom:0}

    /* Dépôt de documents */
    .docs-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(max-width:600px){.docs-grid{grid-template-columns:1fr}}
    .doc-field{border:1.5px dashed var(--gris);border-radius:12px;padding:14px 16px;transition:border-color .2s}
    .doc-field:hover{border-color:#BBB}
    .doc-field.error{border-color:var(--rouge);background:#fef2f2}
    .doc-field label{display:block;font-family:var(--titre);font-weight:700;font-size:12.5px;color:#333;margin-bottom:8px}
    .doc-field input[type=file]{width:100%;font-family:var(--corps);font-size:12.5px;color:#666}
    .doc-field input[type=file]::file-selector-button{
        font-family:var(--titre);font-weight:700;font-size:12px;
        border:0;border-radius:8px;padding:8px 12px;margin-right:10px;
        background:var(--gris);color:#333;cursor:pointer;
    }
    .doc-field input[type=file]::file-selector-button:hover{background:#D8D8D8}
    .doc-field .err-msg{display:block;color:var(--rouge);font-size:12.5px;font-family:var(--titre);font-weight:600;margin-top:6px}

    /* Ce que vous recevrez */
    .recevrez{background:var(--gris);border-radius:16px;padding:24px 26px;margin-top:28px}
    .recevrez h3{font-family:var(--titre);font-weight:800;font-size:15px;margin-bottom:14px}
    .recevrez ul{list-style:none;display:flex;flex-direction:column;gap:9px}
    .recevrez li{display:flex;gap:10px;align-items:flex-start;font-size:14px;line-height:1.5;color:#333}
    .recevrez li::before{content:"✓";color:var(--vert);font-weight:900;flex-shrink:0}

    /* Consentement + envoi */
    .consent{display:flex;gap:12px;align-items:flex-start;margin-top:24px;font-size:13.5px;line-height:1.55;color:#444}
    .consent input{margin-top:3px;width:18px;height:18px;accent-color:var(--rouge);flex-shrink:0}
    .consent.error{color:var(--rouge)}
    .form-submit{margin-top:22px;display:flex;gap:12px;align-items:center;flex-wrap:wrap}
    .form-submit p{font-size:12.5px;color:#666;flex:1;min-width:200px}

    .success-msg{background:#dcfce7;border:2px solid #22c55e;color:#166534;padding:28px;border-radius:16px;text-align:center;font-family:var(--titre)}
    .success-msg h3{font-size:24px;margin-bottom:10px;color:#15803d}
    .success-msg p{font-weight:600;font-family:var(--corps);line-height:1.6}

    .error-msg{background:#fee2e2;border:2px solid #ef4444;color:#991b1b;padding:20px;border-radius:12px;font-family:var(--titre);font-weight:600;margin-bottom:20px}

    /* ═══ réassurance ═══ */
    .reass{padding:clamp(56px,8vw,100px) var(--pad)}
    .reass-wrap{display:grid;grid-template-columns:1fr 1fr;gap:clamp(30px,5vw,70px);align-items:center;max-width:1200px;margin:0 auto}
    @media(max-width:860px){.reass-wrap{grid-template-columns:1fr}}
    .reass .sur{color:var(--vert)}
    .reass .t1{margin-top:12px}
    .reass p.body{margin-top:20px;color:#444;line-height:1.7}
    .reass-list{list-style:none;display:flex;flex-direction:column;gap:12px}
    .reass-list li{
        display:flex;align-items:flex-start;gap:14px;
        background:var(--gris);border-radius:12px;padding:18px 20px;
        font-family:var(--titre);font-weight:700;font-size:15px;color:#222;
    }
    .reass-list li i{width:10px;height:10px;border-radius:50%;background:var(--c);flex-shrink:0;margin-top:6px}

    /* ═══ pourquoi CIBLE ═══ */
    .pourquoi{padding:clamp(56px,8vw,100px) var(--pad);background:var(--noir);color:#fff}
    .pourquoi .entete{max-width:860px;margin:0 auto 44px;text-align:center}
    .pourquoi .t1{color:#fff}
    .pourquoi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;max-width:1100px;margin:0 auto}
    @media(max-width:900px){.pourquoi-grid{grid-template-columns:1fr 1fr}}
    @media(max-width:600px){.pourquoi-grid{grid-template-columns:1fr}}
    .pq-item{
        display:flex;align-items:flex-start;gap:12px;
        background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);
        border-radius:12px;padding:18px 20px;
        font-size:14.5px;line-height:1.5;color:rgba(255,255,255,.88);
    }
    .pq-item i{width:9px;height:9px;border-radius:50%;background:var(--c);flex-shrink:0;margin-top:7px}
@endpush

@section('content')

@php
    $opt = \App\Http\Controllers\CibleController::formOptions();

    // Les cases/boutons radio sont validés soit sur le champ lui-même
    // (`periode`), soit sur chaque élément du tableau (`objectif.0`…).
    // Ce helper récupère l'erreur dans les deux cas — sans lui, un envoi
    // forgé afficherait le bandeau global sans rien signaler au champ.
    $errChoix = fn (string $cle) => $errors->first($cle) ?: $errors->first($cle . '.*');
@endphp

<section class="contact-wrap">
    <div class="motif" aria-hidden="true"></div>
    <div class="contact-inner">

        {{-- Colonne gauche : intro + bénéfices + coordonnées --}}
        <div class="intro rev">
            <span class="sur">Parlons de votre projet</span>
            <h1 class="t1" style="margin-top:14px">Donnons à votre marque la visibilité qu'elle mérite.</h1>
            <p>Que vous souhaitiez lancer une campagne d'affichage, créer une expérience de marque, renforcer votre présence digitale ou construire une stratégie de communication complète, nos équipes vous accompagnent dans la conception du dispositif le plus adapté à vos objectifs.</p>
            <p class="accroche">Nous analysons votre besoin avant de vous proposer une recommandation média personnalisée.</p>

            <div class="benefs">
                <h2>Ce que vous obtenez</h2>
                <ul>
                    <li>Une recommandation adaptée à votre objectif de communication</li>
                    <li>Une sélection des formats et des emplacements les plus pertinents</li>
                    <li>Une proposition adaptée à votre budget</li>
                    <li>Un interlocuteur dédié pour vous accompagner</li>
                    <li>Une réponse sous 24 heures ouvrées</li>
                </ul>
            </div>

            <div class="coord">
                <a href="tel:+2250700780628" class="coord-row coord-tel">
                    <span class="coord-icon coord-icon-tel">📞</span>
                    <span class="coord-txt">
                        <span class="coord-lbl">Téléphone</span>
                        <span class="coord-val num">+225 07 00 78 06 28</span>
                    </span>
                </a>
                <a href="mailto:commercial@cible-ci.com" class="coord-row">
                    <span class="coord-icon coord-icon-mail">✉</span>
                    <span class="coord-txt">
                        <span class="coord-lbl">Email</span>
                        <span class="coord-val">commercial@cible-ci.com</span>
                    </span>
                </a>
                <div class="coord-row" style="cursor:default">
                    <span class="coord-icon coord-icon-addr">📍</span>
                    <span class="coord-txt">
                        <span class="coord-lbl">Adresse</span>
                        <span class="coord-val">Rue des Ambassadeurs</span>
                        <span class="coord-val-sub">Riviera M'Badon · 10 BP 1029 Abidjan 10</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Colonne droite : le mini-brief --}}
        <div class="form-card rev">
            @if(session('devis_sent'))
                <div class="success-msg">
                    <h3>✓ Votre demande a bien été reçue !</h3>
                    <p>Merci pour votre confiance. Un membre de notre équipe analysera votre projet et vous contactera dans les 24 heures ouvrées afin d'échanger sur vos objectifs et de vous proposer la solution la plus adaptée.</p>
                </div>
            @else
                <h2>Recevoir une recommandation média</h2>
                <p class="sub">Décrivez votre projet. Nos équipes reviendront vers vous avec une proposition personnalisée et les solutions les plus adaptées à vos objectifs.</p>

                @if(session('devis_error'))
                    <div class="error-msg">⚠ {{ session('devis_error') }}</div>
                @endif
                @if($errors->any())
                    <div class="error-msg">⚠ Merci de vérifier les champs indiqués avant de valider votre demande.</div>
                @endif

                {{-- enctype obligatoire : le bloc 6 accepte des documents. --}}
                <form method="POST" action="{{ route('devis.submit') }}" enctype="multipart/form-data" novalidate>
                    @csrf

                    {{-- Honeypot anti-bot --}}
                    <div class="honeypot" aria-hidden="true" style="position:absolute;left:-9999px;visibility:hidden">
                        <label>Ne pas remplir <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    {{-- ─────── 1 · Informations de contact ─────── --}}
                    <div class="bloc">
                        <div class="bloc-titre"><b>1</b> Informations sur le contact</div>

                        <div class="form-row">
                            <div class="form-field @error('nom') error @enderror">
                                <label for="f-nom">Nom et prénom <span class="req">*</span></label>
                                <input id="f-nom" type="text" name="nom" value="{{ old('nom') }}" required maxlength="100" placeholder="Ex. Jean Dupont" autocomplete="name">
                                @error('nom') <span class="err-msg">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-field @error('entreprise') error @enderror">
                                <label for="f-ent">Entreprise <span class="req">*</span></label>
                                <input id="f-ent" type="text" name="entreprise" value="{{ old('entreprise') }}" required maxlength="150" placeholder="Nom de votre entreprise" autocomplete="organization">
                                @error('entreprise') <span class="err-msg">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-field @error('poste') error @enderror">
                            <label for="f-poste">Fonction</label>
                            <input id="f-poste" type="text" name="poste" value="{{ old('poste') }}" maxlength="100" placeholder="Ex. Directrice Marketing" autocomplete="organization-title">
                            @error('poste') <span class="err-msg">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-field @error('email') error @enderror">
                                <label for="f-email">Adresse email <span class="req">*</span></label>
                                <input id="f-email" type="email" name="email" value="{{ old('email') }}" required maxlength="150" placeholder="nom@entreprise.ci" autocomplete="email">
                                @error('email') <span class="err-msg">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-field @error('tel') error @enderror">
                                <label for="f-tel">Téléphone <span class="req">*</span></label>
                                <input id="f-tel" type="tel" name="tel" value="{{ old('tel') }}" required maxlength="30" placeholder="+225 …" autocomplete="tel">
                                @error('tel') <span class="err-msg">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ─────── 2 · Le projet ─────── --}}
                    <div class="bloc">
                        <div class="bloc-titre"><b>2</b> Parlons de votre projet</div>

                        <fieldset>
                            <legend class="choix-legende">Quel est votre objectif principal&nbsp;?</legend>
                            <div class="choix-groupe">
                                @foreach($opt['objectif'] as $val => $label)
                                    <label class="choix">
                                        <input type="checkbox" name="objectif[]" value="{{ $val }}" @checked(in_array($val, old('objectif', []), true))>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @if($e = $errChoix('objectif'))<span class="err-msg" style="display:block;margin-top:10px">{{ $e }}</span>@endif
                        </fieldset>

                        <fieldset>
                            <legend class="choix-legende">Qui souhaitez-vous toucher&nbsp;?</legend>
                            <div class="choix-groupe">
                                @foreach($opt['cible'] as $val => $label)
                                    <label class="choix">
                                        <input type="checkbox" name="cible[]" value="{{ $val }}" @checked(in_array($val, old('cible', []), true))>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @if($e = $errChoix('cible'))<span class="err-msg" style="display:block;margin-top:10px">{{ $e }}</span>@endif
                        </fieldset>

                        <fieldset>
                            <legend class="choix-legende">Où souhaitez-vous communiquer&nbsp;?</legend>
                            <div class="choix-groupe">
                                @foreach($opt['zone'] as $val => $label)
                                    <label class="choix">
                                        <input type="checkbox" name="zone[]" value="{{ $val }}" @checked(in_array($val, old('zone', []), true))>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @if($e = $errChoix('zone'))<span class="err-msg" style="display:block;margin-top:10px">{{ $e }}</span>@endif
                        </fieldset>

                        <fieldset>
                            <legend class="choix-legende">Quand souhaitez-vous lancer votre campagne&nbsp;?</legend>
                            <div class="choix-groupe">
                                @foreach($opt['periode'] as $val => $label)
                                    <label class="choix">
                                        <input type="radio" name="periode" value="{{ $val }}" @checked(old('periode') === $val)>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @if($e = $errChoix('periode'))<span class="err-msg" style="display:block;margin-top:10px">{{ $e }}</span>@endif
                        </fieldset>
                    </div>

                    {{-- ─────── 3 · Services ─────── --}}
                    <div class="bloc">
                        <div class="bloc-titre"><b>3</b> Les services recherchés</div>
                        <fieldset>
                            <legend class="choix-legende">Quels services vous intéressent&nbsp;?</legend>
                            <div class="choix-groupe">
                                @foreach($opt['services'] as $val => $label)
                                    <label class="choix">
                                        <input type="checkbox" name="services[]" value="{{ $val }}" @checked(in_array($val, old('services', []), true))>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @if($e = $errChoix('services'))<span class="err-msg" style="display:block;margin-top:10px">{{ $e }}</span>@endif
                        </fieldset>
                    </div>

                    {{-- ─────── 4 · Budget ─────── --}}
                    <div class="bloc">
                        <div class="bloc-titre"><b>4</b> Budget</div>
                        <fieldset>
                            <legend class="choix-legende">Quel budget envisagez-vous&nbsp;?</legend>
                            <div class="choix-groupe">
                                @foreach($opt['budget'] as $val => $label)
                                    <label class="choix">
                                        <input type="radio" name="budget" value="{{ $val }}" @checked(old('budget') === $val)>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @if($e = $errChoix('budget'))<span class="err-msg" style="display:block;margin-top:10px">{{ $e }}</span>@endif
                        </fieldset>
                    </div>

                    {{-- ─────── 5 · Description ─────── --}}
                    <div class="bloc">
                        <div class="bloc-titre"><b>5</b> Description</div>
                        <div class="form-field @error('message') error @enderror">
                            <label for="f-message">Décrivez votre projet</label>
                            <textarea id="f-message" name="message" maxlength="4000" placeholder="Expliquez votre besoin, votre cible, votre période de campagne et toute information utile.">{{ old('message') }}</textarea>
                            <span class="aide">Quels sont vos objectifs ? Quelles sont vos contraintes ? Y a-t-il une date importante ?</span>
                            @error('message') <span class="err-msg">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- ─────── 6 · Documents ─────── --}}
                    <div class="bloc">
                        <div class="bloc-titre"><b>6</b> Vos documents <span style="color:#999;font-weight:600;letter-spacing:0;text-transform:none">— facultatif</span></div>
                        <p style="font-size:13.5px;color:#666;margin-bottom:16px">Nous faire gagner du temps : joignez ce dont vous disposez déjà. PDF, images, AI, ZIP ou Office — 10 Mo maximum par fichier.</p>
                        <div class="docs-grid">
                            @foreach(\App\Http\Controllers\CibleController::DOCUMENTS as $champ => $label)
                                <div class="doc-field @error($champ) error @enderror">
                                    <label for="f-{{ $champ }}">{{ $label }}</label>
                                    <input id="f-{{ $champ }}" type="file" name="{{ $champ }}"
                                           accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.ai,.eps,.svg,.zip,.doc,.docx,.ppt,.pptx">
                                    @error($champ) <span class="err-msg">{{ $message }}</span> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ─────── 7 · Provenance ─────── --}}
                    <div class="bloc">
                        <div class="bloc-titre"><b>7</b> Comment nous avez-vous connus&nbsp;?</div>
                        <fieldset>
                            <div class="choix-groupe">
                                @foreach($opt['provenance'] as $val => $label)
                                    <label class="choix">
                                        <input type="radio" name="provenance" value="{{ $val }}" @checked(old('provenance') === $val)>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @if($e = $errChoix('provenance'))<span class="err-msg" style="display:block;margin-top:10px">{{ $e }}</span>@endif
                        </fieldset>
                    </div>

                    {{-- ─────── Ce que vous recevrez ─────── --}}
                    <div class="recevrez">
                        <h3>Ce que vous recevrez</h3>
                        <ul>
                            <li>Une recommandation média adaptée à vos objectifs</li>
                            <li>Une sélection des meilleurs emplacements</li>
                            <li>Une stratégie de visibilité personnalisée</li>
                            <li>Des propositions d'expériences de marque adaptées à vos audiences</li>
                            <li>Un calendrier de déploiement</li>
                            <li>Une estimation budgétaire</li>
                            <li>Des recommandations pour maximiser l'impact de votre campagne</li>
                        </ul>
                    </div>

                    <label class="consent @error('consentement') error @enderror">
                        <input type="checkbox" name="consentement" value="1" @checked(old('consentement'))>
                        <span>
                            J'accepte que mes informations soient utilisées pour être recontacté dans le cadre de ma demande.
                            @error('consentement') <span class="err-msg" style="display:block">{{ $message }}</span> @enderror
                        </span>
                    </label>

                    <div class="form-submit">
                        <button type="submit" class="bouton b-rouge">Recevoir ma recommandation média</button>
                        <p>Vos données ne sont utilisées que pour vous répondre. Aucun démarchage tiers.</p>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>

{{-- ═══ RÉASSURANCE ═══ --}}
<section class="reass a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:var(--vert);--op:.11;top:6%;right:5%;width:120px;--r:18deg;--dur:27s'],
        ['f-fleche', '--c:var(--jaune);--op:.14;bottom:8%;left:3%;width:110px;--r:-14deg;--dur:23s;--del:.5s'],
    ]" />
    <div class="reass-wrap">
        <div class="rev">
            <span class="sur">Notre engagement</span>
            <h2 class="t1">Chaque demande est étudiée avec attention.</h2>
            <p class="body">Nous ne transmettons pas de devis standardisés. Nous analysons vos objectifs, vos audiences, votre zone d'intervention et votre budget afin de vous proposer une stratégie de visibilité réellement adaptée.</p>
        </div>
        <ul class="reass-list rev">
            @foreach([
                ['Réponse sous 24 heures ouvrées',                            'var(--rouge)'],
                ['Aucun engagement avant validation de votre projet',         'var(--jaune)'],
                ['Échanges confidentiels',                                    'var(--vert)'],
                ["Un interlocuteur unique jusqu'au lancement de votre campagne", 'var(--bleu)'],
            ] as [$label, $c])
                <li style="--c:{{ $c }}"><i></i>{{ $label }}</li>
            @endforeach
        </ul>
    </div>
</section>

{{-- ═══ POURQUOI CIBLE ═══ --}}
<section class="pourquoi a-decor">
    <x-decor :formes="[
        ['f-plume',  '--c:#fff;--op:.06;top:6%;left:5%;width:145px;--r:20deg;--dur:32s'],
        ['f-plume',  '--c:#fff;--op:.05;bottom:4%;right:6%;width:115px;--r:-18deg;--dur:36s;--del:.5s'],
        ['f-fleche', '--c:var(--jaune);--op:.14;top:30%;right:11%;width:105px;--r:6deg;--dur:25s;--del:.8s'],
    ]" />
    <div class="entete rev">
        <h2 class="t1">Pourquoi les annonceurs choisissent CIBLE&nbsp;?</h2>
    </div>
    <div class="pourquoi-grid rev">
        @foreach([
            ['+400 panneaux répartis sur le territoire ivoirien',                                    'var(--rouge)'],
            ['31 communes et villes couvertes',                                                      'var(--jaune)'],
            ["Plus de 30 ans d'expertise",                                                            'var(--vert)'],
            ['Une offre intégrée : affichage, digital, street marketing, audiovisuel et brand experience', 'var(--bleu)'],
            ['Un pilotage des campagnes par la Media Intelligence',                                   'var(--violet)'],
            ['Des preuves terrain avec photos horodatées et géolocalisées',                           'var(--rouge)'],
            ['Un interlocuteur unique de la stratégie au reporting',                                  'var(--jaune)'],
        ] as [$label, $c])
            <div class="pq-item" style="--c:{{ $c }}"><i></i>{{ $label }}</div>
        @endforeach
    </div>
</section>

@endsection
