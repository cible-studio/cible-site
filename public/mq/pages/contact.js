/* ==========================================================================
   Maquette — page CONTACT · formulaire en étapes, colonnes collantes.
   Amélioration progressive : sans JavaScript, les 4 étapes restent
   affichées l'une sous l'autre et le formulaire s'envoie d'une traite.
   La validation du serveur (CibleController::submitDevis) reste seule
   juge ; ce script évite seulement d'avancer puis de devoir revenir.
   ========================================================================== */
(() => {
  const doux = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const html = document.documentElement;
  const entete = document.querySelector('.entete');
  const hautEntete = () => (entete ? entete.offsetHeight : 84);
  const zone = document.getElementById('formulaire');
  if (!zone) return;

  /* ---- 1. Boutons d'action communs → formulaire ----
     Ils pointent vers cette même page : un clic rechargerait la page et
     effacerait la saisie en cours. Ici, ils mènent au formulaire. */
  document.querySelectorAll('.entete__cta, .barre-action .btn, .panneau-mobile__bas .btn--rouge')
    .forEach((a) => a.setAttribute('href', '#formulaire'));
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(([e]) => html.classList.toggle('formulaire-en-vue', e.isIntersecting),
      { rootMargin: '-20% 0px -20% 0px' }).observe(zone);
  }

  /* ---- 2. Colonnes collantes (ordinateur) ----
     La plus courte des deux colonnes accompagne le défilement : collée en
     haut si elle tient à l'écran, sinon ancrée par le bas (top négatif)
     pour qu'on puisse toujours la lire jusqu'au bout. */
  const grille = zone.parentElement;
  const colonnes = [...grille.children];
  const large = matchMedia('(min-width: 1100px)');
  const caler = () => colonnes.forEach((c) => {
    if (!large.matches) { c.style.removeProperty('--colle'); return; }
    c.style.setProperty('--colle', Math.min(hautEntete() + 24, innerHeight - c.offsetHeight - 24) + 'px');
  });
  caler();
  addEventListener('resize', caler, { passive: true });
  large.addEventListener?.('change', caler);
  if ('ResizeObserver' in window) {
    const ro = new ResizeObserver(caler);
    colonnes.forEach((c) => ro.observe(c));
  }

  /* ---- 3. Formulaire en étapes ---- */
  const form = document.getElementById('form-brief');
  if (!form) return; // demande déjà envoyée : pas de formulaire

  const etapes = [...form.querySelectorAll('.etape')];
  const progres = document.getElementById('progres');
  const compte = document.getElementById('et-compte');
  const pas = [...form.querySelectorAll('[data-pas]')];
  const prec = document.getElementById('et-prec');
  const suiv = document.getElementById('et-suiv');
  const envoi = document.getElementById('et-envoi');
  const note = document.getElementById('et-note');
  const alerte = document.getElementById('form-alerte');
  const formats = (form.dataset.formats || '').split(',').filter(Boolean);
  const maxOctets = parseInt(form.dataset.maxKo || '0', 10) * 1024;
  const aVerifier = '[required], input[type="file"]';

  // Erreur d'un champ, avec le message qu'aurait renvoyé le serveur
  // (attributs data-msg-*, issus de lang/fr/validation.php). '' = valide.
  const erreurDe = (ch) => {
    if (ch.type === 'file') {
      const f = ch.files && ch.files[0];
      if (!f) return '';
      if (maxOctets && f.size > maxOctets) return ch.dataset.msgTaille;
      const ext = f.name.includes('.') ? f.name.split('.').pop().toLowerCase() : '';
      return formats.length && !formats.includes(ext) ? ch.dataset.msgFormat : '';
    }
    if (ch.type !== 'checkbox' && !ch.value.trim()) ch.value = ''; // des espaces seuls = vide
    if (ch.checkValidity()) return '';
    if (ch.validity.valueMissing) return ch.dataset.msgRequis || ch.validationMessage;
    if (ch.validity.typeMismatch) return ch.dataset.msgFormat || ch.validationMessage;
    return ch.validationMessage;
  };

  // Affiche ou retire l'erreur d'un champ (message relié par aria-describedby).
  const marquer = (ch, texte) => {
    const bloc = ch.closest('.champ, .doc-zone, .consentement, .groupe');
    if (!bloc) return;
    const id = 'err-' + ch.name.replace('[]', '');
    const liens = (ch.getAttribute('aria-describedby') || '').split(' ').filter((x) => x && x !== id);
    let err = document.getElementById(id);
    bloc.classList.toggle('est-erreur', !!texte);
    if (texte) {
      if (!err) {
        err = document.createElement('p');
        err.className = 'erreur';
        err.id = id;
        bloc.append(err);
      }
      err.textContent = texte;
      ch.setAttribute('aria-invalid', 'true');
      ch.setAttribute('aria-describedby', [...liens, id].join(' '));
    } else {
      err?.remove();
      ch.removeAttribute('aria-invalid');
      liens.length ? ch.setAttribute('aria-describedby', liens.join(' ')) : ch.removeAttribute('aria-describedby');
    }
  };

  // Vérifie une étape ; place le focus sur le premier champ fautif.
  const valider = (etape) => {
    let premier = null;
    etape.querySelectorAll(aVerifier).forEach((ch) => {
      const texte = erreurDe(ch);
      marquer(ch, texte);
      if (texte && !premier) premier = ch;
    });
    premier?.focus();
    return !premier;
  };

  // Une correction efface aussitôt l'erreur affichée (serveur ou navigateur).
  const effacer = (e) => {
    const ch = e.target;
    if (ch.name && ch.closest('.est-erreur')) marquer(ch, ch.matches(aVerifier) ? erreurDe(ch) : '');
  };
  form.addEventListener('input', effacer);
  form.addEventListener('change', effacer);

  // Documents : nom du fichier choisi, affiché en entier.
  form.querySelectorAll('.doc input[type="file"]').forEach((ch) => {
    const nom = ch.parentElement.querySelector('.doc__fichier');
    const maj = () => { if (nom) nom.textContent = ch.files && ch.files[0] ? ch.files[0].name : ''; };
    ch.addEventListener('change', maj);
    maj(); // fichier conservé par le navigateur au retour arrière
  });

  // Le découpage n'est activé QUE si ce script s'exécute.
  form.classList.add('js-etapes');
  progres.hidden = false;

  let courant = Math.min(Math.max(parseInt(form.dataset.etapeDepart || '1', 10) || 1, 1), etapes.length);

  const afficher = (n, defiler) => {
    courant = n;
    const dernier = n === etapes.length;
    etapes.forEach((e, i) => e.toggleAttribute('data-courant', i === n - 1));
    pas.forEach((p, i) => { p.dataset.etat = i + 1 < n ? 'fait' : (i + 1 === n ? 'courant' : 'a-venir'); });
    prec.hidden = n === 1;
    suiv.hidden = dernier;
    envoi.hidden = !dernier; // l'envoi n'apparaît qu'à la dernière étape
    note.hidden = !dernier;
    envoi.parentElement.classList.toggle('etape-nav--fin', dernier);

    const nom = document.createElement('b');
    nom.textContent = etapes[n - 1].dataset.nom || '';
    compte.replaceChildren('Étape ' + n + ' sur ' + etapes.length + ' · ', nom);

    if (defiler) {
      // Retour au haut de l'étape. Position calculée « au repos » : les
      // colonnes collantes décalent getBoundingClientRect.
      const y = grille.getBoundingClientRect().top + scrollY
        + (progres.getBoundingClientRect().top - zone.getBoundingClientRect().top) - hautEntete() - 16;
      scrollTo({ top: Math.max(0, y), behavior: doux ? 'auto' : 'smooth' });
      // Focus sur le titre de l'étape : annoncé par les lecteurs d'écran,
      // sans ouvrir le clavier du téléphone.
      etapes[n - 1].querySelector('.bloc__titre')?.focus({ preventScroll: true });
    }
  };

  suiv.addEventListener('click', () => { if (valider(etapes[courant - 1])) afficher(courant + 1, true); });
  prec.addEventListener('click', () => afficher(courant - 1, true));

  // Entrée dans un champ texte : étape suivante plutôt qu'un envoi incomplet.
  form.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter') return;
    const t = e.target;
    if (t.tagName === 'TEXTAREA' || t.tagName === 'BUTTON') return;
    if (courant < etapes.length) { e.preventDefault(); suiv.click(); }
  });

  // Envoi : un champ requis vide (ou un document trop lourd) renvoie à son
  // étape, sans téléverser pour rien des fichiers sur une connexion lente.
  form.addEventListener('submit', (e) => {
    const fautive = etapes.findIndex((et) => [...et.querySelectorAll(aVerifier)].some((ch) => erreurDe(ch)));
    if (fautive !== -1) {
      e.preventDefault();
      if (fautive + 1 !== courant) afficher(fautive + 1, false);
      valider(etapes[fautive]);
      return;
    }
    envoi.disabled = true; // pas de double envoi pendant le téléversement
  });
  // Retour arrière du navigateur (cache de page) : bouton réactivé.
  addEventListener('pageshow', () => { envoi.disabled = false; });

  afficher(courant, false);

  // Retour d'erreur du serveur : on amène le visiteur au message.
  if (alerte) alerte.focus();
})();
