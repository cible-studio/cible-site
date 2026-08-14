# Manuel d'utilisation — Site CIBLE

**Pour qui** : l'équipe CIBLE. Aucune compétence technique n'est nécessaire.
**Ce que ce manuel couvre** : modifier les textes et les images du site vous-même.

---

## 1. Ce que vous pouvez modifier — et ce que vous ne pouvez pas

L'espace d'administration vous permet de changer **tout le contenu visible** du
site : titres, paragraphes, boutons, listes, photos, coordonnées, chiffres,
projets présentés.

Il ne permet pas de changer la **structure** : ajouter une page, déplacer un
bloc, changer les couleurs ou la mise en page. Ces opérations demandent un
développeur — c'est volontaire : cela garantit que le site reste conforme à la
charte quoi qu'il arrive.

| Vous pouvez | Vous ne pouvez pas |
|---|---|
| Changer n'importe quel texte | Ajouter une page |
| Remplacer une photo | Déplacer un bloc |
| Ajouter ou retirer une ligne dans une liste | Changer les couleurs ou les polices |
| Modifier les coordonnées et les chiffres | Modifier le formulaire de contact |
| Revenir en arrière sur une modification | Supprimer une section |

---

## 2. Se connecter

1. Rendez-vous sur **https://www.cible-ci.com/admin**
2. Saisissez votre email et votre mot de passe

> **L'adresse ne fonctionne pas ?** C'est normal si les identifiants ne sont pas
> configurés : la page répond « introuvable » plutôt que d'afficher un
> formulaire de connexion. C'est une protection — un robot qui parcourt le site
> ne voit aucune porte à forcer. Contactez votre développeur.

Après six tentatives ratées, la connexion se bloque pendant dix minutes. Là
encore, c'est une protection contre les robots.

---

## 3. Le tableau de bord

C'est la page d'accueil de l'administration. Elle affiche :

- **Pages modifiables** — le nombre de pages du site que vous pouvez éditer
- **Textes modifiables** — le nombre de textes au total
- **Visuels remplaçables** — le nombre de photos que vous pouvez changer
- **Dernière modification** — quand le site a été retouché pour la dernière fois

En dessous, la liste des pages. Une page déjà retouchée porte la mention
**modifiée** et la date.

### La barre latérale

À gauche, la navigation est organisée en trois blocs :

- **Vue d'ensemble** — le tableau de bord
- **Les pages du site** — Accueil, Services, Qui sommes-nous, Réseau,
  Références, Contact
- **Commun à tout le site** — Coordonnées, Chiffres clés, Réalisations

Un **point jaune** à côté d'un nom signale que cette section a déjà été
modifiée depuis l'administration.

Sur téléphone, la barre est masquée : le bouton **☰** en haut à gauche l'ouvre.

---

## 4. Modifier une page

1. Cliquez sur la page dans la barre latérale
2. La page est découpée en **blocs** (Bandeau principal, Récit de marque…)
3. En haut, un **sommaire** permet d'atteindre directement un bloc sans faire
   défiler — utile sur l'accueil, qui compte treize blocs
4. Modifiez ce que vous voulez
5. Cliquez sur **Enregistrer** — le bouton reste visible en bas de l'écran
   pendant que vous faites défiler

**La modification est visible immédiatement sur le site.** Il n'y a rien à
publier, rien à redéployer. Le bouton **Voir la page ↗** en haut à droite ouvre
la page publique dans un nouvel onglet pour vérifier.

### Les types de champs

**Texte simple** — une ligne. Un titre, un libellé de bouton.

**Zone de texte** — un paragraphe. Écrivez normalement, sans vous préoccuper de
la mise en forme : elle est appliquée automatiquement.

**Liste** — **une entrée par ligne**. Par exemple, pour les communes couvertes :

```
Plateau
Cocody
Yopougon
```

Ajoutez une ligne, elle apparaît sur le site. Retirez-la, elle disparaît. Les
couleurs et la mise en page s'ajustent seules, quel que soit le nombre
d'entrées. Les lignes vides sont ignorées.

**Image** — la photo actuelle s'affiche. Pour la remplacer, choisissez un
fichier. **Laissez vide pour conserver l'image actuelle.**

- Formats acceptés : JPG, PNG, WEBP
- Taille maximale : 5 Mo
- Une photo trop petite s'affichera floue : visez au moins 900 pixels de large

### Mettre un mot en avant

Certains champs permettent de faire ressortir une partie du texte en couleur.
Entourez le passage de **deux astérisques** :

```
Votre marque mérite un impact durable : **nous construisons votre visibilité.**
```

Le texte entre astérisques s'affiche dans la couleur d'accent de la section
(rouge sur le bandeau d'accueil, jaune sur la signature, la couleur du pôle sur
les pages Services). Les champs concernés le précisent sous le champ.

> N'écrivez jamais de code HTML dans les champs : il ne sera pas interprété,
> il s'affichera tel quel sur le site.

---

## 5. Les contenus communs à tout le site

Trois écrans à part, parce que leur contenu apparaît sur **plusieurs pages** à
la fois. Les modifier met tout le site à jour d'un coup.

### Coordonnées

Téléphones, emails, adresse. Repris dans le pied de page, sur la page Contact,
et dans les données que Google utilise pour afficher votre fiche.

### Chiffres clés

Le nombre de panneaux, de communes, d'années d'expérience, de distinctions.
Ils alimentent les compteurs animés de l'accueil et les statistiques des autres
pages.

> ⚠ Le site affiche **« +400 panneaux »**, jamais un chiffre exact — c'est une
> décision commerciale prise en août 2026. Si vous changez ce chiffre, il
> change partout à la fois.

### Réalisations

Les six projets présentés. Pour chacun : le nom du client, le secteur, le
descriptif, les résultats, la couleur et le visuel. Ils alimentent trois
endroits d'un coup : l'aperçu sur l'accueil, la grille de la page Références,
et la page de détail du projet.

---

## 6. Annuler une modification

En bas du tableau de bord, la section **« Revenir au contenu d'origine »** liste
les sections que vous avez modifiées.

Cliquer sur **Réinitialiser** rétablit le contenu livré à la mise en ligne du
site. C'est le filet de sécurité : une modification qui a mal tourné se répare
en un clic, sans développeur.

> ⚠ L'opération est définitive et ne concerne qu'une section à la fois. Vos
> modifications de cette section sont perdues, celles des autres sections sont
> conservées.

---

## 7. Le formulaire de demande de devis

Les demandes envoyées depuis la page Contact arrivent par email à
**commercial@cible-ci.com**. Rien n'est stocké sur le site : ni les messages,
ni les fichiers joints.

Le visiteur peut joindre jusqu'à **4 documents de 10 Mo** (brief, logo, charte,
cahier des charges).

### Répondre à une demande

Répondez directement à l'email : l'adresse du prospect est en **Répondre à**.

### À propos du spam

Le formulaire est protégé sur plusieurs niveaux : pièges invisibles pour les
robots, détection des envois trop rapides, refus des messages contenant des
liens publicitaires, et limitation du nombre d'envois (5 par visiteur toutes
les dix minutes, 40 par heure au total).

Un message jugé indésirable est **rejeté en silence** : l'expéditeur voit une
confirmation normale, mais rien n'est envoyé. Un robot ne peut donc pas
comprendre ce qui l'a trahi et ajuster son message.

**Si un message publicitaire passe malgré tout** : ne cliquez sur aucun lien,
ne répondez pas — l'adresse de réponse est celle du spammeur. Signalez-le à
votre développeur, les défenses seront ajustées.

---

## 8. Bonnes pratiques

**Vérifiez sur téléphone.** La majorité des visiteurs consultent le site depuis
un mobile à Abidjan. Un titre qui tient sur une ligne sur votre ordinateur peut
en occuper quatre sur un téléphone. Le bouton *Voir la page ↗* permet de
vérifier immédiatement.

**Restez court sur les titres.** La mise en page est calibrée pour la longueur
actuelle des textes. Doubler la longueur d'un titre le fera déborder.

**Ne changez pas les chiffres officiels à la légère.** 1994, 31 communes, 3
distinctions, +400 panneaux : ils apparaissent à plusieurs endroits et
engagent l'entreprise.

**Une modification à la fois.** Enregistrez, vérifiez sur le site, puis passez
à la suivante. Si quelque chose ne va pas, vous saurez exactement quoi
réinitialiser.

**Déconnectez-vous** sur un ordinateur partagé — le bouton est en bas de la
barre latérale.

---

## 9. En cas de problème

| Ce que vous voyez | Ce que ça veut dire |
|---|---|
| Un bandeau orange sur le tableau de bord | Vos modifications risquent d'être perdues. **Contactez votre développeur avant de continuer à travailler.** |
| « Le visuel actuel est introuvable » | L'image a été supprimée ou n'a jamais été déposée. Déposez-en une nouvelle. |
| Un message rouge sous un champ | Le contenu saisi ne convient pas — la raison est indiquée. Corrigez et réenregistrez. |
| La page publique n'a pas changé | Actualisez avec **Ctrl+F5** (Cmd+Shift+R sur Mac) : votre navigateur affiche une version en cache. |
| `/admin` répond « page introuvable » | Les identifiants ne sont pas configurés sur le serveur. Contactez votre développeur. |

**Contact technique** : studio@cible-ci.com

---

*Dernière mise à jour : 14 août 2026*
