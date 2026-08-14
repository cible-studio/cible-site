{{-- ═══════════════════════════════════════════════════════════════════
     Mail de demande reçue via /contact

     Contraintes propres à l'email, différentes du site :
     - mise en page en <table> et non en flex/grid — Outlook s'appuie sur
       un moteur Word qui ignore les dispositions modernes ;
     - styles en ligne uniquement, aucune feuille externe ni classe ;
     - pas de webfont fiable : Poppins/Nunito sont déclarées en tête de
       pile et retombent sur Trebuchet/Arial, présentes partout ;
     - le logo est une URL absolue, en version FONCÉE (logol.png) puisque
       le fond est clair. Les clients bloquent les images par défaut, d'où
       le bandeau de 5 couleurs : le mail reste identifiable CIBLE même
       sans image affichée.
════════════════════════════════════════════════════════════════════ --}}
@php
    // Palette officielle — cf. CLAUDE.md. Ne pas improviser de teinte ici.
    $rouge = '#E20613'; $jaune = '#FAB80B'; $vert = '#3AA835';
    $bleu  = '#3F7FC0'; $violet = '#81358A';
    $noir  = '#111111'; $gris = '#E6E6E6';
    $titre = "'Poppins','Trebuchet MS',Arial,Helvetica,sans-serif";
    $corps = "'Nunito','Segoe UI',Arial,Helvetica,sans-serif";
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Nouvelle demande — {{ $d['entreprise'] ?? 'CIBLE' }}</title>
</head>
<body style="margin:0;padding:0;background:#F4F4F2;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F4F4F2;padding:28px 12px;">
<tr><td align="center">

    <table role="presentation" width="640" cellpadding="0" cellspacing="0" border="0" style="width:640px;max-width:100%;background:#FFFFFF;border-radius:14px;overflow:hidden;border:1px solid {{ $gris }};">

        {{-- Bandeau 5 couleurs : signature de marque qui reste visible même
             quand le client de messagerie bloque les images. --}}
        <tr>
            <td style="font-size:0;line-height:0;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="20%" height="6" bgcolor="{{ $rouge }}"  style="background:{{ $rouge }};font-size:0;line-height:0;">&nbsp;</td>
                        <td width="20%" height="6" bgcolor="{{ $jaune }}"  style="background:{{ $jaune }};font-size:0;line-height:0;">&nbsp;</td>
                        <td width="20%" height="6" bgcolor="{{ $vert }}"   style="background:{{ $vert }};font-size:0;line-height:0;">&nbsp;</td>
                        <td width="20%" height="6" bgcolor="{{ $bleu }}"   style="background:{{ $bleu }};font-size:0;line-height:0;">&nbsp;</td>
                        <td width="20%" height="6" bgcolor="{{ $violet }}" style="background:{{ $violet }};font-size:0;line-height:0;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- En-tête clair --}}
        <tr>
            <td style="padding:30px 34px 24px;">
                <img src="{{ asset('images/logol.png') }}" width="140" height="75" alt="CIBLE — Vous visez juste"
                     style="display:block;border:0;outline:none;width:140px;height:auto;margin-bottom:22px;">
                <div style="font-family:{{ $titre }};font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:{{ $rouge }};">
                    Nouvelle demande de recommandation média
                </div>
                <div style="font-family:{{ $titre }};font-size:26px;font-weight:800;color:{{ $noir }};line-height:1.2;margin-top:8px;">
                    {{ $d['entreprise'] ?? '—' }}
                </div>
                <div style="font-family:{{ $corps }};font-size:14.5px;color:#666666;margin-top:6px;">
                    {{ $d['nom'] ?? '—' }}@if(!empty($d['poste'])) · {{ $d['poste'] }}@endif
                </div>
                <div style="font-family:{{ $corps }};font-size:13px;color:#888888;margin-top:14px;padding-top:14px;border-top:1px solid {{ $gris }};">
                    Répondre à ce message écrit directement au demandeur.
                </div>
            </td>
        </tr>

        <tr>
            <td style="padding:6px 34px 10px;">

                @php
                    // Un seul rendu pour toutes les sections : titre rouge en
                    // capitales souligné d'un filet, puis lignes libellé/valeur.
                    $sectionTitre = function (string $t) use ($rouge, $titre) {
                        return '<div style="font-family:'.$titre.';font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:'.$rouge.';padding-bottom:8px;border-bottom:2px solid '.$rouge.';margin-bottom:16px;">'.$t.'</div>';
                    };
                @endphp

                {{-- ─── 1 · Contact ─── --}}
                {!! $sectionTitre('1 · Contact') !!}
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-family:{{ $corps }};font-size:14px;">
                    @foreach([
                        ['Nom et prénom', $d['nom'] ?? '—', true],
                        ['Fonction',      $d['poste'] ?: '—', false],
                        ['Entreprise',    $d['entreprise'] ?? '—', true],
                    ] as [$lbl, $val, $fort])
                        <tr>
                            <td width="150" style="padding:7px 0;color:#777777;vertical-align:top;">{{ $lbl }}</td>
                            <td style="padding:7px 0;color:{{ $noir }};{{ $fort ? 'font-weight:700;' : '' }}">{{ $val }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td width="150" style="padding:7px 0;color:#777777;">Téléphone</td>
                        <td style="padding:7px 0;"><a href="tel:{{ $d['tel'] ?? '' }}" style="color:{{ $rouge }};font-weight:700;text-decoration:none;">{{ $d['tel'] ?? '—' }}</a></td>
                    </tr>
                    <tr>
                        <td width="150" style="padding:7px 0;color:#777777;">Email</td>
                        <td style="padding:7px 0;"><a href="mailto:{{ $d['email'] ?? '' }}" style="color:{{ $rouge }};font-weight:700;text-decoration:none;">{{ $d['email'] ?? '—' }}</a></td>
                    </tr>
                </table>

                {{-- ─── 2 · Le projet ─── --}}
                <div style="height:30px;line-height:30px;font-size:0;">&nbsp;</div>
                {!! $sectionTitre('2 · Le projet') !!}
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-family:{{ $corps }};font-size:14px;">
                    @foreach([
                        ['Objectifs',        !empty($d['objectif']) ? implode(' · ', $d['objectif']) : '—'],
                        ['Audiences visées', !empty($d['cible'])    ? implode(' · ', $d['cible'])    : '—'],
                        ['Zones',            !empty($d['zone'])     ? implode(' · ', $d['zone'])     : '—'],
                        ['Lancement',        $d['periode'] ?: '—'],
                    ] as [$lbl, $val])
                        <tr>
                            <td width="150" style="padding:7px 0;color:#777777;vertical-align:top;">{{ $lbl }}</td>
                            <td style="padding:7px 0;color:{{ $noir }};font-weight:600;line-height:1.5;">{{ $val }}</td>
                        </tr>
                    @endforeach
                </table>

                {{-- ─── 3 · Services ─── --}}
                <div style="height:30px;line-height:30px;font-size:0;">&nbsp;</div>
                {!! $sectionTitre('3 · Services recherchés') !!}
                <div style="font-family:{{ $corps }};font-size:14px;font-weight:600;color:{{ $noir }};line-height:1.7;">
                    {{ !empty($d['services']) ? implode(' · ', $d['services']) : '—' }}
                </div>

                {{-- ─── 4 · Budget ─── --}}
                <div style="height:30px;line-height:30px;font-size:0;">&nbsp;</div>
                {!! $sectionTitre('4 · Budget envisagé') !!}
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td bgcolor="#FAFAF8" style="background:#FAFAF8;border:1px solid {{ $gris }};border-radius:9px;padding:13px 20px;font-family:{{ $titre }};font-size:18px;font-weight:800;color:{{ $noir }};">
                            {{ $d['budget'] ?: 'Non précisé' }}
                        </td>
                    </tr>
                </table>

                {{-- ─── 5 · Description ─── --}}
                @if(!empty($d['message']))
                    <div style="height:30px;line-height:30px;font-size:0;">&nbsp;</div>
                    {!! $sectionTitre('5 · Description du projet') !!}
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="border-left:4px solid {{ $jaune }};padding:4px 0 4px 18px;font-family:{{ $corps }};font-size:14.5px;line-height:1.65;color:#333333;white-space:pre-wrap;">{{ $d['message'] }}</td>
                        </tr>
                    </table>
                @endif

                {{-- ─── 6 · Provenance ─── --}}
                @if(!empty($d['provenance']))
                    <div style="height:30px;line-height:30px;font-size:0;">&nbsp;</div>
                    {!! $sectionTitre('6 · Nous a connus via') !!}
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="border:2px solid {{ $bleu }};border-radius:999px;padding:8px 18px;font-family:{{ $titre }};font-size:13px;font-weight:700;color:{{ $bleu }};">
                                {{ $d['provenance'] }}
                            </td>
                        </tr>
                    </table>
                @endif

                <div style="height:30px;line-height:30px;font-size:0;">&nbsp;</div>
            </td>
        </tr>

        {{-- Pied clair --}}
        <tr>
            <td bgcolor="#FAFAF8" style="background:#FAFAF8;padding:20px 34px 24px;border-top:1px solid {{ $gris }};font-family:{{ $corps }};font-size:12px;color:#999999;line-height:1.7;">
                Les documents éventuellement déposés sont joints à ce message.<br>
                Reçu le {{ $d['received_at'] ?? now()->format('d/m/Y H:i') }} · IP {{ $d['ip'] ?? '—' }}<br>
                <span style="color:#BBBBBB;">CIBLE · Régie publicitaire &amp; studio créatif · Côte d'Ivoire —
                <a href="{{ url('/') }}" style="color:#999999;text-decoration:none;font-weight:700;">www.cible-ci.com</a></span>
            </td>
        </tr>

    </table>

    <div style="font-family:{{ $corps }};font-size:11px;color:#AAAAAA;margin-top:16px;">
        Message automatique envoyé depuis le formulaire du site.
    </div>

</td></tr>
</table>

</body>
</html>
