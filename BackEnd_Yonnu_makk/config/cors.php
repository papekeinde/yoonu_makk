<?php

/**
 * SECURITE (OWASP A05) — Configuration CORS restrictive.
 *
 * En développement : on autorise localhost + 127.0.0.1 (app Flutter web).
 * En production : remplacer 'allowed_origins' par les domaines exacts de l'app.
 * Ne jamais utiliser '*' en production pour une API avec authentification.
 */
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // SECURITE: restreindre aux origines connues uniquement
    // Ajouter ici le domaine de production quand déployé
    'allowed_origins' => [
        'http://localhost:51995',   // Flutter web dev (port fixe)
        'http://127.0.0.1:51995',
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://localhost:8080',
        'http://127.0.0.1:8080',
    ],

    // Pattern pour couvrir tous les ports localhost en développement Flutter web
    // (flutter run --web génère un port aléatoire à chaque lancement)
    'allowed_origins_patterns' => [
        '#^http://localhost:\d+$#',
        '#^http://127\.0\.0\.1:\d+$#',
    ],

    'allowed_headers' => ['Content-Type', 'Accept', 'Authorization', 'X-Requested-With'],

    'exposed_headers' => [],

    'max_age' => 86400,

    // Mettre true uniquement si on utilise les cookies Sanctum stateful
    'supports_credentials' => false,
];
