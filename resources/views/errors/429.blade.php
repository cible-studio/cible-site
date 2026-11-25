{{-- Limite d'essais atteinte, hors connexion admin et formulaire de contact
     (ceux-là reviennent sur leur page avec un message, cf. bootstrap/app.php).
     Page autonome : aucune dépendance au layout, elle doit s'afficher même
     quand le reste du site est sollicité. --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Trop de demandes — CIBLE</title>
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:#F4F4F2;color:#111;
             font-family:system-ui,-apple-system,'Segoe UI',Roboto,Arial,sans-serif;padding:24px}
        .carte{max-width:460px;background:#fff;border-radius:18px;padding:36px 30px;text-align:center;border-top:6px solid #E20613}
        h1{margin:0;font-size:24px}
        p{margin:14px 0 0;font-size:16px;line-height:1.6;color:#3A3A3A}
        a{display:inline-flex;align-items:center;min-height:48px;margin-top:24px;padding:0 24px;border-radius:999px;
          background:#E20613;color:#fff;font-weight:700;text-decoration:none}
    </style>
</head>
<body>
    <div class="carte">
        <h1>Trop de demandes en peu de temps</h1>
        <p>Pour protéger le site, le nombre d'essais est limité. Merci de patienter quelques minutes avant de réessayer.</p>
        <a href="{{ url('/') }}">Retour au site</a>
    </div>
</body>
</html>
