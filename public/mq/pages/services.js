/* ==========================================================================
   Maquette — page NOS SERVICES : navigation interne des 4 pôles.
   Amélioration progressive : sans JavaScript, les liens d'ancre suffisent.
   Ici on signale seulement le pôle en cours de lecture (aria-current) et,
   sur mobile, on fait glisser la barre pour garder ce pôle visible.
   ========================================================================== */
(() => {
  const nav = document.querySelector('[data-sv-nav]');
  if (!nav || !('IntersectionObserver' in window)) return;

  const doux = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const piste = nav.querySelector('.sv-nav__piste');
  const liens = new Map([...nav.querySelectorAll('a[href^="#"]')].map((a) => [a.hash.slice(1), a]));
  const visibles = new Set();
  let actif = null;

  const marquer = () => {
    // Premier pôle (ordre du document) présent dans la bande de lecture.
    const id = [...liens.keys()].find((cle) => visibles.has(cle)) ?? null;
    if (id === actif) return;
    actif = id;

    liens.forEach((a, cle) => {
      if (cle === id) a.setAttribute('aria-current', 'true');
      else a.removeAttribute('aria-current');
    });

    // La barre ne défile que si elle déborde (mobile) : on centre le lien actif.
    const a = id && liens.get(id);
    if (a && piste.scrollWidth > piste.clientWidth) {
      piste.scrollTo({ left: a.parentElement.offsetLeft - (piste.clientWidth - a.offsetWidth) / 2, behavior: doux ? 'auto' : 'smooth' });
    }
  };

  // Bande de lecture : une fine tranche au tiers haut de l'écran, sous
  // l'en-tête et la barre des pôles.
  const io = new IntersectionObserver((entrees) => {
    entrees.forEach((e) => (e.isIntersecting ? visibles.add(e.target.id) : visibles.delete(e.target.id)));
    marquer();
  }, { rootMargin: '-34% 0px -62% 0px' });

  liens.forEach((_, cle) => {
    const section = document.getElementById(cle);
    if (section) io.observe(section);
  });
})();
