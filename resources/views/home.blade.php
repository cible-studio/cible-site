@extends('_layout', [
    'seo_title'       => 'CIBLE — Régie publicitaire en Côte d\'Ivoire · +400 panneaux · Vous visez juste',
    'seo_description' => 'Régie publicitaire ivoirienne depuis 1994. Affichage grand format, publicité mobile, communication 360°. +400 panneaux dans 31 communes. Réponse sous 24 h ouvrées.',
    'current'         => 'home',
])

@push('page-css')
    /* ───── HERO ───── */
    .hero-wrap{position:relative}
    .hero{display:grid;grid-template-columns:1.05fr .95fr;gap:clamp(24px,4vw,56px);align-items:center;padding:clamp(36px,5.5vw,80px) var(--pad) 0;position:relative}
    @media(max-width:920px){.hero{grid-template-columns:1fr}}
    .hero>*{position:relative;z-index:2}
    .hero .sur{color:var(--vert)}
    /* Hero h1 2026-08-10 — nouveaux textes : la ligne 2 passe de 14 à 25
       caractères ("Notre intelligence média."). L'ancien cap de 96px la
       faisait déborder de la colonne (651px à 1440px) et le line-height
       .95 provoquait la superposition Poppins 900 déjà corrigée en août.
       On aligne sur l'échelle .t1 unifiée (32→64px) + line-height 1.03,
       et text-wrap:balance répartit proprement les lignes qui bouclent. */
    .hero h1{margin-top:16px;font-family:var(--titre);font-weight:900;line-height:1.03;letter-spacing:-.03em;font-size:clamp(32px,5vw,64px)}
    .hero h1 .l{display:block;text-wrap:balance;opacity:0;transform:translateY(26px);animation:monte .8s cubic-bezier(.2,.8,.3,1) forwards}
    .hero h1 .l:nth-child(1){animation-delay:.05s}
    .hero h1 .l:nth-child(2){animation-delay:.16s}
    .hero h1 .l:nth-child(3){animation-delay:.27s;color:var(--rouge)}
    @keyframes monte{to{opacity:1;transform:none}}
    .hero .sous-titre{margin-top:20px;max-width:52ch;font-family:var(--corps);font-weight:700;font-size:clamp(16px,1.6vw,20px);line-height:1.45;color:#3A3A3A;opacity:0;animation:monte .8s .36s cubic-bezier(.2,.8,.3,1) forwards}
    .hero .accroche{margin-top:16px;max-width:48ch;font-size:clamp(15px,1.4vw,18px);color:#4A4A4A;opacity:0;animation:monte .8s .46s cubic-bezier(.2,.8,.3,1) forwards}
    .hero .accroche strong{color:var(--rouge)}
    .actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:30px;opacity:0;animation:monte .8s .52s cubic-bezier(.2,.8,.3,1) forwards}
    .hero-visuel{position:relative;aspect-ratio:1/1;border-radius:999px 28px 28px 999px;overflow:hidden;background:var(--gris);opacity:0;transform:scale(.94);animation:zoom .9s .3s cubic-bezier(.2,.8,.3,1) forwards}
    @keyframes zoom{to{opacity:1;transform:none}}
    @media(max-width:920px){.hero-visuel{border-radius:28px;aspect-ratio:4/3}}

    /* ───── marque — image + bloc violet (refonte v3 2026-08-04) ─────
       Retour au layout image ronde + bloc violet avec améliorations :
       - Halo doré subtil autour de la photo (glow radial)
       - Pastilles numérotées 01/02/03 devant chaque h3 (couleurs palette)
       - Séparateurs élégants entre articles (trait dégradé)
       - Signature finale imposante avec accent visuel */
    .marque{
        display:grid;
        grid-template-columns:0.85fr 1.15fr;
        align-items:center;
        gap:0;
        min-height:min(80vh,700px);
        background:var(--violet);
        position:relative;
        overflow:hidden;
    }
    @media(max-width:900px){.marque{grid-template-columns:1fr;min-height:auto}}

    /* Wrapper image avec halo doré via ::before (glow radial ambient) */
    .marque-img{
        position:relative;
        aspect-ratio:1/1;
        max-width:min(88%,540px);
        margin:clamp(30px,4vw,60px) auto;
        z-index:2;
    }
    .marque-img::before{
        content:"";position:absolute;
        inset:-8%;border-radius:50%;
        background:radial-gradient(circle,rgba(250,184,11,.45) 0%,rgba(250,184,11,0) 65%);
        z-index:0;pointer-events:none;
        animation:halo 8s ease-in-out infinite alternate;
    }
    @keyframes halo{
        from{transform:scale(1);opacity:.9}
        to{transform:scale(1.08);opacity:1}
    }
    .marque-img-inner{
        position:relative;z-index:1;
        aspect-ratio:1/1;border-radius:50%;
        overflow:hidden;background:var(--gris);
        box-shadow:0 30px 70px -15px rgba(0,0,0,.55),
                   0 0 0 6px rgba(255,255,255,.08);
    }
    @media(max-width:900px){.marque-img{max-width:min(72%,340px);margin:clamp(40px,6vw,60px) auto 20px}}

    /* Bloc texte violet — respire, hiérarchie renforcée */
    .marque-txt{
        color:var(--blanc);
        padding:clamp(40px,5vw,80px) clamp(30px,5vw,80px);
        display:flex;flex-direction:column;justify-content:center;
        position:relative;overflow:hidden;
    }
    .marque-txt > .rev{position:relative;z-index:2}
    .marque-txt .sur{color:var(--jaune);opacity:.95}
    .marque-txt .intro-p{
        margin-top:18px;max-width:52ch;
        font-size:clamp(15px,1.4vw,17px);line-height:1.65;
        opacity:.88;font-weight:500;
    }

    /* Récit — cards distinctes (v4 2026-08-04) : chaque article dans
       son propre bloc blanc translucide avec bordure gauche jaune,
       hover surbrillance. Plus "bloc" que la version séparateurs. */
    .recit{margin-top:32px;display:flex;flex-direction:column;gap:14px}
    .recit article{
        position:relative;
        padding:20px 22px 20px 24px;
        background:rgba(255,255,255,.06);
        border:1px solid rgba(255,255,255,.10);
        border-left:3px solid var(--jaune);
        border-radius:14px;
        backdrop-filter:blur(4px);
        transition:background .25s cubic-bezier(.2,.8,.3,1),
                   transform .25s cubic-bezier(.2,.8,.3,1),
                   border-left-width .2s;
    }
    .recit article:hover{
        background:rgba(255,255,255,.11);
        transform:translateX(4px);
        border-left-width:5px;
    }
    .recit article h3{
        font-family:var(--titre);font-weight:800;
        font-size:clamp(15px,1.6vw,18px);
        color:var(--jaune);margin-bottom:10px;
        display:flex;align-items:center;gap:12px;line-height:1.3;
    }
    /* Badge numéroté rond plein (au lieu de outlined) : plus présent */
    .recit article h3::before{
        content:attr(data-num);
        font-family:var(--titre);font-weight:900;font-size:12px;
        color:var(--violet);background:var(--jaune);
        width:28px;height:28px;border-radius:50%;
        display:inline-flex;align-items:center;justify-content:center;
        flex-shrink:0;
        box-shadow:0 4px 12px -3px rgba(0,0,0,.4);
    }
    .recit article p{
        margin:0;font-size:14.5px;opacity:.92;
        line-height:1.65;color:#fff;
    }

    /* Signature finale — call-out imposant avec accent visuel */
    .marque-signature{
        margin-top:32px;padding-top:24px;
        border-top:2px solid var(--jaune);
        font-family:var(--titre);font-weight:900;
        font-size:clamp(18px,2.1vw,24px);
        line-height:1.35;color:#fff;
    }
    /* em conservé pour l'historique, strong = emphase saisie dans l'admin */
    .marque-signature em,
    .marque-signature strong{
        color:var(--jaune);font-style:normal;
    }

    /* ───── territoires (onglets) ───── */
    .territoires{padding:clamp(56px,8vw,100px) var(--pad) 0}
    /* Centrage 2026-08-04ter : la font-size hérite maintenant du .t1
       global unifié (clamp 32-64px). Plus d'override ici — cohérence
       avec tous les autres titres de section du site. */
    .entete{max-width:900px;position:relative;z-index:2;text-align:center;margin-left:auto;margin-right:auto}
    .entete .sur{color:var(--bleu)}
    .entete .t1{margin-top:14px}
    .entete p{margin-top:18px;color:#444;max-width:60ch;margin-left:auto;margin-right:auto}
    /* ───── onglets territoires ─────
       Refonte 2026-08-13 : les 4 onglets inactifs étaient des pastilles
       grises identiques, rien n'indiquait qu'ils étaient cliquables ni
       qu'il y avait d'autres contenus derrière. Trois signaux ajoutés :
       1. chaque onglet porte SA couleur de palette dès l'état inactif
          (à 45 %) — on voit 5 choses distinctes, pas un décor ;
       2. l'onglet inactif est une vraie pastille bordée qui se soulève
          au survol, avec le vocabulaire d'un bouton ;
       3. une consigne explicite + une flèche de marque pointant dessus. */
    .onglets-aide{
        display:flex;align-items:center;justify-content:center;gap:12px;
        margin-top:30px;position:relative;z-index:2;
        font-family:var(--titre);font-weight:700;font-size:13.5px;color:#777;
    }
    .onglets-aide .dessin{width:34px;flex-shrink:0;--c:var(--rouge);transform:rotate(96deg) scaleX(-1);animation:pointe 2.4s ease-in-out infinite}
    @keyframes pointe{0%,100%{transform:rotate(96deg) scaleX(-1) translateY(0)}50%{transform:rotate(96deg) scaleX(-1) translateY(5px)}}

    .onglets{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin-top:18px;position:relative;z-index:2}
    .onglet{
        display:flex;flex-direction:column;align-items:center;gap:10px;
        background:none;border:0;padding:10px 14px 12px;border-radius:18px;cursor:pointer;
        font-family:var(--titre);font-weight:800;font-size:14px;color:var(--tc);
        transition:color .25s,background .25s,transform .25s cubic-bezier(.2,.8,.3,1);
    }
    /* --tc = couleur du LIBELLÉ, --bc = couleur de la PASTILLE. Les deux
       sont dissociées pour l'onglet jaune : #FAB80B en texte sur blanc
       plafonne à 1,9:1 de contraste, très en dessous du seuil lisible.
       La pastille reste jaune, le libellé passe en noir. */
    .onglet .forme{width:clamp(56px,7vw,88px);height:clamp(22px,3vw,30px);background:var(--bc,currentColor);opacity:.45;border-radius:46% 46% 6px 6px / 60% 60% 6px 6px;transition:opacity .3s,height .35s cubic-bezier(.2,.9,.3,1)}
    .onglet::after{content:"";display:block;width:26px;height:3px;border-radius:2px;background:transparent;margin-top:8px;transition:background .25s}
    .onglet:hover{background:rgba(0,0,0,.04);transform:translateY(-3px)}
    .onglet:hover .forme{opacity:.85}
    /* L'onglet actif se distingue par un fond ET un soulignement plein, pas
       seulement par la teinte — reste lisible pour un daltonien. */
    .onglet[aria-selected="true"]{background:rgba(0,0,0,.05)}
    .onglet[aria-selected="true"] .forme{opacity:1;height:clamp(34px,4.4vw,48px)}
    .onglet[aria-selected="true"]::after{background:var(--bc,currentColor)}
    #o-rue{--tc:var(--rouge)}
    #o-mouvement{--tc:var(--noir);--bc:var(--jaune)}
    #o-ecran{--tc:var(--vert)}
    #o-digital{--tc:var(--bleu)}
    #o-terrain{--tc:var(--violet)}
    @media(max-width:560px){
        .onglet{font-size:12.5px;padding:8px 10px 10px}
        .onglets{gap:6px}
    }

    .scene{position:relative;margin-top:34px;border-radius:34px 34px 0 0;overflow:hidden;transition:background .55s cubic-bezier(.4,0,.2,1)}
    .pan{position:relative;z-index:2;padding:clamp(28px,4vw,62px);color:var(--blanc);display:grid;grid-template-columns:1fr 1fr;gap:clamp(22px,3.5vw,52px);align-items:center}
    @media(max-width:900px){.pan{grid-template-columns:1fr}}
    .pan[hidden]{display:none}
    .pan.entre>*{animation:glisse .55s cubic-bezier(.2,.8,.3,1) both}
    .pan.entre>*:nth-child(2){animation-delay:.09s}
    @keyframes glisse{from{opacity:0;transform:translateX(22px)}to{opacity:1;transform:none}}
    .pan h3{font-family:var(--titre);font-weight:900;font-size:clamp(28px,3.8vw,50px);line-height:1;letter-spacing:-.025em;margin-top:12px}
    .pan p{margin-top:16px;max-width:46ch;opacity:.95}
    .pan .grosnum{font-family:var(--titre);font-weight:900;font-size:clamp(52px,7.6vw,104px);line-height:.9;letter-spacing:-.04em}
    .tags{list-style:none;display:flex;flex-wrap:wrap;gap:8px;margin-top:22px;padding:0}
    .tags li{border:1.5px solid currentColor;opacity:.85;padding:6px 13px;border-radius:999px;font-family:var(--titre);font-weight:600;font-size:13px}
    .preuve{margin-top:24px;padding-left:16px;border-left:4px solid currentColor;font-size:15px}
    .pan-visuel{aspect-ratio:4/3;border-radius:22px;overflow:hidden}
    #t-mouvement{color:var(--noir)}
    .balayage{position:absolute;inset:0;z-index:1;pointer-events:none;overflow:hidden}
    .balayage span{position:absolute;opacity:0}
    .scene.switch .balayage span{animation:traverse .95s cubic-bezier(.3,.7,.3,1) both}
    .scene.switch .balayage span:nth-child(2){animation-delay:.1s}
    .scene.switch .balayage span:nth-child(3){animation-delay:.2s}
    @keyframes traverse{
      0%{opacity:0;transform:translateX(-30%) rotate(var(--r,0deg)) scale(.9)}
      35%{opacity:.22}
      100%{opacity:0;transform:translateX(60%) rotate(calc(var(--r,0deg) + 20deg)) scale(1.1)}
    }
    .balayage svg{fill:#fff}

    /* ───── mission (gris) ─────
       Habillage 2026-08-13 : la section n'avait aucun élément de marque.
       Trois plumes de la palette en très basse opacité, dans le même
       registre que les sections Marque et Contact. Le texte ne bouge pas
       et reste au-dessus (z-index). */
    .mission{background:var(--gris);padding:clamp(46px,6vw,86px) var(--pad);text-align:center;position:relative;overflow:hidden}
    .mission > p{position:relative;z-index:2}
    .mission p{max-width:64ch;margin:0 auto;font-size:clamp(16px,1.5vw,19px);color:#333}
    .mission p + p{margin-top:20px}
    .mission .fort{margin-top:22px;font-family:var(--titre);font-weight:800;font-size:clamp(17px,2vw,23px);color:var(--noir);max-width:44ch}
    .mission .dessin{position:absolute;pointer-events:none;z-index:0}

    /* ───── travaux (grille 6) ───── */
    .travaux{padding:clamp(56px,8vw,100px) var(--pad)}
    .travaux-tete{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap;margin-bottom:36px;position:relative;z-index:2}
    .travaux .sur{color:var(--jaune)}
    .grille{display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(14px,2vw,26px);position:relative;z-index:2}
    @media(max-width:900px){.grille{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:560px){.grille{grid-template-columns:1fr}}
    .carte{transition:transform .28s cubic-bezier(.2,.8,.3,1)}
    .carte:hover{transform:translateY(-8px)}
    .carte .vign{aspect-ratio:4/3;border-radius:22px;overflow:hidden;position:relative}
    .carte .vign::after{content:"";position:absolute;inset:auto 0 0 0;height:7px;background:var(--c);transform:scaleX(0);transform-origin:left;transition:transform .35s cubic-bezier(.2,.8,.3,1)}
    .carte:hover .vign::after{transform:scaleX(1)}
    .carte h4{font-family:var(--titre);font-weight:800;font-size:18px;margin-top:14px}
    .carte span{font-size:14px;color:#666}
    .pastille{display:inline-block;width:10px;height:10px;border-radius:50%;margin-right:8px;vertical-align:middle;background:var(--c)}

    /* ───── réseau (bleu) ───── */
    .reseau{background:var(--bleu);color:var(--blanc);padding:clamp(56px,8vw,110px) var(--pad)}
    .reseau-grille{position:relative;z-index:2;display:grid;grid-template-columns:1fr 1fr;gap:clamp(26px,4vw,60px);align-items:center}
    @media(max-width:900px){.reseau-grille{grid-template-columns:1fr}}
    .reseau p{margin-top:20px;max-width:48ch;opacity:.95}
    .stats{display:flex;gap:44px;flex-wrap:wrap;margin-top:36px}
    .stats .v{font-family:var(--titre);font-weight:900;font-size:clamp(46px,6vw,80px);line-height:.86;letter-spacing:-.04em}
    .stats .l{font-family:var(--titre);font-weight:600;font-size:13px;opacity:.9;margin-top:10px}
    .carte-slot{aspect-ratio:16/11;border-radius:26px;overflow:hidden}
    .note{margin-top:14px;font-size:13px;opacity:.9}

    /* ───── distinctions ───── */
    .dist-sec{padding:clamp(50px,7vw,90px) var(--pad);text-align:center}
    .dist-sec .sur{color:var(--vert)}
    /* max-width élargi 24ch→38ch (2026-08-04) — évite la cassure serrée
       "Trois distinctions de / l'État ivoirien" avec letters qui touchent. */
    .dist-sec .t2{max-width:38ch;margin-left:auto;margin-right:auto}
    .dist-grille{display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(18px,3vw,44px);margin-top:40px;text-align:left}
    @media(max-width:820px){.dist-grille{grid-template-columns:1fr}}
    .dist{border-top:6px solid var(--c);padding-top:16px}
    .dist .an{font-family:var(--titre);font-weight:900;font-size:32px;color:var(--c)}
    .dist h4{font-family:var(--titre);font-weight:800;font-size:16px;margin-top:6px;line-height:1.3}
    .dist p{font-size:14px;color:#5F5F5F;margin-top:8px}

    /* ───── contact (jaune + motif plumes) ───── */
    .contact-home{background:var(--jaune);color:var(--noir);padding:clamp(60px,8vw,120px) var(--pad);position:relative;overflow:hidden}
    .motif{position:absolute;inset:-10% -10%;background-image:var(--tuile);background-size:340px 340px;animation:motifDerive 60s linear infinite;pointer-events:none;z-index:0}
    @keyframes motifDerive{from{background-position:0 0}to{background-position:340px 340px}}
    .contact-inner{position:relative;z-index:2}
    .contact-home .t1{max-width:16ch}
    .contact-home p{margin-top:20px;max-width:44ch;font-size:clamp(16px,1.6vw,20px)}
    .coord{margin-top:36px;display:flex;flex-wrap:wrap;gap:12px 40px;background:var(--blanc);padding:22px 28px;border-radius:22px;font-family:var(--titre);font-weight:700;font-size:15px}
    .coord a:hover{color:var(--rouge)}
@endpush

@section('content')


{{-- ═══ HERO ═══ --}}
<section class="hero-wrap">
    <div class="couche" data-parallaxe>
        <span class="fleche dessin f-fleche" style="--c:var(--vert);--op:.85;top:6%;left:40%;width:74px;--r:-12deg;--dur:17s;--del:.7s"></span>
        <span class="fleche dessin f-fleche" style="--c:var(--bleu);--op:.8;top:52%;left:4%;width:62px;--r:14deg;--dur:21s;--del:.95s;--dx:-14px;--dy:20px"></span>
        <span class="fleche dessin f-fleche" style="--c:var(--jaune);--op:.9;top:16%;right:3%;width:70px;--r:8deg;--dur:19s;--del:1.1s"></span>
        <span class="fleche dessin f-plume" style="--c:var(--violet);--op:.55;bottom:2%;left:26%;width:52px;--r:-24deg;--dur:24s;--del:1.3s;--dx:22px;--dy:14px"></span>
    </div>
    <div class="hero">
        <div>
            <span class="sur">{{ \App\Support\Contenu::get('accueil.hero_surtitre') }}</span>
            <h1>
                <span class="l">{{ \App\Support\Contenu::get('accueil.hero_titre_1') }}</span>
                <span class="l">{{ \App\Support\Contenu::get('accueil.hero_titre_2') }}</span>
                <span class="l">{{ \App\Support\Contenu::get('accueil.hero_titre_3') }}</span>
            </h1>
            <h2 class="sous-titre">{{ \App\Support\Contenu::get('accueil.hero_sous_titre') }}</h2>
            {{-- riche() : escape tout puis ne réintroduit que <strong>, à partir
                 des **astérisques** saisies dans l'admin. Cf. Contenu::riche(). --}}
            <p class="accroche">{!! \App\Support\Contenu::riche('accueil.hero_accroche') !!}</p>
            <div class="actions">
                <a class="bouton b-rouge" href="{{ route('contact') }}">{{ \App\Support\Contenu::get('accueil.hero_cta1') }}<i class="fl dessin f-fleche" aria-hidden="true"></i></a>
                <a class="bouton b-ligne" href="{{ route('references') }}">{{ \App\Support\Contenu::get('accueil.hero_cta2') }}</a>
            </div>
        </div>
        <div class="hero-visuel">
            <img class="photo" src="{{ \App\Support\Contenu::urlImage(\App\Support\Contenu::get('accueil.hero_image')) }}" alt="Perroquet écarlate — symbole de la marque CIBLE" style="object-position:52% 42%">
        </div>
    </div>

    {{-- Bande 5 couleurs retirée 2026-08-04 — les couleurs étaient
         redondantes avec le ticker qui suit immédiatement + section
         territoires plus bas. Trop bruyant visuellement en début de page. --}}
</section>

{{-- ═══ TICKER ═══ --}}
<div class="ticker" aria-hidden="true" style="margin-top:clamp(24px,4vw,44px)">
    {{-- Piste dupliquée 2× : l'animation translateX(-50%) exige que la
         seconde moitié soit identique à la première pour boucler sans
         saut visible. Toute modification doit être faite dans les 2 blocs. --}}
    @php
        $ticker = \App\Support\Contenu::lignes('accueil.ticker');
        // Les couleurs tournent en boucle : l'admin peut ajouter ou retirer
        // une mention sans qu'aucune pastille se retrouve sans fond.
        $tons = [
            'background:var(--rouge)',
            'background:var(--jaune);color:#111',
            'background:var(--vert)',
            'background:var(--violet)',
            'background:var(--bleu)',
            'background:var(--noir)',
        ];
    @endphp
    <div class="piste">
        @for($passe = 0; $passe < 2; $passe++)
            @foreach($ticker as $i => $mention)
                {{-- .num = chiffres à chasse fixe : appliqué dès qu'il y a un chiffre --}}
                <b style="{{ $tons[$i % count($tons)] }}"@if(preg_match('/\d/', $mention)) class="num"@endif>{{ $mention }}</b>
            @endforeach
        @endfor
    </div>
</div>

{{-- ═══ MARQUE — image parrots + bloc violet (v3 2026-08-04) ═══
     Retour au layout demandé + améliorations : halo doré autour de
     la photo, pastilles numérotées 01/02/03 devant chaque h3,
     séparateurs entre articles, signature finale imposante. --}}
<section class="marque" id="marque">
    <div class="marque-img rev">
        <div class="marque-img-inner">
            <img class="photo" src="{{ \App\Support\Contenu::urlImage(\App\Support\Contenu::get('accueil.marque_image')) }}" alt="Perroquet écarlate — identité visuelle CIBLE" style="object-position:62% 38%">
        </div>
    </div>
    <div class="marque-txt">
        <div class="couche">
            <span class="fleche dessin f-plume" style="--c:#fff;--op:.10;top:8%;right:8%;width:140px;--r:16deg;--dur:26s;--del:.2s"></span>
            <span class="fleche dessin f-fleche" style="--c:#fff;--op:.10;bottom:6%;left:6%;width:100px;--r:-14deg;--dur:22s;--del:.4s"></span>
        </div>
        <div class="rev">
            <span class="sur">{{ \App\Support\Contenu::get('accueil.marque_surtitre') }}</span>
            <h2 class="t1" style="margin-top:12px">{{ \App\Support\Contenu::get('accueil.marque_titre') }}</h2>
            <p class="intro-p">{{ \App\Support\Contenu::get('accueil.marque_intro') }}</p>
            <div class="recit">
                <article>
                    <h3 data-num="01">{{ \App\Support\Contenu::get('accueil.marque_1_titre') }}</h3>
                    <p>{{ \App\Support\Contenu::get('accueil.marque_1_texte') }}</p>
                </article>
                <article>
                    <h3 data-num="02">{{ \App\Support\Contenu::get('accueil.marque_2_titre') }}</h3>
                    <p>{{ \App\Support\Contenu::get('accueil.marque_2_texte') }}</p>
                </article>
                <article>
                    <h3 data-num="03">{{ \App\Support\Contenu::get('accueil.marque_3_titre') }}</h3>
                    <p>{{ \App\Support\Contenu::get('accueil.marque_3_texte') }}</p>
                </article>
            </div>
            <div class="marque-signature">
                {!! \App\Support\Contenu::riche('accueil.marque_signature') !!}
            </div>
        </div>
    </div>
</section>

{{-- ═══ TERRITOIRES (onglets interactifs) ═══ --}}
<section class="territoires" id="territoires">
    <div class="couche">
        <span class="fleche dessin f-fleche" style="--c:var(--gris);--op:1;top:6%;right:6%;width:150px;--r:12deg;--dur:28s;--del:.2s"></span>
    </div>
    <div class="entete rev">
        <span class="sur">{{ \App\Support\Contenu::get('accueil.terr_surtitre') }}</span>
        <h2 class="t1">{{ \App\Support\Contenu::get('accueil.terr_titre') }}</h2>
        <p>{{ \App\Support\Contenu::get('accueil.terr_intro') }}</p>
    </div>

    {{-- Consigne explicite : sans elle, rien ne disait au visiteur que les
         5 onglets ouvrent des contenus différents. La flèche de marque
         pointe vers eux et oscille doucement pour attirer l'œil. --}}
    <p class="onglets-aide">
        <span class="dessin f-fleche" aria-hidden="true"></span>
        {{ \App\Support\Contenu::get('accueil.terr_aide') }}
    </p>

    {{-- Les id (o-rue / t-rue…) sont conservés : ils pilotent les couleurs
         de scène en CSS et la table `teintes` du JS en bas de page. Seuls
         les libellés changent (doc textes 2026-08-10). --}}
    <div class="onglets" role="tablist" aria-label="Territoires de visibilité">
        <button class="onglet" id="o-rue"       role="tab" aria-selected="true"  aria-controls="t-rue"><span class="forme"></span>{{ \App\Support\Contenu::get('accueil.terr1_onglet') }}</button>
        <button class="onglet" id="o-mouvement" role="tab" aria-selected="false" aria-controls="t-mouvement"><span class="forme"></span>{{ \App\Support\Contenu::get('accueil.terr2_onglet') }}</button>
        <button class="onglet" id="o-ecran"     role="tab" aria-selected="false" aria-controls="t-ecran"><span class="forme"></span>{{ \App\Support\Contenu::get('accueil.terr3_onglet') }}</button>
        <button class="onglet" id="o-digital"   role="tab" aria-selected="false" aria-controls="t-digital"><span class="forme"></span>{{ \App\Support\Contenu::get('accueil.terr4_onglet') }}</button>
        <button class="onglet" id="o-terrain"   role="tab" aria-selected="false" aria-controls="t-terrain"><span class="forme"></span>{{ \App\Support\Contenu::get('accueil.terr5_onglet') }}</button>
    </div>

    <div class="scene" id="scene" style="background:var(--rouge)">
        <div class="balayage" aria-hidden="true">
            <span class="dessin f-fleche" style="top:4%;left:0;width:190px;--r:10deg"></span>
            <span class="dessin f-fleche" style="top:44%;left:0;width:120px;--r:-16deg"></span>
            <span class="dessin f-plume" style="bottom:2%;left:0;width:150px;--r:22deg"></span>
        </div>

        <div class="pan" id="t-rue" role="tabpanel" aria-labelledby="o-rue">
            <div>
                {{-- Le "+" est hors du span animé : anime() écrase le
                     textContent de sa cible, il l'effacerait sinon. --}}
                <div class="grosnum num"><span aria-hidden="true">+</span><span data-cible="{{ \App\Support\Contenu::get('chiffres.panneaux') }}">0</span></div>
                <span class="sur">{{ \App\Support\Contenu::get('accueil.terr1_surtitre') }}</span>
                <h3>{{ \App\Support\Contenu::get('accueil.terr1_titre') }}</h3>
                <p>{{ \App\Support\Contenu::get('accueil.terr1_texte') }}</p>
                <ul class="tags">@foreach(\App\Support\Contenu::lignes('accueil.terr1_tags') as $tag)<li>{{ $tag }}</li>@endforeach</ul>
                <p class="preuve">{{ \App\Support\Contenu::get('accueil.terr1_preuve') }}</p>
            </div>
            <div class="pan-visuel"><img class="photo" src="{{ \App\Support\Contenu::urlImage(\App\Support\Contenu::get('accueil.terr1_image')) }}" alt="Panneau grand format CIBLE"></div>
        </div>

        <div class="pan" id="t-mouvement" role="tabpanel" aria-labelledby="o-mouvement" hidden>
            <div>
                <div class="grosnum num" data-cible="{{ \App\Support\Contenu::get('chiffres.communes') }}">0</div>
                <span class="sur">{{ \App\Support\Contenu::get('accueil.terr2_surtitre') }}</span>
                <h3>{{ \App\Support\Contenu::get('accueil.terr2_titre') }}</h3>
                <p>{{ \App\Support\Contenu::get('accueil.terr2_texte') }}</p>
                <ul class="tags">@foreach(\App\Support\Contenu::lignes('accueil.terr2_tags') as $tag)<li>{{ $tag }}</li>@endforeach</ul>
                <p class="preuve">{{ \App\Support\Contenu::get('accueil.terr2_preuve') }}</p>
            </div>
            <div class="pan-visuel"><img class="photo" src="{{ \App\Support\Contenu::urlImage(\App\Support\Contenu::get('accueil.terr2_image')) }}" alt="Camion publicitaire CIBLE"></div>
        </div>

        <div class="pan" id="t-ecran" role="tabpanel" aria-labelledby="o-ecran" hidden>
            <div>
                <div class="grosnum num" data-brut="{{ \App\Support\Contenu::get('accueil.terr3_chiffre') }}">Studio</div>
                <span class="sur">{{ \App\Support\Contenu::get('accueil.terr3_surtitre') }}</span>
                <h3>{{ \App\Support\Contenu::get('accueil.terr3_titre') }}</h3>
                <p>{{ \App\Support\Contenu::get('accueil.terr3_texte') }}</p>
                <ul class="tags">@foreach(\App\Support\Contenu::lignes('accueil.terr3_tags') as $tag)<li>{{ $tag }}</li>@endforeach</ul>
                <p class="preuve">{{ \App\Support\Contenu::get('accueil.terr3_preuve') }}</p>
            </div>
            <div class="pan-visuel"><img class="photo" src="{{ \App\Support\Contenu::urlImage(\App\Support\Contenu::get('accueil.terr3_image')) }}" alt="Extrait de film institutionnel"></div>
        </div>

        <div class="pan" id="t-digital" role="tabpanel" aria-labelledby="o-digital" hidden>
            <div>
                <div class="grosnum num" data-brut="{{ \App\Support\Contenu::get('accueil.terr4_chiffre') }}">24/7</div>
                <span class="sur">{{ \App\Support\Contenu::get('accueil.terr4_surtitre') }}</span>
                <h3>{{ \App\Support\Contenu::get('accueil.terr4_titre') }}</h3>
                <p>{{ \App\Support\Contenu::get('accueil.terr4_texte') }}</p>
                <ul class="tags">@foreach(\App\Support\Contenu::lignes('accueil.terr4_tags') as $tag)<li>{{ $tag }}</li>@endforeach</ul>
                <p class="preuve">{{ \App\Support\Contenu::get('accueil.terr4_preuve') }}</p>
            </div>
            <div class="pan-visuel"><img class="photo" src="{{ \App\Support\Contenu::urlImage(\App\Support\Contenu::get('accueil.terr4_image')) }}" alt="Campagne social media CIBLE"></div>
        </div>

        <div class="pan" id="t-terrain" role="tabpanel" aria-labelledby="o-terrain" hidden>
            <div>
                <div class="grosnum num" data-brut="{{ \App\Support\Contenu::get('accueil.terr5_chiffre') }}">Face à face</div>
                <span class="sur">{{ \App\Support\Contenu::get('accueil.terr5_surtitre') }}</span>
                <h3>{{ \App\Support\Contenu::get('accueil.terr5_titre') }}</h3>
                <p>{{ \App\Support\Contenu::get('accueil.terr5_texte') }}</p>
                <ul class="tags">@foreach(\App\Support\Contenu::lignes('accueil.terr5_tags') as $tag)<li>{{ $tag }}</li>@endforeach</ul>
                <p class="preuve">{{ \App\Support\Contenu::get('accueil.terr5_preuve') }}</p>
            </div>
            <div class="pan-visuel"><img class="photo" src="{{ \App\Support\Contenu::urlImage(\App\Support\Contenu::get('accueil.terr5_image')) }}" alt="Activation street marketing CIBLE"></div>
        </div>
    </div>
</section>

{{-- ═══ MISSION ═══ --}}
<section class="mission rev">
    {{-- Plumes de marque en fond, très basse opacité : la section était le
         seul bloc de la page sans aucun signe d'identité. Décoratif pur,
         donc aria-hidden et hors du flux de lecture. --}}
    <span class="dessin f-plume" aria-hidden="true" style="--c:var(--violet);opacity:.10;width:150px;top:-30px;left:4%;transform:rotate(18deg)"></span>
    <span class="dessin f-plume" aria-hidden="true" style="--c:var(--rouge);opacity:.09;width:110px;bottom:-24px;right:8%;transform:rotate(-24deg)"></span>
    <span class="dessin f-fleche" aria-hidden="true" style="--c:var(--bleu);opacity:.12;width:120px;top:22%;right:3%;transform:rotate(-8deg)"></span>
    <p>{{ \App\Support\Contenu::get('accueil.mission_texte') }}</p>
    <p class="fort">{{ \App\Support\Contenu::get('accueil.mission_fort') }}</p>
</section>

{{-- ═══ RÉALISATIONS ═══ --}}
<section class="travaux" id="travaux">
    <div class="couche">
        <span class="fleche dessin f-fleche" style="--c:var(--gris);--op:1;bottom:8%;left:-2%;width:170px;--r:-8deg;--dur:30s;--del:.3s"></span>
    </div>
    <div class="travaux-tete rev">
        <div>
            <span class="sur">{{ \App\Support\Contenu::get('accueil.travaux_surtitre') }}</span>
            <h2 class="t1" style="margin-top:12px">{{ \App\Support\Contenu::get('accueil.travaux_titre') }}</h2>
        </div>
        <a class="bouton b-ligne" href="{{ route('references') }}">{{ \App\Support\Contenu::get('accueil.travaux_cta') }}<i class="fl dessin f-fleche" aria-hidden="true"></i></a>
    </div>
    {{-- Cartes alimentées par CibleController::projets() — source unique
         partagée avec /references et les pages détail, pour qu'un libellé
         ne puisse plus diverger entre les trois emplacements.
         Les catégories courtes du doc (HOME.TRAVAUX.Cx.CAT) sont conservées
         ici ; les pages détail affichent la version longue (REF.Cx.CAT). --}}
    <div class="grille rev">
        @php
            // Libellés courts propres à l'accueil (HOME.TRAVAUX.Cx.*) —
            // les pages détail utilisent les versions longues (REF.Cx.*).
            $court = [
                'orange'    => ['Orange',      'Brand experience'],
                'cofina'    => ['Cofina',      'Film institutionnel'],
                'snedai'    => ['Snedai',      'Stratégie 360°'],
                'sgs-sicta' => ['SGS · SICTA', 'Création & réseaux sociaux'],
                'ifg'       => ['IFG',         'Stand expérientiel'],
                'sigfu'     => ['SIGFU',       'Design & architecture'],
            ];
        @endphp
        @foreach($projets as $slug => $p)
            @php [$nom, $cat] = $court[$slug]; @endphp
            <article class="carte" style="--c:{{ $p['couleur'] }}">
                <a href="{{ route('realisation', $slug) }}">
                    <div class="vign"><img class="photo" src="{{ \App\Support\Contenu::urlImage($p['image']) }}" alt="{{ $nom }} — {{ $cat }}"></div>
                    <h4>{{ $nom }}</h4>
                    <span><i class="pastille"></i>{{ $cat }}</span>
                </a>
            </article>
        @endforeach
    </div>
</section>

{{-- ═══ RÉSEAU (bleu) ═══ --}}
<section class="reseau" id="reseau">
    <div class="couche">
        <span class="fleche dessin f-fleche" style="--c:#fff;--op:.13;top:4%;left:34%;width:150px;--r:22deg;--dur:25s;--del:.2s"></span>
        <span class="fleche dessin f-plume" style="--c:#fff;--op:.12;bottom:6%;right:4%;width:120px;--r:-12deg;--dur:29s;--del:.5s"></span>
    </div>
    <div class="reseau-grille">
        <div class="rev">
            <span class="sur" style="opacity:.85">{{ \App\Support\Contenu::get('accueil.reseau_surtitre') }}</span>
            <h2 class="t1" style="margin-top:14px">{{ \App\Support\Contenu::get('accueil.reseau_titre') }}</h2>
            <p>{{ \App\Support\Contenu::get('accueil.reseau_texte') }}</p>
            {{-- Répartition 180/184 retirée 2026-08-10 : leur somme (364)
                 contredisait le volume global désormais annoncé en "+400".
                 On ne communique plus qu'un chiffre de parc, arrondi. --}}
            <div class="stats">
                <div><div class="v num"><span aria-hidden="true">+</span><span data-cible="{{ \App\Support\Contenu::get('chiffres.panneaux') }}">0</span></div><div class="l">Panneaux en exploitation</div></div>
                <div><div class="v num" data-cible="{{ \App\Support\Contenu::get('chiffres.communes') }}">0</div><div class="l">Communes et villes<br>couvertes</div></div>
            </div>
            <a class="bouton b-blanc" style="margin-top:34px" href="{{ route('reseau') }}">{{ \App\Support\Contenu::get('accueil.reseau_cta') }}<i class="fl dessin f-fleche" aria-hidden="true"></i></a>
        </div>
        <div class="rev">
            <div class="carte-slot"><img class="photo" src="{{ \App\Support\Contenu::urlImage(\App\Support\Contenu::get('accueil.reseau_image')) }}" alt="Réseau CIBLE — Abidjan by night"></div>
            <p class="note">Réseau détaillé et carte interactive sur la page <a href="{{ route('reseau') }}" style="text-decoration:underline">Notre réseau</a>.</p>
        </div>
    </div>
</section>

{{-- ═══ DISTINCTIONS ═══ --}}
<section class="dist-sec rev">
    <span class="sur">Reconnaissances officielles</span>
    <h2 class="t2" style="margin-top:12px">Trois distinctions de l'État ivoirien.</h2>
    <div class="dist-grille">
        <div class="dist" style="--c:var(--jaune)"><div class="an num">2016</div><h4>2<sup>e</sup> prix du meilleur publicitaire</h4><p>Distinction professionnelle du secteur de la publicité ivoirienne.</p></div>
        <div class="dist" style="--c:var(--vert)"><div class="an num">2019</div><h4>Chevalier de l'Ordre du Mérite de la Communication</h4><p>Reconnaissance de la contribution à la structuration du métier.</p></div>
        <div class="dist" style="--c:var(--violet)"><div class="an num">2020</div><h4>Officier de l'Ordre du Mérite National</h4><p>Distinction républicaine pour services rendus au pays.</p></div>
    </div>
</section>

{{-- ═══ CONTACT (jaune + motif plumes) ═══ --}}
<section class="contact-home" id="contact">
    <div class="motif" aria-hidden="true"></div>
    <div class="contact-inner rev">
        <h2 class="t1">{{ \App\Support\Contenu::get('accueil.contact_titre') }}</h2>
        <p>{{ \App\Support\Contenu::get('accueil.contact_texte') }}</p>
        <a class="bouton b-rouge" style="margin-top:28px" href="{{ route('contact') }}">{{ \App\Support\Contenu::get('accueil.contact_cta') }}<i class="fl dessin f-fleche" aria-hidden="true"></i></a>
        <div class="coord">
            <a href="tel:+2250700780628" class="num">+225 07 00 78 06 28</a>
            <a href="mailto:commercial@cible-ci.com">commercial@cible-ci.com</a>
            <a href="{{ route('contact') }}">Formulaire de contact</a>
        </div>
    </div>
</section>

@endsection

@push('page-js')
<script>
(function(){
    const doux = matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ─── territoires : couleur de scène + balayage flèches + glissement contenu ───
    const teintes = {'t-rue':'--rouge','t-mouvement':'--jaune','t-ecran':'--vert','t-digital':'--bleu','t-terrain':'--violet'};
    const scene = document.getElementById('scene');
    const onglets = [...document.querySelectorAll('.onglet')];
    function anime(el){
        if(el.dataset.brut){el.textContent=el.dataset.brut;return;}
        const fin=parseInt(el.dataset.cible,10);
        if(doux){el.textContent=fin;return;}
        const t0=performance.now(),d=1150;
        const pas=t=>{const p=Math.min((t-t0)/d,1);el.textContent=Math.round(fin*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(pas);};
        requestAnimationFrame(pas);
    }
    function activer(i,premier){
        onglets.forEach((o,j)=>{
            const a=i===j, id=o.getAttribute('aria-controls'), p=document.getElementById(id);
            o.setAttribute('aria-selected',a); o.tabIndex=a?0:-1; p.hidden=!a;
            if(a){
                scene.style.background=`var(${teintes[id]})`;
                p.querySelectorAll('[data-cible],[data-brut]').forEach(anime);
                if(!doux){
                    p.classList.remove('entre'); void p.offsetWidth; p.classList.add('entre');
                    if(!premier){scene.classList.remove('switch'); void scene.offsetWidth; scene.classList.add('switch');}
                }
            }
        });
    }
    onglets.forEach((o,i)=>{
        o.addEventListener('click',()=>activer(i));
        o.addEventListener('keydown',e=>{
            if(e.key!=='ArrowRight'&&e.key!=='ArrowLeft')return;
            e.preventDefault();
            const n=(i+(e.key==='ArrowRight'?1:-1)+onglets.length)%onglets.length;
            activer(n); onglets[n].focus();
        });
    });
    if(onglets.length) activer(0,true);

    // ─── parallaxe légère des flèches du héro ───
    if(!doux){
        const couches=[...document.querySelectorAll('[data-parallaxe] .fleche')];
        let tic=false;
        addEventListener('scroll',()=>{
            if(tic)return; tic=true;
            requestAnimationFrame(()=>{
                const y=scrollY;
                couches.forEach((el,k)=>{el.style.marginTop=(y*(0.05+k*0.028)*-1).toFixed(1)+'px';});
                tic=false;
            });
        },{passive:true});
    }
})();
</script>
@endpush
