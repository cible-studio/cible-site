# CLAUDE.md — Site vitrine CIBLE CI

> ⚠️ Ce fichier DOIT être relu au début de CHAQUE tâche, même si l'utilisateur ne le mentionne pas. Ces règles ne sont pas optionnelles.

---

## 🎯 CE QUE TU DOIS SAVOIR EN PREMIER

### Nature du projet

- **Site vitrine public** de CIBLE, régie publicitaire ivoirienne fondée en 1994.
- **Laravel 12 + Blade + SQLite + Leaflet** — statique, sans base de données réelle (SQLite juste pour sessions/cache Laravel).
- **6 pages** : accueil, qui-sommes-nous, services, réseau (avec carte), références, contact.
- **1 endpoint JSON** `/api/reseau-map` alimenté par un fichier statique `public/data/reseau-map.json` (31 communes + GPS).
- **1 formulaire /devis** → mail à `commercial@cible-ci.com` (rate-limited 5/10min).

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
│       ├── references.blade.php
│       ├── contact.blade.php
│       └── emails/cible/devis.blade.php        ← template mail formulaire devis
├── routes/web.php                              ← 8 routes total
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
| Panneaux en propre | **364** |
| Communes couvertes | **31** |
| Adresse | Rue des Ambassadeurs, Riviera M'Badon, 10 BP 1029, Abidjan |
| Téléphone | +225 07 00 78 06 28 |
| Email commercial | `commercial@cible-ci.com` |
| Email studio | `studio@cible-ci.com` |

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
2. Tu ouvres le fichier avec l'outil `Read` (les .docx sont lisibles).
3. Tu identifies dans les vues Blade quels textes correspondent aux IDs.
4. Tu proposes la LISTE des modifications avant de coder (règle N°1 CIBLE).
5. Après validation utilisateur, tu appliques toutes les modifs d'un coup.

**Ne jamais inventer de texte** — si un ID n'est pas clair, demande.

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

- **Repo git** : *à créer* (`cible-studio/cible-site` sur GitHub prévu)
- **Hébergement** : Coolify + Hetzner *à mettre en place*
- **Domaine** : `cible-ci.com` (DNS OVH)

### Une fois le git init fait

- Branche principale : `main` (prod)
- Pas de branche `develop` séparée pour ce projet (site vitrine faible fréquence de release)
- Chaque commit `main` = déploiement automatique via Coolify

### ⚠️ OVH DNS — INTERDICTION ABSOLUE

Ne JAMAIS toucher aux enregistrements **MX** et **TXT (SPF/DKIM)** du domaine `cible-ci.com` en changeant les DNS. Sinon on casse les mails de toute l'équipe CIBLE (`@cible-ci.com`).

Seuls les enregistrements **A** (pointant vers l'IP Hetzner) et **AAAA** peuvent être modifiés pour la mise en prod du site.

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
{
  "communes": [
    {
      "name": "Cocody",
      "lat": 5.348,
      "lng": -3.986,
      "count": 28,
      "notes": "…"
    }
  ]
}
```

Pour éditer : ouvrir directement le fichier avec `Edit` ou `Write`. Pas de BDD, pas de migration.

**31 communes** doivent être présentes à tout moment (chiffre officiel affiché sur le site).

---

## 📋 Checklist début de session

Quand l'utilisateur t'invoque dans ce dossier :

- [ ] Lire ce `CLAUDE.md` complet
- [ ] Vérifier `git status` (ou noter que git n'est pas encore init)
- [ ] Si l'utilisateur mentionne un fichier Word (`.docx`), l'ouvrir avec `Read` avant de proposer quoi que ce soit
- [ ] Demander sur quelle(s) page(s) portent les modifs
- [ ] Suivre la RÈGLE N°1 : LISTER → attendre validation → coder → rapport final

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
```

---

## 🆘 Points de vigilance récurrents

- **Chiffres officiels** : 1994, 3, 364, 31 → ne jamais improviser
- **Superposition Poppins 900** sur mobile — testé et corrigé 2026-08-04, ne pas ré-augmenter les tailles
- **Panora n'existe pas ici** → aucune référence
- **Contact studio** : `studio@cible-ci.com` (l'utilisateur)
- **Aucun compte client, aucun login, aucune BDD relationnelle** — c'est une vitrine pure

---

**En cas de doute → DEMANDER, ne pas inventer.**
