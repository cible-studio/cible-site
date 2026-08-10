<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande — recommandation média</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background: #f7f7f5; padding: 30px; color: #0f172a;">
    <div style="max-width: 640px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <div style="background: #0f172a; color: #fff; padding: 26px 32px; border-bottom: 3px solid #e8a020;">
            <div style="font-size: 12px; letter-spacing: .14em; text-transform: uppercase; color: #e8a020; font-weight: 700;">CIBLE CI · Nouvelle demande de recommandation média</div>
            <div style="font-size: 22px; margin-top: 6px; font-weight: 700;">{{ $d['entreprise'] ?? '—' }}</div>
        </div>

        <div style="padding: 26px 32px;">

            {{-- ─── 1. Contact ─── --}}
            <div style="font-size: 12px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #94a3b8; margin-bottom: 10px;">1 · Contact</div>
            <table style="width:100%; border-collapse: collapse; font-size: 14px;">
                <tr><td style="padding: 8px 0; color: #64748b; width: 150px;">Nom et prénom</td><td style="font-weight: 600;">{{ $d['nom'] ?? '—' }}</td></tr>
                <tr><td style="padding: 8px 0; color: #64748b;">Fonction</td><td>{{ $d['poste'] ?: '—' }}</td></tr>
                <tr><td style="padding: 8px 0; color: #64748b;">Entreprise</td><td style="font-weight: 600;">{{ $d['entreprise'] ?? '—' }}</td></tr>
                <tr><td style="padding: 8px 0; color: #64748b;">Téléphone</td><td><a href="tel:{{ $d['tel'] ?? '' }}" style="color: #e8a020;">{{ $d['tel'] ?? '—' }}</a></td></tr>
                <tr><td style="padding: 8px 0; color: #64748b;">Email</td><td><a href="mailto:{{ $d['email'] ?? '' }}" style="color: #e8a020;">{{ $d['email'] ?? '—' }}</a></td></tr>
            </table>

            {{-- ─── 2. Le projet ─── --}}
            <div style="font-size: 12px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #94a3b8; margin: 26px 0 10px; padding-top: 18px; border-top: 1px dashed #e2e8f0;">2 · Le projet</div>
            <table style="width:100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 8px 0; color: #64748b; width: 150px; vertical-align: top;">Objectifs</td>
                    <td style="font-weight: 600;">{{ !empty($d['objectif']) ? implode(' · ', $d['objectif']) : '—' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; vertical-align: top;">Audiences visées</td>
                    <td>{{ !empty($d['cible']) ? implode(' · ', $d['cible']) : '—' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; vertical-align: top;">Zones</td>
                    <td>{{ !empty($d['zone']) ? implode(' · ', $d['zone']) : '—' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b;">Lancement souhaité</td>
                    <td>{{ $d['periode'] ?: '—' }}</td>
                </tr>
            </table>

            {{-- ─── 3. Services ─── --}}
            <div style="font-size: 12px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #94a3b8; margin: 26px 0 10px; padding-top: 18px; border-top: 1px dashed #e2e8f0;">3 · Services recherchés</div>
            <div style="font-size: 14px; font-weight: 600; line-height: 1.7;">
                {{ !empty($d['services']) ? implode(' · ', $d['services']) : '—' }}
            </div>

            {{-- ─── 4. Budget ─── --}}
            <div style="font-size: 12px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #94a3b8; margin: 26px 0 10px; padding-top: 18px; border-top: 1px dashed #e2e8f0;">4 · Budget envisagé</div>
            <div style="font-size: 16px; font-weight: 700; color: #0f172a;">{{ $d['budget'] ?: '—' }}</div>

            {{-- ─── 5. Description ─── --}}
            @if(!empty($d['message']))
                <div style="margin-top: 26px; padding: 18px; background: #fbf8f1; border-left: 3px solid #e8a020; border-radius: 4px;">
                    <div style="font-size: 12px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #e8a020; margin-bottom: 8px;">5 · Description du projet</div>
                    <div style="font-size: 14.5px; line-height: 1.6; color: #1e293b; white-space: pre-wrap;">{{ $d['message'] }}</div>
                </div>
            @endif

            {{-- ─── 6. Provenance ─── --}}
            @if(!empty($d['provenance']))
                <div style="margin-top: 26px; padding-top: 18px; border-top: 1px dashed #e2e8f0; font-size: 14px;">
                    <span style="color: #64748b;">Nous a connus via</span>
                    <strong style="margin-left: 8px;">{{ $d['provenance'] }}</strong>
                </div>
            @endif

            <div style="margin-top: 28px; padding-top: 18px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
                Les documents éventuellement déposés sont joints à ce message.<br>
                Reçu le {{ $d['received_at'] ?? now()->format('d/m/Y H:i') }}<br>
                IP : {{ $d['ip'] ?? '—' }}
            </div>
        </div>
    </div>
</body>
</html>
