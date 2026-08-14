{{-- ═══════════════════════════════════════════════════════════════════
     Layout de l'espace d'administration.

     Volontairement séparé de _layout.blade.php : le site public embarque
     des décors animés, des polices Google et de la parallaxe dont l'admin
     n'a aucun besoin. Ici, on veut une interface sobre et rapide.
     La palette CIBLE est reprise pour ne pas dépayser.
════════════════════════════════════════════════════════════════════ --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Ceinture et bretelles : l'en-tête HTTP est déjà posé par le
         middleware, mais la balise protège si la page est servie autrement. --}}
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $titre ?? 'Administration' }} — CIBLE</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icone-32.png') }}">
    <style>
    :root{
      --rouge:#E20613; --jaune:#FAB80B; --vert:#3AA835; --bleu:#3F7FC0; --violet:#81358A;
      --gris:#E6E6E6; --noir:#111111; --blanc:#FFFFFF;
      --police:system-ui,-apple-system,'Segoe UI',Roboto,Arial,sans-serif;
    }
    *{box-sizing:border-box;margin:0;padding:0}
    [hidden]{display:none!important}
    body{background:#F4F4F2;color:var(--noir);font-family:var(--police);font-size:15px;line-height:1.55}
    a{color:inherit}
    :focus-visible{outline:3px solid var(--rouge);outline-offset:2px}

    header.adm{background:var(--noir);color:#fff;padding:14px 24px;display:flex;align-items:center;gap:20px;flex-wrap:wrap}
    header.adm img{height:34px;width:auto;display:block}
    header.adm nav{display:flex;gap:16px;flex-wrap:wrap;font-weight:600;font-size:14px}
    header.adm nav a{opacity:.72;text-decoration:none;padding:4px 0;border-bottom:2px solid transparent}
    header.adm nav a:hover{opacity:1}
    header.adm nav a[aria-current="page"]{opacity:1;border-bottom-color:var(--jaune)}
    header.adm .fin{margin-left:auto;display:flex;align-items:center;gap:14px;font-size:13px}
    header.adm .voir{color:var(--jaune);text-decoration:none;font-weight:600}

    main{max-width:920px;margin:0 auto;padding:32px 24px 80px}
    h1{font-size:26px;font-weight:800;letter-spacing:-.01em}
    h1 + .intro{color:#666;margin-top:8px;font-size:14.5px}
    .carte{background:#fff;border:1px solid var(--gris);border-radius:14px;padding:26px;margin-top:22px}
    .carte h2{font-size:16px;font-weight:800;margin-bottom:4px}
    .carte .aide{color:#777;font-size:13.5px;margin-bottom:20px}

    label{display:block;font-weight:700;font-size:12.5px;text-transform:uppercase;letter-spacing:.05em;color:#444;margin-bottom:6px}
    input[type=text],input[type=email],input[type=password],input[type=file],select,textarea{
        width:100%;border:1.5px solid var(--gris);border-radius:9px;padding:11px 13px;
        font-family:var(--police);font-size:15px;background:#fff;color:var(--noir);
    }
    input:focus,select:focus,textarea:focus{outline:none;border-color:var(--rouge);box-shadow:0 0 0 3px rgba(226,6,19,.1)}
    textarea{min-height:120px;resize:vertical}
    .champ{margin-bottom:18px}
    .champ .note{font-size:12.5px;color:#888;margin-top:5px;font-weight:400;text-transform:none;letter-spacing:0}
    .duo{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    @media(max-width:640px){.duo{grid-template-columns:1fr}}

    .bt{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:999px;padding:12px 22px;
        font-family:var(--police);font-weight:700;font-size:14px;cursor:pointer;text-decoration:none}
    .bt-rouge{background:var(--rouge);color:#fff}
    .bt-rouge:hover{background:#B00510}
    .bt-gris{background:var(--gris);color:var(--noir)}
    .bt-gris:hover{background:#D5D5D5}
    .bt-lien{background:none;color:#777;padding:12px 4px;text-decoration:underline}
    .actions{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-top:24px}

    .alerte{border-radius:11px;padding:14px 18px;margin-bottom:20px;font-size:14px;font-weight:600}
    .a-ok{background:#dcfce7;border:1px solid #86efac;color:#166534}
    .a-ko{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b}
    .a-info{background:#dbeafe;border:1px solid #93c5fd;color:#1e40af}
    .a-attention{background:#fef3c7;border:1px solid #fcd34d;color:#92400e}
    .erreur{color:var(--rouge);font-size:13px;font-weight:600;margin-top:5px}

    .liste{display:grid;gap:12px}
    .ligne{display:flex;align-items:center;gap:16px;background:#fff;border:1px solid var(--gris);
           border-radius:12px;padding:14px 18px;text-decoration:none}
    .ligne:hover{border-color:#BBB}
    .ligne .vign{width:64px;height:48px;border-radius:7px;object-fit:cover;background:var(--gris);flex-shrink:0}
    .ligne .nom{font-weight:700}
    .ligne .cat{font-size:13px;color:#777}
    .ligne .fleche{margin-left:auto;color:#BBB;font-size:20px}
    .pastille{width:11px;height:11px;border-radius:50%;flex-shrink:0}
    </style>
</head>
<body>

@if(!empty($connecte))
<header class="adm">
    <img src="{{ asset('images/logon.png') }}" alt="CIBLE">
    <nav>
        <a href="{{ route('admin.tableau') }}"      @if(($onglet ?? '')==='tableau') aria-current="page" @endif>Tableau de bord</a>
        <a href="{{ route('admin.coordonnees') }}"  @if(($onglet ?? '')==='coordonnees') aria-current="page" @endif>Coordonnées</a>
        <a href="{{ route('admin.chiffres') }}"     @if(($onglet ?? '')==='chiffres') aria-current="page" @endif>Chiffres clés</a>
        <a href="{{ route('admin.realisations') }}" @if(($onglet ?? '')==='realisations') aria-current="page" @endif>Réalisations</a>
    </nav>
    <div class="fin">
        <a class="voir" href="{{ route('home') }}" target="_blank" rel="noopener">Voir le site ↗</a>
        <form method="POST" action="{{ route('admin.deconnexion') }}">
            @csrf
            <button type="submit" class="bt bt-lien" style="color:rgba(255,255,255,.7)">Déconnexion</button>
        </form>
    </div>
</header>
@endif

<main>
    @if(session('ok'))    <div class="alerte a-ok">✓ {{ session('ok') }}</div> @endif
    @if(session('info'))  <div class="alerte a-info">{{ session('info') }}</div> @endif
    @error('stockage')    <div class="alerte a-ko">⚠ {{ $message }}</div> @enderror

    @yield('contenu')
</main>

</body>
</html>
