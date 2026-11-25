<?php

namespace App\Http\Controllers;

use App\Mail\AdminCodeMail;
use App\Support\AdminAuth;
use App\Support\Contenu;
use App\Support\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Espace d'administration — contenus éditables du site vitrine.
 *
 * Périmètre volontairement étroit : coordonnées, chiffres officiels et
 * réalisations. Tout ce qui touche à la charte (palette, typographie,
 * structure des pages) reste hors de portée : le design est verrouillé, et
 * un champ libre finirait tôt ou tard par le casser.
 */
class AdminController extends Controller
{
    /* ═══════════════════ Authentification ═══════════════════ */

    public function login()
    {
        abort_unless(AdminAuth::configure(), 404);

        return $this->sansCache(response()->view('admin.login'));
    }

    public function authentifier(Request $request)
    {
        abort_unless(AdminAuth::configure(), 404);

        $data = $request->validate([
            'email'    => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'max:200'],
        ]);

        if (!AdminAuth::tenter($data['email'], $data['password'])) {
            AdminAuth::journaliser('connexion_refusee', $request, ['email' => $data['email']]);

            // Message unique : distinguer « email inconnu » de « mot de passe
            // incorrect » confirmerait à un attaquant qu'il a trouvé
            // l'identifiant.
            return back()->withErrors(['email' => 'Identifiants incorrects.']);
        }

        // Mot de passe correct : on NE connecte PAS. Un code part à l'adresse
        // admin ; sans lui, personne n'entre (double authentification).
        // Nouvelle session dès maintenant : un identifiant obtenu avant ce
        // point ne doit pas pouvoir porter la vérification.
        $request->session()->regenerate();

        if (!$this->envoyerCode($request)) {
            return back()->withErrors(['email' => 'Le code de connexion n\'a pas pu être envoyé. Réessayez dans quelques instants.']);
        }

        return redirect()->route('admin.verification');
    }

    /** Page de saisie du code reçu par e-mail. */
    public function verification(Request $request)
    {
        abort_unless(AdminAuth::configure(), 404);

        if (!AdminAuth::codeEnAttente($request)) {
            return redirect()->route('admin.login');
        }

        return $this->sansCache(response()->view('admin.verification', [
            'email'   => $this->masquerEmail((string) config('cible.admin.email')),
            'minutes' => AdminAuth::CODE_MINUTES,
        ]));
    }

    public function verifier(Request $request)
    {
        abort_unless(AdminAuth::configure(), 404);

        $data = $request->validate(['code' => ['required', 'string', 'max:20']]);

        switch (AdminAuth::verifierCode($request, $data['code'])) {
            case 'ok':
                AdminAuth::connecter($request);
                AdminAuth::journaliser('connexion_reussie', $request);
                return redirect()->route('admin.tableau');

            case 'faux':
                AdminAuth::journaliser('code_refuse', $request);
                $reste = AdminAuth::essaisRestants($request);
                return back()->withErrors(['code' => "Code incorrect. Il vous reste $reste essai" . ($reste > 1 ? 's' : '') . '.']);

            case 'expire':
                AdminAuth::journaliser('code_expire', $request);
                return redirect()->route('admin.login')->withErrors(['email' => 'Le code a expiré. Reconnectez-vous pour en recevoir un nouveau.']);

            case 'epuise':
                AdminAuth::journaliser('code_epuise', $request);
                return redirect()->route('admin.login')->withErrors(['email' => 'Trop d\'essais. Reconnectez-vous pour recevoir un nouveau code.']);

            default:   // 'absent' : pas de mot de passe validé dans cette session
                return redirect()->route('admin.login');
        }
    }

    /** Nouveau code, sans ressaisir le mot de passe (déjà validé dans cette session). */
    public function renvoyer(Request $request)
    {
        abort_unless(AdminAuth::configure(), 404);

        if (!AdminAuth::codeEnAttente($request)) {
            return redirect()->route('admin.login');
        }

        if (!$this->envoyerCode($request)) {
            return back()->withErrors(['code' => 'Le code n\'a pas pu être renvoyé. Réessayez dans quelques instants.']);
        }

        return redirect()->route('admin.verification')->with('info', 'Un nouveau code vous a été envoyé. Seul le dernier reçu est valable.');
    }

    /**
     * Crée un code et l'envoie à l'adresse admin. En cas d'échec d'envoi, le
     * code est détruit : l'accès reste fermé, il n'existe aucune autre voie.
     */
    private function envoyerCode(Request $request): bool
    {
        $code = AdminAuth::preparerCode($request);

        try {
            Mail::to((string) config('cible.admin.email'))->send(new AdminCodeMail(
                code:    $code,
                minutes: AdminAuth::CODE_MINUTES,
                quand:   now()->timezone('Africa/Abidjan')->format('d/m/Y à H:i'),
                ip:      (string) $request->ip(),
            ));
        } catch (\Throwable $e) {
            AdminAuth::annulerCode($request);
            AdminAuth::journaliser('code_non_envoye', $request, ['erreur' => $e->getMessage()]);
            return false;
        }

        AdminAuth::journaliser('code_envoye', $request);

        return true;
    }

    /** « studio@cible-ci.com » → « st••••@cible-ci.com » : on rappelle où chercher sans afficher l'adresse. */
    private function masquerEmail(string $email): string
    {
        [$nom, $domaine] = array_pad(explode('@', $email, 2), 2, '');

        return mb_substr($nom, 0, 2) . str_repeat('•', max(2, mb_strlen($nom) - 2)) . '@' . $domaine;
    }

    /** Pages d'accès à l'admin : ni indexées, ni gardées en cache par un intermédiaire. */
    private function sansCache($reponse)
    {
        return $reponse->header('X-Robots-Tag', 'noindex, nofollow, noarchive')
                       ->header('Cache-Control', 'no-store, private');
    }

    public function deconnexion(Request $request)
    {
        AdminAuth::deconnecter($request);

        return redirect()->route('admin.login')->with('info', 'Vous êtes déconnecté.');
    }

    /* ═══════════════════ Tableau de bord ═══════════════════ */

    public function tableau()
    {
        return view('admin.tableau', [
            'stockage'   => Contenu::stockageDisponible(),
            'persistant' => Contenu::stockagePersistant(),
            'surchargees'=> collect(Contenu::sections())
                ->mapWithKeys(fn ($s) => [$s => Contenu::estSurchargee($s)])
                ->all(),
        ]);
    }

    /* ═══════════════════ Coordonnées ═══════════════════ */

    public function coordonnees()
    {
        return view('admin.coordonnees', ['valeurs' => Contenu::section('coordonnees')]);
    }

    public function enregistrerCoordonnees(Request $request)
    {
        $data = $request->validate([
            'tel_mobile'        => ['required', 'string', 'max:30'],
            'tel_fixe'          => ['nullable', 'string', 'max:30'],
            'email_commercial'  => ['required', 'email', 'max:150'],
            'email_secretariat' => ['nullable', 'email', 'max:150'],
            'adresse_rue'       => ['required', 'string', 'max:150'],
            'adresse_complement'=> ['nullable', 'string', 'max:200'],
        ]);

        return $this->persister('coordonnees', $data, $request);
    }

    /* ═══════════════════ Chiffres clés ═══════════════════ */

    public function chiffres()
    {
        return view('admin.chiffres', ['valeurs' => Contenu::section('chiffres')]);
    }

    public function enregistrerChiffres(Request $request)
    {
        // Chiffres seuls : le « + » de « +400 » est ajouté par les vues, il
        // ne doit pas être saisi ici — les compteurs animés écrasent le
        // contenu de leur cible et l'effaceraient.
        $data = $request->validate([
            'panneaux'     => ['required', 'digits_between:1,6'],
            'communes'     => ['required', 'digits_between:1,4'],
            'annees'       => ['required', 'digits_between:1,3'],
            'distinctions' => ['required', 'digits_between:1,2'],
        ]);

        return $this->persister('chiffres', $data, $request);
    }

    /* ═══════════════════ Réalisations ═══════════════════ */

    public function realisations()
    {
        return view('admin.realisations', ['projets' => Contenu::section('realisations')]);
    }

    public function editerRealisation(string $slug)
    {
        $projets = Contenu::section('realisations');
        abort_unless(isset($projets[$slug]), 404);

        return view('admin.realisation-edition', [
            'slug'   => $slug,
            'projet' => $projets[$slug],
        ]);
    }

    public function enregistrerRealisation(Request $request, string $slug)
    {
        $projets = Contenu::section('realisations');
        abort_unless(isset($projets[$slug]), 404);

        $palette = ['var(--rouge)', 'var(--jaune)', 'var(--vert)', 'var(--bleu)', 'var(--violet)'];

        $data = $request->validate([
            'nom'      => ['required', 'string', 'max:80'],
            'cat'      => ['required', 'string', 'max:120'],
            'titre'    => ['required', 'string', 'max:200'],
            'texte'    => ['required', 'string', 'max:1200'],
            'services' => ['required', 'string', 'max:300'],
            // La couleur est contrainte à la palette : un champ libre
            // permettrait d'introduire une 6e teinte hors charte.
            'couleur'  => ['required', Rule::in($palette)],
            'image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'couleur.in'  => 'Couleur hors palette.',
            'image.image' => 'Le visuel doit être une image.',
            'image.max'   => 'Le visuel ne doit pas dépasser 5 Mo.',
        ]);

        $projet = $projets[$slug];

        foreach (['nom', 'cat', 'titre', 'texte', 'services', 'couleur'] as $champ) {
            $projet[$champ] = $data[$champ];
        }

        if ($request->hasFile('image')) {
            $chemin = $this->stockerVisuel($request->file('image'), $slug);

            if ($chemin === null) {
                return back()->withInput()->withErrors([
                    'image' => "Le visuel n'a pas pu être enregistré. Vérifiez le stockage persistant.",
                ]);
            }

            $projet['image'] = $chemin;
        }

        $projets[$slug] = $projet;

        return $this->persister('realisations', $projets, $request, route('admin.realisations'));
    }

    /* ═══════════════════ Pages pilotées par le schéma ═══════════════════ */

    public function page(string $cle)
    {
        abort_unless(Schema::page($cle) !== null, 404);

        return view('admin.page', [
            'cle'     => $cle,
            'schema'  => Schema::page($cle),
            'valeurs' => Contenu::section($cle),
        ]);
    }

    public function enregistrerPage(Request $request, string $cle)
    {
        abort_unless(Schema::page($cle) !== null, 404);

        // Règles déduites du schéma : ajouter un champ ne demande donc
        // aucune modification ici.
        $data = $request->validate(Schema::regles($cle));

        $valeurs = Contenu::section($cle);

        foreach ($data as $champ => $valeur) {
            $valeurs[$champ] = $valeur;
        }

        // Les images arrivent en fichier : on ne remplace que celles qui ont
        // effectivement été redéposées, les autres gardent leur visuel.
        foreach (Schema::images($cle) as $champ => $def) {
            if (!$request->hasFile($champ)) {
                continue;
            }

            $request->validate([
                $champ => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ], [
                $champ . '.image' => 'Le visuel doit être une image.',
                $champ . '.max'   => 'Le visuel ne doit pas dépasser 5 Mo.',
            ]);

            $chemin = $this->stockerVisuel($request->file($champ), $cle . '-' . $champ);

            if ($chemin === null) {
                return back()->withInput()->withErrors([
                    $champ => "Le visuel n'a pas pu être enregistré. Vérifiez le stockage persistant.",
                ]);
            }

            $valeurs[$champ] = $chemin;
        }

        return $this->persister($cle, $valeurs, $request);
    }

    /* ═══════════════════ Réinitialisation ═══════════════════ */

    public function reinitialiser(Request $request, string $section)
    {
        abort_unless(in_array($section, Contenu::sections(), true), 404);

        Contenu::reinitialiser($section);
        AdminAuth::journaliser('reinitialisation', $request, ['section' => $section]);

        return back()->with('ok', 'Section revenue au contenu d\'origine du site.');
    }

    /* ═══════════════════ Utilitaires ═══════════════════ */

    private function persister(string $section, array $valeurs, Request $request, ?string $vers = null)
    {
        // Un navigateur envoie toujours de l'UTF-8 sur une page UTF-8, mais un
        // copier-coller depuis un outil exotique peut glisser un octet
        // invalide. Sans ce garde-fou, l'encodage JSON le remplacerait par un
        // « � » et l'utilisateur découvrirait la corruption sur le site.
        if (!$this->encodageValide($valeurs)) {
            return back()->withInput()->withErrors([
                'stockage' => 'Un caractère non reconnu a été détecté. Retapez le texte concerné plutôt que de le coller depuis un document.',
            ]);
        }

        if (!Contenu::enregistrer($section, $valeurs)) {
            return back()->withInput()->withErrors([
                'stockage' => "Enregistrement impossible : le stockage persistant n'est pas accessible en écriture. Les modifications n'ont PAS été conservées.",
            ]);
        }

        AdminAuth::journaliser('modification', $request, ['section' => $section]);

        return ($vers ? redirect($vers) : back())->with('ok', 'Modifications enregistrées et visibles sur le site.');
    }

    /** Toutes les chaînes du tableau sont-elles en UTF-8 valide ? */
    private function encodageValide(array $valeurs): bool
    {
        foreach ($valeurs as $valeur) {
            if (is_array($valeur)) {
                if (!$this->encodageValide($valeur)) {
                    return false;
                }
            } elseif (is_string($valeur) && !mb_check_encoding($valeur, 'UTF-8')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Stocke un visuel de réalisation sur le volume persistant.
     *
     * Les images ne vont PAS dans public/ : ce dossier est reconstruit à
     * chaque déploiement depuis git, un fichier téléversé y disparaîtrait.
     * Elles sont servies par la route admin.visuel.
     */
    private function stockerVisuel($fichier, string $slug): ?string
    {
        try {
            $nom = $slug . '-' . Str::random(8) . '.' . $fichier->getClientOriginalExtension();
            $fichier->storeAs('visuels', $nom, 'contenu');

            return 'visuel:' . $nom;
        } catch (\Throwable $e) {
            Log::error('cible.admin.visuel_non_stocke', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
