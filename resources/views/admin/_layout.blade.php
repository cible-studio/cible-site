{{-- ═══════════════════════════════════════════════════════════════════
     Coque de l'espace d'administration.

     Volontairement séparée de _layout.blade.php : le site public embarque
     des décors animés, des polices Google et de la parallaxe dont l'admin
     n'a aucun besoin. Ici, on veut une interface sobre et rapide.
     La palette CIBLE est reprise pour ne pas dépayser.

     Navigation en barre latérale et non en barre supérieure : il y a une
     dizaine d'écrans, une rangée horizontale les tassait sur une ligne
     illisible et n'offrait aucun regroupement. La colonne permet de
     séparer « les pages du site » de « ce qui est commun à tout le site »,
     ce qui est la vraie distinction pour la personne qui édite.
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
      --barre:264px;
    }
    *{box-sizing:border-box;margin:0;padding:0}
    [hidden]{display:none!important}
    body{background:#F4F4F2;color:var(--noir);font-family:var(--police);font-size:15px;line-height:1.55}
    a{color:inherit}
    :focus-visible{outline:3px solid var(--rouge);outline-offset:2px}

    /* ─────────────────────────── Barre latérale ─────────────────────── */
    .cote{
        position:fixed;inset:0 auto 0 0;width:var(--barre);
        background:var(--noir);color:#fff;display:flex;flex-direction:column;
        z-index:40;
    }
    .cote-tete{padding:22px 22px 18px;border-bottom:1px solid rgba(255,255,255,.09)}
    .cote-tete img{height:32px;width:auto;display:block}
    .cote-tete .sous{font-size:11.5px;color:rgba(255,255,255,.42);margin-top:9px;
        text-transform:uppercase;letter-spacing:.1em;font-weight:700}

    .cote-nav{flex:1;overflow-y:auto;padding:16px 12px 12px}
    .cote-nav .groupe{font-size:10.5px;text-transform:uppercase;letter-spacing:.11em;
        font-weight:800;color:rgba(255,255,255,.34);padding:16px 12px 8px}
    .cote-nav .groupe:first-child{padding-top:4px}
    .cote-nav a{
        display:flex;align-items:center;gap:11px;
        padding:9px 12px;border-radius:9px;text-decoration:none;
        font-size:14px;font-weight:600;color:rgba(255,255,255,.72);
        border-left:3px solid transparent;
    }
    .cote-nav a:hover{background:rgba(255,255,255,.07);color:#fff}
    .cote-nav a[aria-current="page"]{
        background:rgba(255,255,255,.11);color:#fff;border-left-color:var(--rouge);
    }
    .cote-nav .pastille{width:9px;height:9px;border-radius:50%;flex-shrink:0;background:rgba(255,255,255,.3)}
    .cote-nav .marque{margin-left:auto;width:7px;height:7px;border-radius:50%;background:var(--jaune)}

    .cote-pied{padding:14px 12px;border-top:1px solid rgba(255,255,255,.09);display:grid;gap:4px}
    .cote-pied a,.cote-pied button{
        display:flex;align-items:center;gap:10px;width:100%;text-align:left;
        padding:9px 12px;border-radius:9px;border:0;background:none;cursor:pointer;
        font-family:var(--police);font-size:13.5px;font-weight:600;
        color:rgba(255,255,255,.66);text-decoration:none;
    }
    .cote-pied a:hover,.cote-pied button:hover{background:rgba(255,255,255,.07);color:#fff}
    .cote-pied .voir{color:var(--jaune)}

    /* ─────────────────────── Barre supérieure ───────────────────────── */
    .haut{
        position:sticky;top:0;z-index:30;
        background:rgba(255,255,255,.92);backdrop-filter:blur(8px);
        border-bottom:1px solid var(--gris);
        padding:0 clamp(18px,3vw,36px);height:60px;
        display:flex;align-items:center;gap:14px;
    }
    .haut .fil{font-size:13.5px;color:#777;font-weight:600}
    .haut .fil b{color:var(--noir);font-weight:800}
    .haut .droite{margin-left:auto;display:flex;align-items:center;gap:10px}

    .burger{display:none;border:1.5px solid var(--gris);background:#fff;border-radius:9px;
        width:38px;height:38px;cursor:pointer;font-size:17px;line-height:1}

    /* ─────────────────────────── Contenu ────────────────────────────── */
    .zone{margin-left:var(--barre);min-height:100vh}
    main{max-width:980px;margin:0 auto;padding:30px clamp(18px,3vw,36px) 90px}
    h1{font-size:26px;font-weight:800;letter-spacing:-.01em}
    h1 + .intro{color:#666;margin-top:8px;font-size:14.5px}
    .carte{background:#fff;border:1px solid var(--gris);border-radius:14px;padding:26px;margin-top:22px;scroll-margin-top:126px}
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
    .bt-petit{padding:9px 16px;font-size:13px}
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

    .voile{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:35}

    /* ───────────────────────────── Mobile ───────────────────────────── */
    @media(max-width:960px){
        .cote{transform:translateX(-100%);transition:transform .22s ease}
        body.ouvert .cote{transform:none}
        body.ouvert .voile{display:block}
        .zone{margin-left:0}
        .burger{display:block}
    }
    </style>
</head>
<body>

@if(!empty($connecte))
@php
    // Navigation construite depuis le schéma : ajouter une page au schéma
    // la fait apparaître ici sans toucher au gabarit.
    $pages = \App\Support\Schema::pages();
    $onglet = $onglet ?? '';
    $transversaux = [
        'coordonnees'  => ['Coordonnées',  'admin.coordonnees'],
        'chiffres'     => ['Chiffres clés', 'admin.chiffres'],
        'realisations' => ['Réalisations',  'admin.realisations'],
    ];
    // Fil d'Ariane : évite de dupliquer le titre dans chaque vue.
    $courant = $pages[$onglet]['titre']
        ?? ($transversaux[$onglet][0] ?? 'Tableau de bord');
@endphp

<aside class="cote" id="cote">
    <div class="cote-tete">
        <img src="{{ asset('images/logon.png') }}" alt="CIBLE">
        <div class="sous">Administration du site</div>
    </div>

    <nav class="cote-nav">
        <div class="groupe">Vue d'ensemble</div>
        <a href="{{ route('admin.tableau') }}" @if($onglet==='tableau') aria-current="page" @endif>
            <span class="pastille" style="background:#888"></span> Tableau de bord
        </a>

        <div class="groupe">Les pages du site</div>
        @foreach($pages as $c => $p)
            <a href="{{ route('admin.page', $c) }}" @if($onglet===$c) aria-current="page" @endif>
                <span class="pastille" style="background:{{ $p['couleur'] }}"></span>
                {{ \Illuminate\Support\Str::of($p['titre'])->replace("Page d'", '')->replace('Page ', '')->ucfirst() }}
                @if(\App\Support\Contenu::estSurchargee($c))
                    {{-- Point jaune : cette page a été modifiée depuis l'admin,
                         elle ne correspond donc plus au contenu livré. --}}
                    <span class="marque" title="Modifiée depuis l'admin"></span>
                @endif
            </a>
        @endforeach

        <div class="groupe">Commun à tout le site</div>
        @foreach($transversaux as $c => [$nom, $route])
            <a href="{{ route($route) }}" @if($onglet===$c) aria-current="page" @endif>
                <span class="pastille" style="background:#888"></span> {{ $nom }}
                @if(\App\Support\Contenu::estSurchargee($c))
                    <span class="marque" title="Modifié depuis l'admin"></span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="cote-pied">
        <a class="voir" href="{{ route('home') }}" target="_blank" rel="noopener">↗ Voir le site</a>
        <form method="POST" action="{{ route('admin.deconnexion') }}">
            @csrf
            <button type="submit">⏻ Déconnexion</button>
        </form>
    </div>
</aside>

<div class="voile" id="voile"></div>

<div class="zone">
    <div class="haut">
        <button class="burger" id="burger" type="button" aria-label="Ouvrir le menu" aria-controls="cote" aria-expanded="false">☰</button>
        <div class="fil">Administration &nbsp;›&nbsp; <b>{{ $courant }}</b></div>
        <div class="droite">
            @if(!empty($lienPage))
                <a class="bt bt-gris bt-petit" href="{{ $lienPage }}" target="_blank" rel="noopener">Voir la page ↗</a>
            @endif
        </div>
    </div>
@endif

    <main>
        @if(session('ok'))    <div class="alerte a-ok">✓ {{ session('ok') }}</div> @endif
        @if(session('info'))  <div class="alerte a-info">{{ session('info') }}</div> @endif
        @error('stockage')    <div class="alerte a-ko">⚠ {{ $message }}</div> @enderror

        @yield('contenu')
    </main>

@if(!empty($connecte))
</div>

<script>
// Tiroir mobile. Sans JS la barre reste masquée sous 960px : on ajoute donc
// aussi la fermeture au clic sur le voile et à Échap, sinon on peut se
// retrouver bloqué derrière le menu sur un téléphone.
(function () {
    var b = document.getElementById('burger'),
        v = document.getElementById('voile');
    function bascule(ouvrir) {
        document.body.classList.toggle('ouvert', ouvrir);
        b.setAttribute('aria-expanded', ouvrir ? 'true' : 'false');
    }
    b.addEventListener('click', function () {
        bascule(!document.body.classList.contains('ouvert'));
    });
    v.addEventListener('click', function () { bascule(false); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { bascule(false); }
    });
})();
</script>
@endif

</body>
</html>
