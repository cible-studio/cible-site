<?php

/*
|--------------------------------------------------------------------------
| Messages de validation — français
|--------------------------------------------------------------------------
|
| Le site est intégralement francophone mais Laravel n'embarque que les
| messages anglais. Seules les règles réellement utilisées par le
| formulaire /contact sont traduites ici ; toute règle non listée retombe
| sur APP_FALLBACK_LOCALE (en), jamais sur une clé brute.
|
| `attributes` sert à nommer les champs en clair dans les messages :
| sans lui, on obtient « The doc logo field… » au lieu de « Le logo… ».
|
*/

return [

    'accepted'   => 'Merci de cocher le champ :attribute.',
    'array'      => 'Le champ :attribute est invalide.',
    'email'      => 'Merci de saisir une adresse email valide.',
    'extensions' => 'Le fichier :attribute doit être au format : :values.',
    'file'       => 'Le champ :attribute doit être un fichier.',
    'in'         => 'La valeur sélectionnée pour :attribute est invalide.',
    'required'   => 'Le champ :attribute est obligatoire.',
    'string'     => 'Le champ :attribute doit être du texte.',

    'max' => [
        'array'   => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
        'file'    => 'Le fichier :attribute ne doit pas dépasser :max Ko.',
        'numeric' => 'Le champ :attribute ne peut pas être supérieur à :max.',
        'string'  => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],

    'custom' => [
        'website' => [
            'max' => 'Champ invalide.',
        ],
    ],

    // Noms affichés des champs du mini-brief /contact.
    'attributes' => [
        'nom'          => 'nom et prénom',
        'entreprise'   => 'entreprise',
        'poste'        => 'fonction',
        'email'        => 'adresse email',
        'tel'          => 'téléphone',
        'objectif'     => 'objectif',
        'cible'        => 'audience visée',
        'zone'         => 'zone géographique',
        'services'     => 'services souhaités',
        // Les 4 champs ci-dessus sont des tableaux : sans ces entrées
        // génériques, une valeur invalide s'afficherait « objectif.0 ».
        'objectif.*'   => 'objectif',
        'cible.*'      => 'audience visée',
        'zone.*'       => 'zone géographique',
        'services.*'   => 'services souhaités',
        'periode'      => 'période de lancement',
        'budget'       => 'budget',
        'provenance'   => 'origine du contact',
        'message'      => 'description du projet',
        'consentement' => 'consentement',
        'doc_brief'    => 'brief',
        'doc_logo'     => 'logo',
        'doc_charte'   => 'charte graphique',
        'doc_cahier'   => 'cahier des charges',
    ],

];
