{{-- Récapitulatif mensuel des demandes — chiffres seulement (styles en ligne pour les messageries). --}}
@php
    $total = $stats['total'];
    $ecart = $total - $totalAvant;
    $titres = \App\Support\Demandes::MULTIPLES + \App\Support\Demandes::UNIQUES;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:0;background:#F4F4F2;font-family:Arial,Helvetica,sans-serif;color:#111111">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F4F2;padding:32px 12px">
<tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#FFFFFF;border-radius:16px;overflow:hidden">
    <tr><td style="height:6px;background:#E20613;font-size:0;line-height:0">&nbsp;</td></tr>
    <tr><td style="padding:30px 32px 6px">
        <p style="margin:0;font-size:12px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;color:#E20613">Site CIBLE · Rapport mensuel</p>
        <h1 style="margin:12px 0 0;font-size:24px;line-height:1.25">Demandes reçues — {{ $libelle }}</h1>
    </td></tr>
    <tr><td style="padding:18px 32px 8px">
        <table role="presentation" cellpadding="0" cellspacing="0"><tr>
            <td style="padding:16px 22px;border-radius:12px;background:#111111;color:#FFFFFF">
                <div style="font-size:40px;font-weight:bold;line-height:1">{{ $total }}</div>
                <div style="margin-top:6px;font-size:13px;color:#DDDDDD">demande{{ $total > 1 ? 's' : '' }} de recommandation média</div>
            </td>
        </tr></table>
        <p style="margin:12px 0 0;font-size:14px;color:#3A3A3A">
            {{ $libelleAvant }} : {{ $totalAvant }} demande{{ $totalAvant > 1 ? 's' : '' }}
            ({{ $ecart === 0 ? 'stable' : ($ecart > 0 ? '+' . $ecart : $ecart) }}).
            @if($stats['mails_non_partis'] > 0)
                <br><strong style="color:#B00510">{{ $stats['mails_non_partis'] }} demande(s) n'ont pas pu partir par e-mail : à consulter dans l'administration.</strong>
            @endif
        </p>
    </td></tr>
    @if($total > 0)
        @foreach($titres as $cle => $titre)
            @php $r = array_slice($stats['repartitions'][$cle] ?? [], 0, 5, true); @endphp
            @continue(empty($r))
            <tr><td style="padding:16px 32px 0">
                <p style="margin:0 0 6px;font-size:13px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#5F5F5F">{{ $titre }}</p>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px">
                    @foreach($r as $valeur => $n)
                        <tr>
                            <td style="padding:6px 0;border-bottom:1px solid #EEEEEC">{{ $valeur }}</td>
                            <td align="right" style="padding:6px 0;border-bottom:1px solid #EEEEEC;font-weight:bold;width:60px">{{ $n }}</td>
                        </tr>
                    @endforeach
                </table>
            </td></tr>
        @endforeach
    @endif
    <tr><td style="padding:26px 32px 30px">
        <table role="presentation" cellpadding="0" cellspacing="0"><tr>
            <td style="border-radius:999px;background:#E20613">
                <a href="{{ $lien }}" style="display:inline-block;padding:14px 26px;font-size:15px;font-weight:bold;color:#FFFFFF;text-decoration:none">Voir le détail des demandes</a>
            </td>
        </tr></table>
        <p style="margin:12px 0 0;font-size:12.5px;line-height:1.6;color:#5F5F5F">Accès réservé : mot de passe et code de connexion requis.</p>
    </td></tr>
</table>
<p style="margin:16px 0 0;font-size:12px;color:#8A8A8A">Message automatique envoyé par le site www.cible-ci.com le 1er de chaque mois.</p>
</td></tr>
</table>
</body>
</html>
