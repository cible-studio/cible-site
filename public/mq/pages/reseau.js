/* ==========================================================================
   CIBLE — maquette de refonte · page NOTRE RÉSEAU : carte interactive.
   · Leaflet (unpkg, contrôlé par SRI) et les données /api/reseau-map ne
     sont chargés qu'à l'approche de la carte : rien à télécharger pour qui
     ne descend pas jusque-là (visiteurs en 3G).
   · Règle CIBLE : le champ `total` des données n'est JAMAIS affiché — pas
     de nombre de panneaux par commune (décision 2026-08-10).
   · Défilement jamais piégé : molette inactive tant qu'on n'a pas cliqué la
     carte ; sur écran tactile, un doigt fait défiler la page, deux doigts
     déplacent la carte.
   · Lisibilité : en vue d'ensemble, le Grand Abidjan est regroupé en une
     seule étiquette ; partout, une étiquette qui en chevaucherait une autre
     passe à gauche, sinon se masque (le point reste, son nom s'affiche au
     clic). Priorité : l'ordre des listes de communes de l'espace admin.
   · Fond de carte : dessiné ici, en aplats, à partir des contours des 33
     régions (reseau-contours.json, 15 Ko compressé — geoBoundaries,
     CC BY 4.0). Les régions qui comptent une commune couverte sont teintées.
     Pourquoi pas de tuiles : CARTO exige désormais une clé (toutes ses
     tuiles affichent « API KEY REQUIRED », constaté le 2026-11-24) et la
     CSP n'autorise aucun autre fournisseur. Pour revenir à des tuiles :
     renseigner TUILES et, si l'hôte change, l'ajouter à img-src dans
     app/Http/Middleware/SecurityHeaders.php.
   ========================================================================== */
(() => {
  const bloc = document.querySelector('[data-carte]');
  if (!bloc) return;

  const LEAFLET = 'https://unpkg.com/leaflet@1.9.4/dist/';
  const SRI = {
    css: 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=',
    js: 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=',
  };
  const TUILES = null; // ex. 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png?api_key=…'
  const SEUIL_DETAIL = 10; // zoom à partir duquel les communes d'Abidjan sont détaillées
  const COULEUR = { abidjan: 'var(--rouge)', interieur: 'var(--jaune)' };
  // Les aplats SVG ne lisent pas les variables CSS : valeurs des jetons de mq.css.
  const FOND = {
    terre: '#FFFFFF',            // --blanc
    limite: '#D2D2CE',           // entre --ligne et --gris, lisible sur blanc
    pays: '#111111',             // --noir
    abidjan: '#FBE0E1',          // --rouge-7
    interieur: '#FEEEC2',        // --jaune-6
  };

  const doux = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const tactile = matchMedia('(pointer: coarse)').matches;
  const conteneur = bloc.querySelector('.rs-carte__map');
  const etat = bloc.querySelector('.rs-carte__etat');
  const aide = bloc.querySelector('.rs-carte__aide');
  const boutons = [...bloc.querySelectorAll('[data-zone]')];
  const nomAbidjan = bloc.dataset.abidjan || 'Grand Abidjan';
  let ordre = [];
  try { ordre = JSON.parse(bloc.dataset.ordre || '[]'); } catch { /* ordre alphabétique à défaut */ }

  const afficherEtat = (cle) => {
    etat.hidden = !cle;
    etat.querySelectorAll('[data-etat]').forEach((el) => { el.hidden = el.dataset.etat !== cle; });
  };

  /* ---- Chargement à la demande ---- */
  const charger = (balise, attributs) => new Promise((ok, ko) => {
    const el = Object.assign(document.createElement(balise), attributs, { crossOrigin: 'anonymous' });
    el.onload = ok;
    el.onerror = () => ko(new Error(`${balise} non chargé`));
    if (balise === 'link') {
      // Placée avant les feuilles du site : à spécificité égale, nos règles gagnent.
      document.head.insertBefore(el, document.head.querySelector('link[rel="stylesheet"]'));
    } else {
      document.head.appendChild(el);
    }
  });

  const json = (url) => fetch(url, { headers: { Accept: 'application/json' } })
    .then((r) => (r.ok ? r.json() : Promise.reject(new Error(`HTTP ${r.status}`))));
  // L'API répond { pins: [...] } ; un tableau plat est aussi accepté.
  const donnees = () => json(bloc.dataset.api)
    .then((d) => (Array.isArray(d) ? d : (Array.isArray(d?.pins) ? d.pins : [])));
  // Contours facultatifs : s'ils manquent, les repères restent lisibles sur fond uni.
  const contours = () => (bloc.dataset.contours ? json(bloc.dataset.contours).catch(() => null) : Promise.resolve(null));

  let lance = false;
  let allerA = null;     // cadrage sur une zone, disponible une fois la carte prête
  let enAttente = null;  // zone demandée avant que la carte soit prête

  const lancer = () => {
    if (lance) return;
    lance = true;
    Promise.all([
      charger('link', { rel: 'stylesheet', href: `${LEAFLET}leaflet.css`, integrity: SRI.css }),
      window.L ? null : charger('script', { src: `${LEAFLET}leaflet.js`, integrity: SRI.js }),
      donnees(),
      contours(),
    ])
      .then(([, , communes, fond]) => construire(communes, fond))
      .catch((e) => { console.warn('[cible/reseau] carte indisponible', e); afficherEtat('erreur'); });
  };

  /* ---- Fond vectoriel : régions, teinte des régions couvertes, frontière ---- */
  // Anneau compact [lat, lng, dlat, dlng, …] (degrés × 1e4) → [[lat, lng], …]
  const decoder = (plat) => {
    const pts = [];
    let lat = 0;
    let lng = 0;
    for (let i = 0; i + 1 < plat.length; i += 2) {
      lat += plat[i];
      lng += plat[i + 1];
      pts.push([lat / 1e4, lng / 1e4]);
    }
    return pts;
  };
  // Point dans un anneau (lancer de rayon) ; dans un polygone = dans l'extérieur, hors des trous.
  const dansAnneau = (lat, lng, anneau) => {
    let dedans = false;
    for (let i = 0, j = anneau.length - 1; i < anneau.length; j = i++) {
      const [ya, xa] = anneau[i];
      const [yb, xb] = anneau[j];
      if ((ya > lat) !== (yb > lat) && lng < ((xb - xa) * (lat - ya)) / (yb - ya) + xa) dedans = !dedans;
    }
    return dedans;
  };
  const dansPolygones = (lat, lng, polygones) => polygones.some((anneaux) => dansAnneau(lat, lng, anneaux[0])
    && !anneaux.slice(1).some((trou) => dansAnneau(lat, lng, trou)));

  function dessinerFond(L, carte, fond, points, estAbidjan) {
    const plein = !TUILES; // sans tuiles, les régions font tout le fond
    const cadrePays = L.latLngBounds([]);
    fond.regions.forEach((region) => {
      const polygones = (region.p || []).map((poly) => poly.map(decoder));
      if (!polygones.length) return;
      const ici = points.filter((c) => dansPolygones(+c.lat, +c.lng, polygones));
      const teinte = ici.some(estAbidjan) ? FOND.abidjan : (ici.length ? FOND.interieur : null);
      polygones.forEach((anneaux) => anneaux[0].forEach((p) => cadrePays.extend(p)));
      if (!plein && !teinte) return;
      L.polygon(polygones, {
        interactive: false,
        stroke: plein,
        color: FOND.limite,
        weight: 1,
        fillColor: teinte || FOND.terre,
        fillOpacity: plein ? 1 : 0.45,
      }).addTo(carte);
    });
    (fond.bord || []).forEach((ligne) => L.polyline(decoder(ligne), {
      interactive: false, color: FOND.pays, weight: 1.6, opacity: 0.6,
    }).addTo(carte));
    carte.attributionControl.addAttribution('Contours <a href="https://www.geoboundaries.org">geoBoundaries</a> (CC BY 4.0)');
    return cadrePays;
  }

  boutons.forEach((b) => b.addEventListener('click', () => {
    if (allerA) allerA(b.dataset.zone);
    else { enAttente = b.dataset.zone; lancer(); }
  }));

  /* ---- Construction de la carte ---- */
  function construire(communes, fond) {
    const L = window.L;
    const points = communes.filter((c) => c && c.lat != null && c.lng != null && Number.isFinite(+c.lat) && Number.isFinite(+c.lng));
    if (!points.length) { afficherEtat('vide'); return; }

    const estAbidjan = (c) => (c.region || '') === 'Abidjan';
    const bornes = (liste) => L.latLngBounds(liste.map((c) => [+c.lat, +c.lng]));
    const zones = {
      abidjan: bornes(points.filter(estAbidjan)),
      interieur: bornes(points.filter((c) => !estAbidjan(c))),
    };
    const carte = L.map(conteneur, {
      minZoom: 6,
      maxZoom: TUILES ? 15 : 13, // sans rues, zoomer plus n'apporte rien
      zoomSnap: 0.25, // cadrage au plus juste : le pays remplit la largeur d'un mobile
      maxBounds: L.latLngBounds([4.3, -8.6], [10.7, -2.5]),
      maxBoundsViscosity: 0.85,
      scrollWheelZoom: false,
      dragging: !tactile,
      zoomAnimation: !doux,
      fadeAnimation: !doux,
      markerZoomAnimation: !doux,
    });

    if (TUILES) {
      L.tileLayer(TUILES, {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> · © <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19,
      }).addTo(carte);
    }
    const pays = (fond && Array.isArray(fond.regions)) ? dessinerFond(L, carte, fond, points, estAbidjan) : null;

    /* ---- Cadrages ----
       « depart » : le pays entier (forme reconnaissable) ; sur écran étroit,
       le pays est contraint par la largeur, on cadre alors sur les communes,
       un cran plus près, pour que les noms tiennent. Sinon : une zone.
       Le cadrage en cours est réappliqué si la carte change de taille
       (rotation d'un téléphone…), tant que le visiteur ne l'a pas déplacée :
       sans cela, maxBounds recentre la vue élargie et le décalage reste. */
    const cadrage = (cle) => {
      const etroit = conteneur.clientWidth < 600;
      if (cle === 'depart') return [!etroit && pays?.isValid() ? pays : bornes(points), { padding: etroit ? [10, 10] : [28, 28] }];
      return [zones[cle], { padding: etroit ? [18, 18] : [28, 28], maxZoom: cle === 'abidjan' ? 13 : 9 }];
    };
    let vue = 'depart'; // cadrage en cours ; null dès que le visiteur a pris la main
    let auto = false;   // mouvement provoqué par le script, pas par le visiteur
    let minuterieAuto = 0;
    const cadrer = (cle, anime = false) => {
      const [b, options] = cadrage(cle);
      if (!b || !b.isValid()) return;
      vue = cle;
      auto = true;
      clearTimeout(minuterieAuto);
      minuterieAuto = setTimeout(() => { auto = false; }, 1500);
      if (anime && !doux) carte.flyToBounds(b, { ...options, duration: 0.8 });
      else carte.fitBounds(b, { ...options, animate: false });
    };
    cadrer('depart');

    // Repère = point coloré + nom, construit en DOM (aucun HTML injecté).
    const reperes = [];
    const creerRepere = (nom, couleur, rang, options = {}) => {
      const el = document.createElement('span');
      el.className = `rs-pin${options.groupe ? ' rs-pin--groupe' : ''}`;
      el.style.setProperty('--c', couleur);
      const point = document.createElement('span');
      point.className = 'rs-pin__point';
      const etiquette = document.createElement('span');
      etiquette.className = 'rs-pin__nom';
      etiquette.textContent = nom;
      el.append(point, etiquette);

      const marqueur = L.marker(options.position, {
        icon: L.divIcon({ html: el, className: 'rs-pin-boite', iconSize: [0, 0] }),
        keyboard: Boolean(options.groupe),
        title: nom,
        riseOnHover: true,
        zIndexOffset: options.groupe ? 1000 : 0,
      });
      reperes.push({ marqueur, el, point, etiquette, rang });
      return marqueur;
    };

    const bulle = (c, abidjan) => {
      const div = document.createElement('div');
      div.className = 'rs-popup';
      div.style.setProperty('--c', abidjan ? COULEUR.abidjan : COULEUR.interieur);
      const nom = document.createElement('strong');
      nom.textContent = c.commune;
      div.append(nom);
      const lieu = (c.city && c.city !== c.commune) ? c.city : (c.region || '');
      if (lieu) {
        const em = document.createElement('em');
        em.textContent = lieu;
        div.append(em);
      }
      const couvert = document.createElement('span');
      couvert.className = 'rs-popup__couvert';
      couvert.textContent = 'Zone couverte par le réseau CIBLE';
      div.append(couvert);
      return div; // c.total n'est volontairement jamais lu
    };

    const rangDe = (nom) => { const i = ordre.indexOf(nom); return i === -1 ? ordre.length : i; };
    const calqueAbidjan = L.layerGroup();
    const calqueInterieur = L.layerGroup().addTo(carte);

    points
      .slice()
      .sort((a, b) => String(a.commune).localeCompare(String(b.commune), 'fr'))
      .forEach((c) => {
        const abidjan = estAbidjan(c);
        const m = creerRepere(c.commune, abidjan ? COULEUR.abidjan : COULEUR.interieur, rangDe(c.commune), { position: [+c.lat, +c.lng] });
        m.bindPopup(() => bulle(c, abidjan), { maxWidth: 260, autoPanPadding: [24, 24] });
        m.addTo(abidjan ? calqueAbidjan : calqueInterieur);
      });

    // Vue d'ensemble : le Grand Abidjan tient en une étiquette, qui y mène.
    const groupe = zones.abidjan.isValid()
      ? creerRepere(nomAbidjan, COULEUR.abidjan, -1, { groupe: true, position: zones.abidjan.getCenter() })
      : null;
    groupe?.on('click', () => allerA('abidjan'));

    /* ---- Placement des étiquettes sans chevauchement ----
       Essais dans l'ordre : à droite, à gauche, au-dessus, au-dessous. */
    const COTES = ['rs-pin--gauche', 'rs-pin--haut', 'rs-pin--bas'];
    const touche = (a, b, m = 2) => a.left < b.right + m && a.right + m > b.left && a.top < b.bottom + m && a.bottom + m > b.top;
    // Un point compte pour son disque coloré, sans son anneau blanc.
    const coeur = (b) => ({ left: b.left + 3, right: b.right - 3, top: b.top + 3, bottom: b.bottom - 3 });
    const placer = () => {
      const cadre = conteneur.getBoundingClientRect();
      const visibles = reperes.filter((r) => carte.hasLayer(r.marqueur)).sort((a, b) => a.rang - b.rang);
      visibles.forEach((r) => r.el.classList.remove(...COTES, 'rs-pin--muet'));
      const pointsPris = visibles.map((r) => ({ r, b: coeur(r.point.getBoundingClientRect()) }));
      const pris = [...conteneur.querySelectorAll('.leaflet-control')].map((c) => c.getBoundingClientRect());
      const libre = (b, r, avecPoints = true) => b.left >= cadre.left + 4 && b.right <= cadre.right - 4
        && b.top >= cadre.top + 4 && b.bottom <= cadre.bottom - 4
        && !(avecPoints && pointsPris.some((p) => p.r !== r && touche(b, p.b)))
        && !pris.some((q) => touche(b, q));
      const essayer = (r, avecPoints) => ['', ...COTES].some((cote) => {
        r.el.classList.remove(...COTES);
        if (cote) r.el.classList.add(cote);
        const b = r.etiquette.getBoundingClientRect();
        if (!libre(b, r, avecPoints)) return false;
        pris.push(b);
        return true;
      });

      visibles.forEach((r) => {
        if (essayer(r, true)) return;
        // « Grand Abidjan » ne disparaît jamais : quitte à couvrir un point
        // voisin (Dabou, Grand-Bassam), qui réapparaît en zoomant.
        if (r.rang < 0 && (essayer(r, false) || (r.el.classList.remove(...COTES), true))) return;
        r.el.classList.remove(...COTES);
        r.el.classList.add('rs-pin--muet');
      });
    };
    let attente = 0;
    const planifier = () => { cancelAnimationFrame(attente); attente = requestAnimationFrame(placer); };

    // Détail d'Abidjan selon le zoom.
    const basculer = () => {
      const detail = carte.getZoom() >= SEUIL_DETAIL;
      if (detail) { if (groupe) carte.removeLayer(groupe); calqueAbidjan.addTo(carte); }
      else { carte.removeLayer(calqueAbidjan); groupe?.addTo(carte); }
      planifier();
    };
    carte.on('zoomend', basculer);
    carte.on('moveend', planifier);
    basculer();
    // Les étiquettes s'élargissent quand la police de titre arrive : on replace.
    document.fonts?.ready.then(planifier);
    document.fonts?.addEventListener?.('loadingdone', planifier);

    /* ---- Raccourcis de zone et reprise en main par le visiteur ---- */
    carte.on('dragstart zoomstart', () => {
      if (auto) return;
      vue = null;
      boutons.forEach((x) => x.setAttribute('aria-pressed', 'false'));
    });
    carte.on('moveend', () => { auto = false; });
    let minuterieTaille = 0;
    carte.on('resize', () => {
      planifier();
      if (!vue) return;
      clearTimeout(minuterieTaille);
      minuterieTaille = setTimeout(() => cadrer(vue), 150);
    });

    allerA = (cle) => {
      if (!zones[cle]?.isValid()) return;
      boutons.forEach((x) => x.setAttribute('aria-pressed', String(x.dataset.zone === cle)));
      carte.closePopup();
      cadrer(cle, true);
    };

    /* ---- Gestes ---- */
    if (tactile) {
      let minuterie = 0;
      conteneur.addEventListener('touchmove', (e) => {
        if (e.touches.length !== 1) { aide?.classList.remove('est-visible'); return; }
        aide?.classList.add('est-visible');
        clearTimeout(minuterie);
        minuterie = setTimeout(() => aide?.classList.remove('est-visible'), 1400);
      }, { passive: true });
    } else {
      // Molette active seulement après un clic sur la carte.
      conteneur.addEventListener('click', () => carte.scrollWheelZoom.enable());
      conteneur.addEventListener('mouseleave', () => carte.scrollWheelZoom.disable());
    }

    afficherEtat(null);
    if (enAttente) allerA(enAttente);
  }

  /* ---- Déclenchement : à l'approche de la carte ---- */
  // ?capture=1 : chargement immédiat pour les captures de contrôle.
  if (/[?&]capture=1\b/.test(location.search) || !('IntersectionObserver' in window)) {
    lancer();
  } else {
    const io = new IntersectionObserver((entrees) => {
      if (entrees.some((e) => e.isIntersecting)) { io.disconnect(); lancer(); }
    }, { rootMargin: '800px 0px' });
    io.observe(bloc);
  }
})();
