# CIBLE — brief entreprise

> Destiné à être fourni à un assistant (Claude Desktop ou autre) qui travaille
> sur des sujets CIBLE : présentations, devis, audits, contenus, veille.
>
> Pour tout ce qui touche au **code du site vitrine**, la référence reste
> `CLAUDE.md` à la racine du dépôt. Ce fichier-ci décrit l'entreprise ; celui-là
> décrit le projet.
>
> Dernière mise à jour : 9 octobre 2026.

---

## 1. L'entreprise

**CIBLE** — régie publicitaire ivoirienne, **fondée en 1994** (32 ans en 2026),
basée à Abidjan.

**Slogan : « Vous visez juste »**

Ce n'est plus seulement une régie d'affichage : l'offre couvre quatre pôles.

| N° | Pôle | Promesse |
|---|---|---|
| 01 | **OOH** (affichage extérieur) | Donner de la visibilité aux marques dans l'espace public. |
| 02 | **Digital — Stratégie & Création** | Construire des marques qui émergent dans les conversations digitales. |
| 03 | **Marketing opérationnel** | Aller au contact, là où la décision se prend. |
| 04 | **Media Intelligence** | Piloter une campagne, pas seulement la diffuser. |

CIBLE se présente donc comme une **agence de communication 360**, pas comme un
simple afficheur — c'est un point de positionnement important dans tout document
commercial.

### Chiffres officiels — ne jamais improviser

| Fait | Valeur |
|---|---|
| Année de fondation | **1994** |
| Communes couvertes | **31** |
| Distinctions d'État | **3** |
| Panneaux (chiffre **public**) | **« +400 »** |

### ⚠ Règle de publication sur le parc de panneaux

Le chiffre public est **« +400 panneaux »**, sans exception et **sans aucune
répartition chiffrée par zone**.

Les comptages exacts (par exemple la base de calcul des taux d'occupation :
panneaux Abidjan, double-faces, faces commercialisables) sont des **données de
travail internes**. Elles servent aux calculs, aux bilans et aux arbitrages
commerciaux — elles ne sortent jamais dans un document public, un site, une
plaquette ou un deck client.

Concrètement : ne jamais écrire un nombre exact de panneaux dans un livrable
destiné à l'extérieur.

### Distinctions officielles — à citer mot pour mot

| Année | Distinction |
|---|---|
| 2016 | 2ᵉ prix du meilleur publicitaire |
| 2019 | Chevalier de l'Ordre du Mérite de la Communication |
| 2020 | Officier de l'Ordre du Mérite National |

### Coordonnées

- **Adresse** : Rue des Ambassadeurs, Riviera M'Badon, 10 BP 1029, Abidjan, Côte d'Ivoire
- **Mobile** : +225 07 98 49 66 74
- **Fixe** : +225 27 22 20 80 08
- **Commercial** : commercial@cible-ci.com
- **Secrétariat** : secretariat@cible-ci.com
- **Studio** : studio@cible-ci.com
- **Domaine** : cible-ci.com (registrar OVH)

### Ton éditorial

Autorité tranquille et preuve terrain. **Pas de superlatifs marketeux.** Chiffres
précis, faits datés, langage direct. Les agences de référence visuellement citées
sont de type McCann.

---

## 2. Les deux systèmes internes — à ne jamais confondre

C'est le point le plus souvent mal compris. **PANORA et le site vitrine sont deux
produits totalement séparés.**

### PANORA (ex-PROGICIA) — le SaaS interne

Plateforme **Laravel / MySQL** de gestion d'exploitation :

- inventaire des panneaux et des faces,
- campagnes, planification et poses,
- facturation,
- taxes communales (ODP / TM) sur une trentaine de communes,
- Espace Technicien mobile,
- module Taxes.

Usage strictement interne. C'est l'outil de production de la régie.

### cible-ci.com — le site vitrine

**⚠ Ce n'est plus un site WordPress / Elementor en une page.** Il a été
entièrement reconstruit.

- **Laravel 13 + Blade + SQLite** (SQLite uniquement pour les sessions et le
  cache — il n'y a **aucune base de données relationnelle de contenu**).
- **6 pages** : accueil, qui-sommes-nous, services, réseau, références, contact.
- **+ 6 pages de détail de réalisation** sur `/references/{slug}`.
- **Carte interactive Leaflet** sur `/reseau` : 31 communes avec coordonnées GPS.
- **Formulaire de demande** qui envoie un mail à commercial@cible-ci.com,
  limité à 5 envois par 10 minutes, avec jusqu'à 4 pièces jointes.
- **Espace d'administration `/admin`** : environ 195 champs de contenu éditables
  sans développeur, sur les 6 pages. Compte unique en variables
  d'environnement, pas de table d'utilisateurs.
- Hébergé sur **Coolify + Hetzner**, déployé par conteneur Docker.
- Une **refonte complète est en cours** (branche `refonte-v2`) : design plus
  dense, animations avancées (GSAP, ScrollTrigger, Lenis), structure resserrée
  en 5 pages. Motif : le site actuel est jugé trop long, trop dispersé, et ne
  reflète pas une agence 360.

### 🚨 La règle fondatrice : CIBLE ≠ PANORA

Le site vitrine est **totalement indépendant** de PANORA.

- **Aucun appel HTTP** du site vers PANORA.
- **Aucune base de données partagée**, aucun identifiant partagé.
- Aucune classe PANORA importée dans le site (on recopie le code au besoin, on
  ne le lie jamais).
- Si PANORA tombe, le site continue de fonctionner. Si le site tombe, PANORA
  continue de fonctionner.

**La carte du réseau n'est PAS alimentée par PANORA.** Elle lit un fichier
statique versionné, `public/data/reseau-map.json` (tableau plat de 31 communes
avec latitude et longitude), servi par un endpoint `/api/reseau-map`. C'était
une idée envisagée au départ ; elle a été écartée délibérément, parce qu'elle
aurait rendu le site vitrine dépendant d'un outil interne.

Raison de fond : le site vitrine doit rester **commercialisable**, et la marque
CIBLE doit y être propriétaire — pas PANORA.

### Interdiction absolue sur les DNS

Ne jamais toucher aux enregistrements **MX** et **TXT (SPF / DKIM)** du domaine
cible-ci.com : cela casserait la messagerie de toute l'équipe `@cible-ci.com`.
Seuls les enregistrements **A** et **AAAA** peuvent changer, et uniquement au
moment d'une bascule de production.

---

## 3. Charte graphique — « Opération Plume Rouge » (mai 2026)

La règle qui prime, citation de la charte :

> « **La couleur rouge doit rester prépondérante sur toutes les applications**
> et peut être principalement accompagnée avec le jaune. »

**Rouge et jaune sont les deux couleurs principales.** Vert, bleu et violet sont
secondaires : ponctuels, jamais à parts égales avec le rouge et le jaune. Erreur
classique à éviter : traiter les cinq couleurs comme un arc-en-ciel équivalent.

| Rôle | Nom | Hex |
|---|---|---|
| **Principale** | Rouge | `#E20613` |
| **Principale** | Jaune | `#FAB80B` |
| Secondaire | Vert | `#3AA835` |
| Secondaire | Bleu | `#3F7FC0` |
| Secondaire | Violet | `#81358A` |
| Neutre | Gris | `#E6E6E6` |
| Neutre | Noir | `#111111` |
| Neutre | Blanc | `#FFFFFF` |

**Ne pas ajouter d'autres couleurs.**

### Interdits, textuels dans la charte

- « **L'usage de dégradés ou d'effets est à éviter.** »
- « Les couleurs peuvent être combinées **avec du blanc ou du noir uniquement** »
  → jamais une couleur de marque posée sur une autre couleur de marque.
- « **N'appliquez pas le texte du titre en couleur à un arrière-plan non blanc.** »
- « **En cas de doute, privilégier le blanc comme base neutre et structurante.** »

### Piège pratique sur le jaune

Le jaune `#FAB80B` sur blanc ne donne qu'un contraste de **1,76:1**, et **1,41:1**
sur le gris — très en dessous du seuil de lisibilité (4,5:1). Le jaune est donc
un **aplat**, pas une couleur de texte : en texte il n'est lisible que sur noir
(10,72:1). Pour du texte coloré sur fond clair, utiliser le rouge (4,92:1).

### Typographie

- **Titres : MADE TOMMY** (Regular, Medium, Bold, Black). Police commerciale,
  pas une Google Font. Tant que les fichiers manquent, Poppins sert de
  remplaçante — c'est un pis-aller, pas la charte.
- **Corps : Nunito** (Regular, Medium, Bold).
- **⚠ « L'utilisation de Nunito peut se faire avec tous les caractères qui la
  composent SAUF LES CHIFFRES. »** Compteurs, dates, téléphones et statistiques
  se composent donc dans la police de titre.
- Documents bureautiques formels : Arial, Montserrat.

### Logo

Version couleur sur fond blanc uniquement. Sur fond de couleur : **monochrome
obligatoire**. Zone de protection égale à la hauteur du « E », réductible de
moitié si la place manque. **Minimum 24 px en digital.** Ne jamais déformer, ne
pas modifier la pupille, ne pas uniformiser les couleurs du symbole, ne pas
changer la couleur du slogan.

### Photographie

Images de **liberté** : nuages, grandes étendues de végétation, le perroquet.
Priorité aux **scènes de vie, « des personnages humains qui sont en joie »**,
avec des émotions perceptibles de joie ou d'apaisement. « Le but est vraiment
d'humaniser la marque. »

⚠ Le stock photographique actuel ne correspond pas à cette demande : ce sont des
constats de pose (rue, panneaux, trafic), sans personnage. Les visuels en place
sur la maquette de refonte sont des images de test Unsplash, à remplacer avant
toute mise en ligne publique.

---

## 4. Aucune donnée personnelle dans les livrables publics

Le site et les supports publics représentent **CIBLE l'entreprise**, pas ses
individus : pas de photo du personnel, pas de nom nominatif, pas de biographie.

---

## 5. À faire confirmer par l'utilisateur

Ces points ne sont pas établis par le dépôt et ne doivent pas être affirmés sans
validation :

- **Le rôle exact de l'utilisateur chez CIBLE.** Son adresse de contact est
  studio@cible-ci.com.
- **Les comptages internes du parc** (panneaux Abidjan, double-faces, faces
  commercialisables) : vérifier la valeur à jour avant tout calcul de taux
  d'occupation. Rappel : ces nombres ne sortent jamais en public.
- **La liste des concurrents directs** et le périmètre de la veille.
- **Les clients et prospects en cours**, ainsi que les travaux associés.
