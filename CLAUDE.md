# CLAUDE.md — Site vitrine CIBLE CI

> ⚠️ Ce fichier DOIT être relu au début de CHAQUE tâche, même si l'utilisateur ne le mentionne pas. Ces règles ne sont pas optionnelles.

---

## 🎯 CE QUE TU DOIS SAVOIR EN PREMIER

### Nature du projet

- **Site vitrine public** de CIBLE, régie publicitaire ivoirienne fondée en 1994.
- **Laravel 13 + Blade + SQLite + Leaflet** — statique, sans base de données réelle (SQLite juste pour sessions/cache Laravel).
- **6 pages** : accueil, qui-sommes-nous, services, réseau, références, contact — **+ 6 pages détail de réalisation** `/references/{slug}` (2026-08-10).
- **1 endpoint JSON** `/api/reseau-map` alimenté par un fichier statique `public/data/reseau-map.json` (31 communes + GPS).
- **1 formulaire /devis** → mail à `commercial@cible-ci.com` (rate-limited 5/10min, **accepte 4 pièces jointes** depuis 2026-08-10).

### 🚨 Règle N°1 absolue — CIBLE ≠ PANORA

Ce projet est **totalement indépendant** de Panora (le SaaS interne CIBLE).

- Aucune référence à Panora dans le code, la config, ou l'UI publique.
- Aucune dépendance externe (BDD Panora, API Panora, credentials Panora).
- Si Panora tombe, ce site continue de fonctionner. Si ce site tombe, Panora continue de fonctionner.
- Le site vitrine doit rester **commercialisable** — la marque CIBLE est propriétaire ici, pas Panora.

**Concrètement** : ne jamais faire d'appel HTTP vers Panora, ne jamais partager une BDD, ne jamais copier une classe Panora ici (recopier le code au besoin, ne pas linker).

---

## 📁 Structure du projet

```
c:\wamp64\www\cible-site\
├── app/
│   ├── Http/Controllers/CibleController.php   ← unique controller (6 routes + submitDevis + mapData)
│   └── Mail/CibleContactMail.php              ← mailable formulaire devis
├── config/
│   ├── mail.php                                ← clé `cible_devis_to` = destinataire /devis
│   └── services.php                            ← clé `cible.ga_id` = Google Analytics 4
├── database/database.sqlite                    ← fichier SQLite (sessions/cache seulement)
├── docs/
│   └── SITE INTERNET CIBLE_TEXTES.docx         ← 📝 SOURCE UNIQUE pour tous les textes du site
├── public/
│   └── data/reseau-map.json                    ← 31 communes + coordonnées GPS pour Leaflet
├── resources/
│   └── views/
│       ├── _layout.blade.php                   ← layout unique (nav + footer + palette CSS + fonts)
│       ├── home.blade.php
│       ├── qui-sommes-nous.blade.php
│       ├── services.blade.php
│       ├── reseau.blade.php                    ← Leaflet map + liste communes
│       ├── references.blade.php                ← grille filtrable (JS) + logos clients
│       ├── realisation.blade.php               ← détail d'une réalisation (6 slugs)
│       ├── contact.blade.php                   ← mini-brief 7 blocs + upload
│       └── emails/cible/devis.blade.php        ← template mail formulaire devis
├── lang/fr/validation.php                      ← messages de validation FR (APP_LOCALE=fr)
├── routes/web.php                              ← 9 routes total
├── Dockerfile                                  ← image lightweight (pas MySQL / pas Redis / pas migrate)
└── .env.example                                ← config prod-ready (SQLite, file cache, GA optional)
```

**Fichiers à modifier 90% du temps** : `resources/views/*.blade.php`, `resources/views/_layout.blade.php`, `public/data/reseau-map.json`.

---

## 🎨 Design system (verrouillé)

### Palette 5 couleurs (dans `_layout.blade.php` `:root`)

| Nom | Hex | Usage |
|---|---|---|
| `--rouge` | `#E20613` | Accent principal, CTA, focus |
| `--jaune` | `#FAB80B` | Highlights, illustrations |
| `--vert` | `#3AA835` | Distinctions, positif |
| `--bleu` | `#3F7FC0` | Détails |
| `--violet` | `#81358A` | Détails |
| `--gris` | `#E6E6E6` | Fonds sections |
| `--noir` | `#111111` | Texte principal |
| `--blanc` | `#FFFFFF` | Fonds page |

**Ne PAS ajouter de couleurs supplémentaires** sans validation user.

### Fonts (Google Fonts, chargées dans `_layout`)

- **Poppins** (400/500/700/800/900) — titres via `var(--titre)`
- **Nunito** (400/600/700) — corps via `var(--corps)`

### Échelle typographique

- `.t1` : `clamp(32px, 4.8vw, 64px)` — titre section (line-height 1.05)
- `.t2` : `clamp(24px, 3.5vw, 40px)` — sous-titre
- Corps : 17px, line-height 1.6

⚠ **Historique 2026-08-04** : les tailles ont déjà été réduites 2× après plaintes sur superposition Poppins 900. Ne pas augmenter sans tester sur mobile 360px.

### Padding responsive

- `var(--pad)` = `clamp(20px, 5vw, 84px)` — utilisé partout pour marges section

---

## ✍️ Direction éditoriale

> **Slogan** : *"Vous visez juste"*
>
> **Ton** : autorité tranquille + preuve terrain. Pas de superlatifs marketeux. Chiffres précis, faits datés, langage direct.

### Chiffres officiels à respecter (jamais improviser)

| Fait | Valeur |
|---|---|
| Année fondation | **1994** (30 ans en 2024) |
| Distinctions État | **3** (2016, 2019, 2020) |
| Panneaux en propre | **+400** (arrondi — voir ci-dessous) |
| Communes couvertes | **31** |
| Adresse | Rue des Ambassadeurs, Riviera M'Badon, 10 BP 1029, Abidjan |
| Téléphone mobile | +225 07 00 78 06 28 |
| Téléphone fixe | +225 27 22 20 80 08 |
| Email commercial | `commercial@cible-ci.com` |
| Email secrétariat | `secretariat@cible-ci.com` |
| Email studio | `studio@cible-ci.com` |

### ⚠️ Panneaux : « +400 », jamais un chiffre exact (décision 2026-08-10)

Le parc était communiqué à **364** (180 Abidjan + 184 intérieur). L'utilisateur a
décidé de ne plus publier de chiffre exact : le site affiche **« +400 panneaux »**
partout, et **plus aucune répartition chiffrée par zone**.

Concrètement :
- Ne jamais réintroduire 364, 180 ou 184 dans une vue.
- La carte Leaflet n'affiche plus le nombre de panneaux par commune (les `total`
  restent dans `reseau-map.json` pour un usage interne, mais ne sont pas rendus).
- Les compteurs animés utilisent `data-cible="400"` avec un `+` dans un span
  **séparé** — `anime()` écrase le `textContent` de sa cible et effacerait le `+`.

### Distinctions officielles (à ne pas paraphraser)

| Année | Distinction |
|---|---|
| 2016 | 2ᵉ prix du meilleur publicitaire |
| 2019 | Chevalier de l'Ordre du Mérite de la Communication |
| 2020 | Officier de l'Ordre du Mérite National |

### Aucune donnée personnelle sur le site

- Pas de photo staff, pas de nom nominatif, pas de bio personnelle.
- Le site représente **CIBLE l'entreprise**, pas ses individus.

---

## 📝 Modifications textuelles — SOURCE UNIQUE

**Toute modification de texte** (titre, paragraphe, CTA, libellé) doit venir de `docs/SITE INTERNET CIBLE_TEXTES.docx`.

Workflow :
1. L'utilisateur t'envoie un fichier Word avec les nouveaux textes structurés par section (ID_SECTION → texte).
2. Tu extrais le texte du .docx (⚠ l'outil `Read` ne lit PAS les .docx : dézipper
   et parser `word/document.xml`, cf. la commande PowerShell utilisée le 2026-08-10).
3. Tu identifies dans les vues Blade quels textes correspondent aux IDs.
4. Tu proposes la LISTE des modifications avant de coder (règle N°1 CIBLE).
5. Après validation utilisateur, tu appliques toutes les modifs d'un coup.

**Ne jamais inventer de texte** — si un ID n'est pas clair, demande.

⚠️ Les documents de textes contiennent des doublons et des coquilles (IDs répétés
avec deux valeurs différentes, numérotation qui saute). Les signaler et faire
trancher plutôt que choisir en silence.

---

## 🔧 Règles de travail

### RÈGLE N°1 — LISTER AVANT DE CODER

Comme sur Panora : pour toute modification (textuelle ou technique), tu réponds d'abord :

```
J'ai trouvé X endroits à modifier :

1. resources/views/home.blade.php (section HERO ligne 45)
   — Avant : "..."
   — Après : "..."
2. resources/views/qui-sommes-nous.blade.php (…)
   …

Je vais appliquer les X modifications. Confirme-moi de continuer.
```

Puis attends la validation avant de toucher le code.

### RÈGLE N°2 — LIRE AVANT D'ÉCRIRE

Avant de modifier une vue, la lire ENTIÈRE (ou au minimum la section touchée + le layout).
Les vues CIBLE ont beaucoup de CSS inline `@push('page-css')` — vérifier qu'un changement de HTML ne casse pas les styles.

### RÈGLE N°3 — MODIFICATIONS ADDITIVES

Pas de refacto "tant qu'à faire". Pas de changement de balise sans raison. Le design est verrouillé.

### RÈGLE N°4 — TESTER LOCALEMENT AVANT DE PUSH

Avant chaque commit, faire tourner :
```bash
php artisan view:cache          # compile toutes les vues Blade
php artisan config:cache         # vérifie la config
php artisan route:list --path=/  # liste les 8 routes
```

Si l'une échoue → ne pas commit.

Pour tester visuellement :
```bash
php artisan serve
# puis ouvrir http://localhost:8000
```

### RÈGLE N°5 — RESPONSIVE OBLIGATOIRE

Toute modif visuelle doit être testée mobile (360px) et desktop (1440px). CIBLE est consulté depuis Abidjan majoritairement en 3G/4G sur Android bas de gamme.

---

## 🚢 Git & Déploiement

### État actuel (2026-08-10)

- **Repo git** : ✅ https://github.com/cible-studio/cible-site (privé)
- **Branches** : `main` (prod future) + `develop` (test actif)
- **Hébergement** : Coolify + Hetzner — **staging actif**, prod à venir
- **URL de test actuelle** : sur le domaine de dev Coolify (à confirmer avec l'utilisateur)
- **Domaine final** : `cible-ci.com` (DNS OVH) — migration à venir quand le site sera validé

### 🎯 Workflow git actuel — DEVELOP UNIQUEMENT

**Tant que le site n'est pas migré sur `cible-ci.com`** :

- ✅ **Tous les commits vont sur `develop`** — c'est la branche du lien de test
- ❌ **NE PAS commit sur `main`** — main est réservée pour la mise en prod finale
- ❌ **NE PAS merger develop → main** sans instruction explicite utilisateur

Commande type après une modification :

```bash
git add <fichiers>
git commit -m "<message>"
git push origin develop
```

Coolify redéploie automatiquement le staging à chaque push sur `develop`.

### 🚦 Basculement futur vers prod (cible-ci.com)

Quand l'utilisateur dira *"on migre sur cible-ci.com"* :

1. Merger develop → main via `git merge --no-ff develop -m "..."`
2. Push main → Coolify prod build l'image
3. Basculer les DNS A/AAAA OVH vers l'IP Hetzner de la prod (⚠ voir avertissement DNS ci-dessous)
4. Vérifier le TLS Let's Encrypt (auto Coolify)
5. Tester `https://cible-ci.com` en profondeur

D'ici là, **rien ne touche à main ni aux DNS**.

### ⚠️ OVH DNS — INTERDICTION ABSOLUE

Ne JAMAIS toucher aux enregistrements **MX** et **TXT (SPF/DKIM)** du domaine `cible-ci.com` en changeant les DNS. Sinon on casse les mails de toute l'équipe CIBLE (`@cible-ci.com`).

Seuls les enregistrements **A** (pointant vers l'IP Hetzner) et **AAAA** peuvent être modifiés pour la mise en prod du site — et uniquement au moment du basculement final.

### 🔐 Authentification GitHub

- Compte GitHub push : **Quenum19** (a le droit `push` sur cible-studio/cible-site, comme sur cible-studio/panora)
- Repo owner : **cible-studio** (compte user CIBLE — pas une organisation ; seul cible-studio peut CRÉER des repos dessus, Quenum19 peut seulement push sur ceux existants)
- Historique 2026-08-10 : repo initialement créé sous Quenum19/cible-site puis transféré à cible-studio/cible-site après acceptation du transfer par cible-studio.
- `gh` CLI est installé et authentifié en tant que Quenum19 — utilisable pour créer PR / issues sur le repo cible-studio/cible-site (le token Quenum19 a les droits `push` requis)

### 📝 Format des commits

Convention alignée sur Panora :

```
type(scope): sujet court (< 72 char)

Corps optionnel expliquant le POURQUOI, pas le QUOI (le diff dit le quoi).
Références issues éventuelles.

Co-Authored-By: Claude Opus 4.7 <noreply@anthropic.com>
```

Types courants : `feat`, `fix`, `chore`, `ui`, `docs`, `refactor`.

Scopes courants : `home`, `services`, `reseau`, `contact`, `layout`, `map`, `mail`, `seo`, `deploy`.

---

## 🔑 Variables d'environnement clés

Dans `.env` (dev) et Coolify (prod) :

```
APP_NAME="CIBLE CI"
APP_ENV=production
APP_URL=https://cible-ci.com
APP_LOCALE=fr

DB_CONNECTION=sqlite

MAIL_MAILER=smtp                     # log en dev, smtp en prod
MAIL_HOST=…                           # à configurer selon prestataire
MAIL_SCHEME=smtp                      # ⚠ PAS MAIL_ENCRYPTION (ignoré en Laravel 11+)
MAIL_FROM_ADDRESS="site@cible-ci.com"
MAIL_FROM_NAME="Site CIBLE"

CIBLE_DEVIS_TO=commercial@cible-ci.com   # destinataire formulaire /devis

CIBLE_GA_ID=                              # laissé vide en dev, G-XXXXXXXXXX en prod
```

Le GA n'est chargé côté frontend QUE si `CIBLE_GA_ID` est renseigné (opt-in).

---

## 🗺️ Fichier `public/data/reseau-map.json`

Structure attendue par la Leaflet map (`resources/views/reseau.blade.php`) :

```json
[
  {
    "commune": "Abobo",
    "city": "Abidjan",
    "region": "Abidjan",
    "lat": 5.4192,
    "lng": -4.0332,
    "total": 18
  }
]
```

⚠ C'est un **tableau plat**, pas un objet `{"communes": […]}` : ce fichier
décrivait une structure erronée (clés `name`/`count`/`notes`, jamais
utilisées) jusqu'au 2026-08-14. Le champ `total` n'est pas rendu — cf. la
décision de ne plus publier de répartition chiffrée par zone.

Pour éditer : ouvrir directement le fichier avec `Edit` ou `Write`. Pas de BDD, pas de migration.

**31 communes** doivent être présentes à tout moment (chiffre officiel affiché sur le site).

---

## 📋 Checklist début de session

Quand l'utilisateur t'invoque dans ce dossier :

- [ ] Lire ce `CLAUDE.md` complet
- [ ] Vérifier `git status` — la branche courante doit être `develop` (jamais `main`)
- [ ] Si tu es sur `main` → `git checkout develop` avant toute modification
- [ ] `git pull origin develop` pour récupérer les derniers changements avant de coder
- [ ] Si l'utilisateur mentionne un fichier Word (`.docx`), l'ouvrir avec `Read` avant de proposer quoi que ce soit
- [ ] Demander sur quelle(s) page(s) portent les modifs
- [ ] Suivre la RÈGLE N°1 : LISTER → attendre validation → coder → rapport final
- [ ] Commit + push sur **develop uniquement** (rappel : main = prod, réservé)

## 📤 Rapport final obligatoire

À la fin de chaque mission (comme sur Panora), produire :

```
✅ MISSION TERMINÉE — "<nom>"

Fichiers modifiés :
   ✅ resources/views/home.blade.php (section X)
   ✅ resources/views/services.blade.php (bloc Y)
   …

À vérifier manuellement par l'utilisateur :
   □ Ouvrir http://localhost:8000/ et vérifier la section X
   □ Ouvrir http://localhost:8000/services et vérifier le bloc Y
   □ Tester en 360px (mobile) ET 1440px (desktop)

Tests :
   ✅ php artisan view:cache — OK
   ✅ php artisan config:cache — OK

Git :
   ✅ Committé et pushé sur develop (branch de test)
   ✅ Coolify staging redéploiera automatiquement dans ~2min
```

---

## 🆘 Points de vigilance récurrents

- **Chiffres officiels** : 1994, 3, **+400**, 31 → ne jamais improviser
- **CTA unifié** : le libellé « Demander un devis » a été retiré du site
  (2026-08-10). Nav et bouton d'envoi = « Recevoir ma recommandation média » ;
  les CTA de page = « Parler de mon projet » / « Recevoir une recommandation média ».
- **Réalisations** : les 6 projets vivent dans `CibleController::projets()` —
  source unique pour l'accueil, `/references` et les pages détail. Ne pas
  dupliquer les libellés dans une vue.
- **Formulaire /contact** : les listes de choix viennent de
  `CibleController::formOptions()`. La validation contrôle les clés puis
  retraduit en libellés avant l'envoi du mail → le commercial ne reçoit
  jamais de slug. Ajouter une option = la déclarer là, nulle part ailleurs.
- **Uploads** : validés par `extensions:` et non `mimes:` (un .ai est un PDF
  déguisé, un .eps du PostScript → `mimes:` rejetait des fichiers légitimes).
  Rien n'est stocké : les fichiers partent en pièce jointe et disparaissent.
  ⚠ `post_max_size` (Dockerfile) borne le corps **entier** de la requête :
  il doit rester ≥ 4 × `DOC_MAX_KO`, sinon PHP jette le POST avant Laravel
  et le visiteur reçoit un 419 au lieu d'un message de validation.
- **Superposition Poppins 900** sur mobile — testé et corrigé 2026-08-04, ne pas ré-augmenter les tailles
- **Panora n'existe pas ici** → aucune référence
- **Contact studio** : `studio@cible-ci.com` (l'utilisateur)
- **Aucun compte client, aucune BDD relationnelle** — c'est une vitrine pure.
  Seule exception : l'espace d'administration `/admin`, compte unique en
  variables d'environnement (pas de table `users`). Il répond 404 tant que
  `CIBLE_ADMIN_EMAIL` / `CIBLE_ADMIN_HASH` ne sont pas renseignés.
- **Contenu éditable** : `config/admin-schema.php` décrit tous les champs
  modifiables sans développeur (6 pages, ~195 champs). Chaîne complète :

  ```
  défaut versionné (config/admin-schema.php)
      ↓ surchargé par
  storage/app/contenu/<page>.json  (volume persistant Coolify)
      ↓ lu par
  Contenu::get('page.champ')  dans la vue Blade
  ```

  Le volume n'est **jamais** la source de vérité : volume perdu ou vidé, le
  site réaffiche les défauts de git au lieu d'une page blanche. C'est ce qui
  rend l'admin acceptable sur un conteneur éphémère.

  Pour rendre un nouveau contenu éditable : ajouter une ligne dans le schéma,
  puis appeler `Contenu::get()` dans la vue. Formulaire, validation et
  enregistrement sont générés — ne rien écrire dans `AdminController`.

  ⚠ La valeur `defaut` du schéma doit correspondre **au mot près** au texte
  affiché : c'est elle qui s'affiche quand rien n'est surchargé.
  ⚠ Champs `liste` : une entrée par ligne, lus avec `Contenu::lignes()`.
  ⚠ Champs `image` : `Contenu::urlImage()` / `imageExiste()` — un visuel
  téléversé vit sur le volume (`visuel:nom.jpg`), pas dans `public/`, qui est
  reconstruit depuis git à chaque déploiement.
  ⚠ Emphase : le site n'accepte pas de HTML saisi en admin. Le texte entre
  `**doubles astérisques**` devient un `<strong>` via `Contenu::riche()`,
  affiché avec `{!! !!}`. Tout le reste est échappé.
- **Mail SMTP** : Laravel 11+ lit `MAIL_SCHEME`, jamais `MAIL_ENCRYPTION`.
  Avec Gmail/Workspace, le mot de passe du compte est refusé (`535-5.7.8`) :
  il faut un mot de passe d'application à 16 caractères, donc la validation
  en 2 étapes activée sur la boîte.
- **Logs en prod** : `LOG_CHANNEL=stack` écrit dans `storage/logs/laravel.log`
  *à l'intérieur* du conteneur — invisible dans l'onglet Logs de Coolify et
  perdu à chaque redéploiement. Passer à `stderr` pour diagnostiquer.

---

**En cas de doute → DEMANDER, ne pas inventer.**
