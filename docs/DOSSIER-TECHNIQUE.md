# Dossier technique — Site vitrine CIBLE CI

> **Document interne.** Il décrit l'infrastructure et les emplacements des
> accès. Aucun mot de passe n'y figure — voir §3. Ne pas diffuser hors de
> CIBLE et de son prestataire technique.

**Objet** : permettre à un développeur qui découvre le projet de le reprendre
sans support.
**Dernière mise à jour** : 14 août 2026.

---

## 1. Le projet en dix lignes

Site vitrine public de **CIBLE**, régie publicitaire ivoirienne fondée en 1994.
Six pages publiques, six pages de détail de réalisation, un formulaire de
demande de devis, une carte interactive du parc de panneaux, et un espace
d'administration qui rend tout le contenu modifiable sans développeur.

**Aucune base de données relationnelle.** SQLite n'est présent que pour le
cache et les sessions de Laravel. Les données du site vivent dans des fichiers
de configuration versionnés, surchargés par des fichiers JSON sur un volume
persistant.

### Règle absolue : CIBLE ≠ Panora

Ce projet est **totalement indépendant** de Panora, le SaaS interne de CIBLE.

- Aucune référence à Panora dans le code, la configuration ou l'interface
- Aucune dépendance : pas d'appel HTTP, pas de base partagée, pas de classe
  copiée avec un lien
- Si Panora tombe, ce site fonctionne. Si ce site tombe, Panora fonctionne

Cette séparation rend le site **commercialisable** : la marque CIBLE est
propriétaire ici, pas Panora. Si vous avez besoin d'une logique qui existe
dans Panora, **recopiez le code**, ne créez pas de lien.

---

## 2. Stack

| Composant | Version / choix | Pourquoi |
|---|---|---|
| PHP | 8.3 | |
| Laravel | 13.24 (`^13.8`) | |
| Moteur de vues | Blade | Pas de framework front, pas de build JS |
| Base de données | SQLite | Cache et sessions Laravel uniquement |
| Cartographie | Leaflet (CDN unpkg) + fonds CartoDB | Carte du parc de panneaux |
| Conteneur | `php:8.3-fpm-alpine` | Image légère : pas de MySQL, pas de Redis |
| Serveur HTTP | serveur intégré de PHP (`php -S`) | Trafic faible, pas de nginx à maintenir |
| Hébergement | Coolify sur Hetzner | |
| Reverse proxy | Traefik (fourni par Coolify) | Termine le TLS, Let's Encrypt automatique |

**Aucune dépendance de service externe au démarrage.** Pas de Redis, pas de
file d'attente, pas de migration au déploiement. Le conteneur démarre seul.

Dépendances Composer : `laravel/framework`, `laravel/tinker`. C'est tout.

---

## 3. Accès — où ils se trouvent

**Aucun secret n'est écrit dans ce document ni dans le dépôt.** Voici où les
récupérer.

| Accès | Emplacement | Détenteur |
|---|---|---|
| Dépôt de code | github.com/cible-studio/cible-site (privé) | compte `cible-studio` (propriétaire), `Quenum19` (droit push) |
| Console d'hébergement | Coolify, `http://178.105.43.137:8000` | CIBLE |
| Serveur | Hetzner, accès SSH | CIBLE |
| DNS | OVH, domaine `cible-ci.com` | CIBLE |
| Variables d'environnement (tous les secrets applicatifs) | Coolify → application → *Environment Variables* | CIBLE |
| Mot de passe de l'admin du site | hash uniquement, dans `CIBLE_ADMIN_HASH` | CIBLE |
| SMTP d'envoi des mails | `MAIL_USERNAME` / `MAIL_PASSWORD` dans Coolify | CIBLE |
| Clés Cloudflare Turnstile | `TURNSTILE_*` dans Coolify | dash.cloudflare.com |
| Google Analytics | `CIBLE_GA_ID` dans Coolify | CIBLE |

> `cible-studio` est un **compte utilisateur**, pas une organisation : lui seul
> peut créer un dépôt sous son nom. `Quenum19` peut pousser sur les dépôts
> existants.

### ⛔ Interdiction absolue — DNS OVH

Ne **jamais** toucher aux enregistrements **MX** et **TXT (SPF / DKIM)** du
domaine `cible-ci.com`. Ils portent la messagerie de toute l'équipe CIBLE
(`@cible-ci.com`). Les casser coupe les mails de l'entreprise entière.

Seuls les enregistrements **A** et **AAAA** peuvent être modifiés, et
uniquement lors d'un changement de serveur.

---

## 4. Arborescence

```
cible-site/
├── app/
│   ├── Console/Commands/
│   │   ├── AdminHash.php            # génère / vérifie le hash admin
│   │   └── AdminMotDePasse.php      # écrit le hash sur le volume
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CibleController.php  # les 6 pages + devis + carte + visuels
│   │   │   └── AdminController.php  # tout l'espace d'administration
│   │   └── Middleware/
│   │       ├── AdminProtege.php     # exige la session admin
│   │       └── SecurityHeaders.php  # CSP et en-têtes de sécurité
│   ├── Mail/CibleContactMail.php    # mail du formulaire de devis
│   └── Support/
│       ├── Schema.php               # lecture de config/admin-schema.php
│       ├── Contenu.php              # défauts + surcharges + helpers de vue
│       ├── AdminAuth.php            # compte unique, sans table users
│       ├── AntiSpam.php             # notation par signaux
│       ├── Turnstile.php            # vérification Cloudflare
│       └── Journal.php              # journalisation qui ne peut pas casser une page
├── config/
│   ├── admin-schema.php             # ★ 6 pages, 195 champs éditables
│   ├── contenu.php                  # défauts des sections hors schéma
│   ├── cible.php                    # identifiants de l'admin
│   ├── mail.php                     # clé cible_devis_to
│   └── services.php                 # ga_id, turnstile
├── docs/
│   ├── MANUEL-UTILISATION.md        # manuel destiné à CIBLE
│   ├── DOSSIER-TECHNIQUE.md         # ce document
│   └── SITE INTERNET CIBLE_TEXTES (1).docx   # source des textes
├── lang/fr/validation.php           # messages de validation en français
├── public/data/reseau-map.json      # 31 communes + coordonnées GPS
├── resources/views/
│   ├── _layout.blade.php            # layout public (palette, polices, nav, pied)
│   ├── home / services / reseau / qui-sommes-nous / references / contact / realisation
│   ├── components/decor.blade.php   # formes de marque décoratives
│   ├── admin/                       # coque + écrans d'administration
│   └── emails/cible/devis.blade.php
├── routes/web.php                   # 24 routes
├── Dockerfile
└── CLAUDE.md                        # règles de travail sur le projet
```

**Fichiers modifiés 90 % du temps** : les vues Blade, `config/admin-schema.php`,
`public/data/reseau-map.json`.

### `public/data/reseau-map.json`

Un **tableau plat de 31 entrées** — pas un objet `{"communes": [...]}`, contrairement
à ce qu'indiquait `CLAUDE.md` jusqu'au 14 août 2026.

```json
[
  { "commune": "Abobo", "city": "Abidjan", "region": "Abidjan",
    "lat": 5.4192, "lng": -4.0332, "total": 18 }
]
```

Servi par `/api/reseau-map` et consommé en `fetch` dans `reseau.blade.php`.
Les 31 entrées doivent être présentes à tout moment : le chiffre est affiché
sur le site. Le champ `total` reste dans le fichier pour un usage interne mais
**n'est pas rendu** — décision de ne plus publier de répartition chiffrée par
zone (§14). Édition directe du fichier, pas de base, pas de migration.

---

## 5. Le modèle de contenu — le cœur du projet

C'est le point à comprendre avant tout le reste.

```
config/admin-schema.php          défaut, versionné dans git
        ↓ surchargé par
storage/app/contenu/<page>.json  écrit par l'admin, sur le volume persistant
        ↓ lu dans la vue par
Contenu::get('page.champ')
```

**Le volume n'est jamais la source de vérité.** Volume perdu, vidé ou mal
monté : le site réaffiche les valeurs de git au lieu d'une page blanche. C'est
précisément ce qui rend un espace d'administration acceptable sur un conteneur
éphémère.

### Rendre un nouveau contenu éditable

1. Ajouter une ligne dans `config/admin-schema.php`
2. Appeler `Contenu::get('page.champ')` dans la vue

C'est tout. Formulaire, validation et enregistrement sont générés à partir du
schéma — **ne rien écrire dans `AdminController`**.

### Pièges

- La valeur `defaut` du schéma doit correspondre **au mot près** au texte
  affiché : c'est elle qui s'affiche quand rien n'est surchargé
- La clé de stockage est `page.champ` : déplacer un champ vers une autre page
  sans migrer la surcharge fait perdre la valeur saisie

### Helpers de vue

| Type de champ | Appel | Note |
|---|---|---|
| `texte`, `zone`, `nombre` | `Contenu::get('page.champ')` | échappé par Blade |
| `liste` | `Contenu::lignes('page.champ')` | une entrée par ligne, lignes vides ignorées |
| `image` | `Contenu::urlImage(...)` + `imageExiste(...)` | gère `public/` et le volume |
| emphase | `Contenu::riche('page.champ')` avec `{!! !!}` | `**texte**` → `<strong>`, tout le reste échappé |

Les visuels téléversés vivent sur le volume sous la référence `visuel:nom.jpg`
et sont servis par la route `/visuels/{nom}` — **jamais** dans `public/`, qui
est reconstruit depuis git à chaque déploiement.

### Sections hors schéma

`coordonnees`, `chiffres` et `realisations` ont une structure particulière :
leurs défauts sont dans `config/contenu.php` et leurs écrans d'administration
sont écrits à la main. Les six pages passent, elles, par le schéma.

---

## 6. Routes

**Public**

| Méthode | URL | Action |
|---|---|---|
| GET | `/` | accueil |
| GET | `/qui-sommes-nous` `/services` `/reseau` `/references` `/contact` | pages |
| GET | `/references/{slug}` | détail d'une réalisation (6 slugs, cf. `CibleController::projets()`) |
| POST | `/devis` | formulaire de contact — limité 5/10 min par IP **et** 40/h au total |
| GET | `/api/reseau-map` | JSON de la carte, limité 60/min |
| GET | `/visuels/{nom}` | visuel téléversé, servi depuis le volume |
| GET | `/up` | sonde de santé (Laravel) |

`/references/{slug}` est déclarée **après** `/references` : Laravel teste les
routes dans l'ordre, la route statique reste prioritaire.

**Administration** — préfixe `/admin`, toutes derrière `AdminProtege` sauf la
connexion.

| Méthode | URL | Action |
|---|---|---|
| GET/POST | `/admin/connexion` | connexion, limitée 6/10 min |
| GET | `/admin` | tableau de bord |
| GET/POST | `/admin/page/{cle}` | **écrans générés depuis le schéma** |
| GET/POST | `/admin/coordonnees` `/admin/chiffres` | sections fixes |
| GET/POST | `/admin/realisations[/{slug}]` | les 6 projets |
| POST | `/admin/reinitialiser/{section}` | retour au contenu de git |

---

## 7. Variables d'environnement

Voir `.env.example`, qui documente chaque bloc. Les points qui coûtent du temps :

```env
APP_ENV=production
APP_DEBUG=false          # ⚠ à true, le code source est public
APP_URL=https://www.cible-ci.com
APP_LOCALE=fr
APP_TIMEZONE=Africa/Abidjan

DB_CONNECTION=sqlite
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=stderr       # voir §11

MAIL_MAILER=smtp
MAIL_SCHEME=smtp         # ⚠ PAS MAIL_ENCRYPTION
CIBLE_DEVIS_TO=commercial@cible-ci.com

CIBLE_GA_ID=             # vide = pas de traceur chargé
TURNSTILE_SITE_KEY=      # vides = widget non rendu, site inchangé
TURNSTILE_SECRET_KEY=
CIBLE_ADMIN_EMAIL=       # vides = /admin répond 404
CIBLE_ADMIN_HASH=
```

Trois fonctionnalités sont **opt-in par variable vide** : Analytics, Turnstile
et l'espace d'administration. Sans les clés, la fonctionnalité n'existe pas —
pas de dégradation, pas d'erreur.

---

## 8. Déploiement

### Hébergement

Coolify sur Hetzner. Traefik devant le conteneur : il termine le TLS et
transmet une requête HTTP simple. D'où `trustProxies(at: '*')` dans
`bootstrap/app.php` — sans lui, Laravel génère toutes ses URL en `http://` sur
une page servie en `https://` (contenu mixte bloqué par le navigateur,
`canonical` erroné pour Google), et `$request->ip()` renvoie l'IP du proxy,
ce qui rendait la limitation de débit commune à tous les visiteurs.

`at: '*'` suppose que le conteneur n'est joignable **que** via le proxy —
c'est le cas sous Coolify.

### ⚠ Volume persistant — obligatoire

Coolify → application → **Storages** → *Volume Mount* :

```
/app/storage/app/contenu
```

Sans ce volume, tout ce qui est écrit par l'administration disparaît au
déploiement suivant : les modifications de contenu, les visuels téléversés, et
le mot de passe administrateur s'il a été posé avec `cible:admin-motdepasse`.

Le tableau de bord affiche un bandeau d'alerte quand le volume manque : la
détection compare le numéro de périphérique du dossier de contenu et celui du
code — un volume monté est un système de fichiers distinct.

### Cycle

Push sur `main` ou `develop` → Coolify reconstruit l'image et redéploie
(~2 min). Le conteneur exécute au démarrage :

```
php artisan config:cache && route:cache && view:cache && php -S 0.0.0.0:8000 -t public
```

### Branches

- `main` — production
- `develop` — recette

Historiquement les deux sont poussées ensemble. Vérifier la convention en cours
avant de pousser.

### Avant chaque commit

```bash
php artisan view:cache      # compile toutes les vues Blade
php artisan config:cache    # valide la configuration
php artisan route:list      # 24 routes attendues
```

Si l'une échoue, ne pas commiter.

---

## 9. Espace d'administration

**Un seul compte, aucune table `users`.** Cela supprime d'un coup
l'inscription, la réinitialisation de mot de passe et la vérification d'email :
quatre surfaces d'attaque en moins sur un site public.

Ordre de résolution du hash, dans `AdminAuth::hashValide()` :

1. `CIBLE_ADMIN_HASH` brut (bcrypt, 60 caractères)
2. la même valeur encodée en **base64**
3. le fichier `admin-hash.txt` sur le volume persistant

### Pourquoi trois sources

Beaucoup d'interfaces de configuration — Coolify en fait partie — interprètent
les `$` d'un hash bcrypt (`$2y$12$…`) comme des variables shell et les avalent.
Le hash arrive tronqué. Un contrôle du seul préfixe `$2y$` le laisse passer,
puis `Hash::check` **lève une exception** au lieu de renvoyer `false` : une
erreur 500 à la connexion, sans indication de la cause.

D'où : validation par `strlen() === 60` **et** `password_get_info()`, support
du base64 (aucun `$` à interpréter), et repli sur un fichier.

### Commandes

```bash
php artisan cible:admin-hash              # génère un hash, l'affiche en base64
php artisan cible:admin-hash --verifier   # diagnostic : source du hash, email, mot de passe
php artisan cible:admin-motdepasse        # écrit le hash sur le volume (contourne l'env)
```

**Préférer la variable d'environnement en base64** : elle survit à la perte du
volume, contrairement au fichier.

---

## 10. Sécurité

**En-têtes** (`SecurityHeaders`) : CSP, `X-Content-Type-Options: nosniff`,
`X-Frame-Options: DENY`, `Referrer-Policy`, `Permissions-Policy`,
`Cross-Origin-Opener-Policy`.

La CSP autorise explicitement : Google Fonts, unpkg (Leaflet), CartoDB (fonds
de carte), Google Tag Manager, `challenges.cloudflare.com` (Turnstile).
**Ajouter un domaine externe impose de l'ajouter à la CSP**, sinon la
ressource est bloquée silencieusement.

**Limitation de débit**

| Limiteur | Plafond | Contre quoi |
|---|---|---|
| `devis-ip` | 5 / 10 min par IP | l'acharnement d'une source |
| `devis-global` | 40 / heure toutes sources | une attaque distribuée qui changerait d'IP |
| connexion admin | 6 / 10 min | la force brute |
| `/api/reseau-map` | 60 / min | |

**Anti-spam** (`AntiSpam`) : notation par signaux, seuil de rejet à 5. Champs
pièges invisibles (+10), envoi en moins de 4 secondes (+5), URL dans un champ
d'identité (+5), raccourcisseur d'URL (+5), HTML dans le message (+4), écriture
non latine (+2), nom identique à la société (+2), aucune case cochée (+2).

Un message rejeté affiche une **confirmation normale** à l'expéditeur. Un robot
ne peut donc pas identifier ce qui l'a trahi.

**Uploads** : validés par `extensions:` et non `mimes:` — un `.ai` est un PDF
déguisé, un `.eps` du PostScript, et `mimes:` rejetait des fichiers légitimes.
Rien n'est stocké : les fichiers partent en pièce jointe et disparaissent.

> ⚠ `post_max_size` (Dockerfile, 45M) borne le corps **entier** de la requête.
> Il doit rester ≥ 4 × `DOC_MAX_KO` (10 Mo), sinon PHP rejette le POST avant
> Laravel et le visiteur reçoit un 419 au lieu d'un message de validation.

---

## 11. Journalisation

`LOG_CHANNEL=stack` écrit dans `storage/logs/laravel.log` **à l'intérieur** du
conteneur : invisible dans l'onglet Logs de Coolify et perdu à chaque
redéploiement. Passer à **`stderr`** pour diagnostiquer.

`Journal` (`app/Support/Journal.php`) encapsule les écritures : une
journalisation impossible ne doit jamais casser une page — le cas s'est produit
sur la connexion à l'admin.

---

## 12. Mail

`config/mail.php` → `cible_devis_to` = destinataire du formulaire, piloté par
`CIBLE_DEVIS_TO`.

**Laravel 11+ lit `MAIL_SCHEME`, jamais `MAIL_ENCRYPTION`** — cette dernière
est ignorée en silence.

- port 587 → `MAIL_SCHEME=smtp` (STARTTLS, cas courant)
- port 465 → `MAIL_SCHEME=smtps` (TLS implicite)

**Gmail / Google Workspace** : le mot de passe du compte est refusé
(`535-5.7.8`). Il faut activer la validation en deux étapes puis générer un
**mot de passe d'application de 16 caractères**, à coller sans les espaces.
`MAIL_USERNAME` doit être une vraie boîte, pas un alias.

---

## 13. Pièges rencontrés — à ne pas revivre

**`<x-decor>` cité dans un commentaire CSS.** Blade compile les balises de
composant *avant* de retirer les commentaires : un `@endif` orphelin, une
erreur 500 sur toutes les pages.

**`[hidden]` neutralisé.** Des règles `display` d'auteur écrasaient l'attribut
`hidden`, ce qui cassait silencieusement le repli sans JavaScript de la
navigation par étapes. D'où `[hidden]{display:none!important}` global.

**L'unité `ch` sur un conteneur.** `max-width:40ch` se calcule sur la taille de
police du conteneur (~380 px), pas sur celle du titre rendu à 64 px : quatre
lignes tassées. Les entêtes sont harmonisées à `860px`.

**Favicon.** Les sources étaient noires opaques, pas blanches — l'aperçu
d'image induisait en erreur. Le détourage se fait par remplissage depuis les
bords, à 1200 px, avec `imagealphablending(false)` pendant le rééchantillonnage
(sinon frange sombre).

**Compteurs animés.** `anime()` écrase le `textContent` de sa cible : le `+` de
« +400 » doit vivre dans un `<span>` **séparé**, sinon il est effacé.

**Textes fournis en `.docx`.** L'outil de lecture ne lit pas ces fichiers :
dézipper et parser `word/document.xml`. Les documents contiennent des doublons
et des coquilles — les signaler plutôt que de trancher en silence.

---

## 14. Conventions éditoriales

Slogan : *« Vous visez juste »*. Ton : autorité tranquille et preuve terrain,
pas de superlatifs, chiffres précis, faits datés.

| Fait | Valeur |
|---|---|
| Fondation | 1994 |
| Distinctions d'État | 3 (2016, 2019, 2020) |
| Panneaux | **+400** — jamais de chiffre exact, jamais de répartition par zone |
| Communes | 31 |
| Adresse | Rue des Ambassadeurs, Riviera M'Badon, 10 BP 1029, Abidjan |
| Mobile / fixe | +225 07 00 78 06 28 / +225 27 22 20 80 08 |
| Emails | commercial@ · secretariat@ · studio@ `cible-ci.com` |

**Aucune donnée personnelle sur le site** : pas de photo de collaborateur, pas
de nom, pas de biographie. Le site représente l'entreprise, pas ses individus.

**Palette verrouillée** — 5 couleurs plus 3 neutres, définies dans `:root` de
`_layout.blade.php`. Ne pas en ajouter sans validation.

`#E20613` rouge · `#FAB80B` jaune · `#3AA835` vert · `#3F7FC0` bleu ·
`#81358A` violet · `#E6E6E6` gris · `#111111` noir · `#FFFFFF` blanc

**Polices** : Poppins (titres), Nunito (corps), via Google Fonts.

**Toute modification de texte** doit venir du document Word de référence
(`docs/`), jamais être inventée.

---

## 15. Développement local

```bash
git clone https://github.com/cible-studio/cible-site.git
cd cible-site
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan serve
```

Adapter `.env` : `APP_ENV=local`, `APP_DEBUG=true`, `MAIL_MAILER=log`
(les mails partent alors dans `storage/logs/laravel.log`).

Pour tester l'administration en local, renseigner `CIBLE_ADMIN_EMAIL` et
`CIBLE_ADMIN_HASH` (`php artisan cible:admin-hash`). L'alerte de volume
persistant ne s'affiche pas hors production : il n'y a pas de conteneur à
recréer sur un poste de développement.

**Tester en 360 px et en 1440 px.** Le site est majoritairement consulté depuis
Abidjan, sur des téléphones Android d'entrée de gamme en 3G/4G.

---

## 16. Points ouverts

- Migration vers le domaine final `cible-ci.com` : bascule des enregistrements
  **A / AAAA** OVH uniquement, en laissant MX et TXT intacts (§3)
- `LOG_CHANNEL=stderr` recommandé en production pour rendre les journaux
  lisibles depuis Coolify

---

*Contact technique : studio@cible-ci.com*
