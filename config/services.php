<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CIBLE CI — services vitrine
    |--------------------------------------------------------------------------
    | gtm_id : conteneur Google Tag Manager (format GTM-XXXXXXX).
    |          Prioritaire : s'il est renseigné, la balise GA4 directe
    |          n'est PAS chargée — GA4 doit alors être configuré dans GTM.
    | ga_id  : ID de mesure Google Analytics 4 (format G-XXXXXXXXXX).
    |          Secours, utilisé seulement quand gtm_id est vide.
    | Les deux vides sur dev/local → aucun script chargé.
    */
    'cible' => [
        'gtm_id' => env('CIBLE_GTM_ID'),
        'ga_id'  => env('CIBLE_GA_ID'),
    ],

    /*
    | Cloudflare Turnstile — anti-robot du formulaire /contact.
    | Tant que les deux clés sont vides, le widget n'est pas rendu et la
    | vérification laisse passer : le site fonctionne à l'identique. La mise
    | en service ne demande que ces deux variables dans Coolify, sans
    | redéploiement de code. Clés à créer sur dash.cloudflare.com → Turnstile.
    */
    'turnstile' => [
        'site_key'   => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

];
