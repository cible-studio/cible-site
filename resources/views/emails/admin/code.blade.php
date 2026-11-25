{{-- Code de connexion admin — styles en ligne (les messageries ignorent les feuilles de style). --}}
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:0;background:#F4F4F2;font-family:Arial,Helvetica,sans-serif;color:#111111">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F4F2;padding:32px 12px">
    <tr><td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#FFFFFF;border-radius:16px;overflow:hidden">
            <tr><td style="height:6px;background:#E20613;font-size:0;line-height:0">&nbsp;</td></tr>
            <tr><td style="padding:32px 32px 8px">
                <p style="margin:0;font-size:12px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;color:#E20613">Administration du site CIBLE</p>
                <h1 style="margin:14px 0 0;font-size:22px;line-height:1.3">Votre code de connexion</h1>
                <p style="margin:12px 0 0;font-size:15px;line-height:1.6;color:#3A3A3A">Saisissez ce code sur la page de vérification pour terminer votre connexion.</p>
            </td></tr>
            <tr><td align="center" style="padding:20px 32px">
                <div style="display:inline-block;padding:16px 26px;border-radius:12px;background:#111111;color:#FFFFFF;font-size:34px;font-weight:bold;letter-spacing:10px;font-family:'Courier New',Courier,monospace">{{ $code }}</div>
                <p style="margin:12px 0 0;font-size:13px;color:#5F5F5F">Valable {{ $minutes }} minutes, pour une seule connexion.</p>
            </td></tr>
            <tr><td style="padding:8px 32px 30px">
                <p style="margin:0;font-size:13px;line-height:1.6;color:#5F5F5F">Demande reçue le {{ $quand }}, depuis l'adresse IP {{ $ip }}.</p>
                <p style="margin:14px 0 0;padding:14px 16px;border-radius:10px;background:#FBE0E1;font-size:13.5px;line-height:1.6;color:#111111">
                    <strong>Ce n'est pas vous ?</strong> Quelqu'un connaît le mot de passe de l'administration.
                    Ne communiquez ce code à personne, changez le mot de passe sans attendre et prévenez le studio : studio@cible-ci.com.
                </p>
            </td></tr>
        </table>
        <p style="margin:16px 0 0;font-size:12px;color:#8A8A8A">Message automatique envoyé par le site www.cible-ci.com.</p>
    </td></tr>
</table>
</body>
</html>
