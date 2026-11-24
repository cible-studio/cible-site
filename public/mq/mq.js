/* ==========================================================================
   CIBLE — maquette de refonte (/marquette) · comportements communs
   Amélioration progressive : sans JavaScript, tout le contenu est visible
   et les chiffres sont justes (ils sont écrits en clair dans le HTML).
   ========================================================================== */
(() => {
  const doux = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const html = document.documentElement;

  /* ---- En-tête : ombre au défilement + barre d'action mobile ---- */
  const entete = document.querySelector('.entete');
  const barre = document.querySelector('.barre-action');
  const surDefilement = () => {
    const y = scrollY;
    entete?.classList.toggle('est-defile', y > 8);
    // La barre d'action apparaît une fois le haut de page dépassé : le
    // bouton du héro suffit tant qu'il est à l'écran.
    barre?.classList.toggle('est-visible', y > 420);
  };
  addEventListener('scroll', surDefilement, { passive: true });
  surDefilement();

  /* ---- Sous-menu des expertises ---- */
  const bouton = document.querySelector('[data-deroulant]');
  const panneau = bouton && document.getElementById(bouton.getAttribute('aria-controls'));
  if (bouton && panneau) {
    const ouvrir = (o) => { bouton.setAttribute('aria-expanded', o); panneau.classList.toggle('est-ouvert', o); };
    bouton.addEventListener('click', () => ouvrir(bouton.getAttribute('aria-expanded') !== 'true'));
    document.addEventListener('click', (e) => { if (!e.target.closest('.deroulant')) ouvrir(false); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && bouton.getAttribute('aria-expanded') === 'true') { ouvrir(false); bouton.focus(); } });
    panneau.addEventListener('focusout', (e) => { if (!panneau.contains(e.relatedTarget) && e.relatedTarget !== bouton) ouvrir(false); });
  }

  /* ---- Menu mobile plein écran ---- */
  const menu = document.getElementById('panneau-mobile');
  const ouvreurs = document.querySelectorAll('[data-menu-ouvrir]');
  const fermeur = document.querySelector('[data-menu-fermer]');
  if (menu) {
    let dernier = null;
    const basculer = (o) => {
      menu.classList.toggle('est-ouvert', o);
      menu.setAttribute('aria-hidden', !o);
      menu.inert = !o;
      html.classList.toggle('est-verrouille', o);
      ouvreurs.forEach((b) => b.setAttribute('aria-expanded', o));
      if (o) { dernier = document.activeElement; fermeur?.focus(); } else { dernier?.focus(); }
    };
    menu.inert = true;
    ouvreurs.forEach((b) => b.addEventListener('click', () => basculer(true)));
    fermeur?.addEventListener('click', () => basculer(false));
    menu.addEventListener('click', (e) => { if (e.target.closest('a')) basculer(false); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && menu.classList.contains('est-ouvert')) basculer(false); });
  }

  /* ---- Apparition au défilement ---- */
  const aReveler = document.querySelectorAll('.rv');
  if (!doux && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver((entrees) => entrees.forEach((e) => {
      if (e.isIntersecting) { e.target.classList.add('est-vu'); io.unobserve(e.target); }
    }), { rootMargin: '0px 0px -8% 0px', threshold: .08 });
    aReveler.forEach((el) => io.observe(el));
  } else {
    aReveler.forEach((el) => el.classList.add('est-vu'));
  }

  /* ---- Compteurs : la valeur finale est déjà dans le HTML (crawlers,
     lecteurs d'écran, sans JS). L'animation part de 0 seulement ici. ---- */
  if (!doux && 'IntersectionObserver' in window) {
    const compteurs = document.querySelectorAll('[data-compte]');
    const anime = (el) => {
      const fin = parseInt(el.dataset.compte, 10);
      if (!Number.isFinite(fin)) return;
      const t0 = performance.now(), d = 1300;
      const pas = (t) => {
        const p = Math.min((t - t0) / d, 1);
        el.textContent = Math.round(fin * (1 - Math.pow(1 - p, 3)));
        if (p < 1) requestAnimationFrame(pas);
      };
      requestAnimationFrame(pas);
    };
    const ioC = new IntersectionObserver((entrees) => entrees.forEach((e) => {
      if (e.isIntersecting) { anime(e.target); ioC.unobserve(e.target); }
    }), { threshold: .6 });
    compteurs.forEach((el) => ioC.observe(el));
  }
})();
