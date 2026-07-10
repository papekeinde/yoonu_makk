<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes API — YOONU MAKK
|--------------------------------------------------------------------------
|
| Préfixe automatique : /api
|
| Guards :
|   auth:sanctum        → patients & admins (table users)
|   auth:gynecologue    → gynécologues (table gynecologues)
|
| Middleware alias :
|   role:patient        → vérifie users.role = 'patient'
|   role:admin          → vérifie users.role = 'admin'
|   gynecologue.actif   → vérifie gynecologues.is_active = true
|   email.verifie       → vérifie email_verified_at non nul
|
*/

// Alias pour la notification Laravel VerifyEmail (qui cherche le nom "verification.verify")
Route::get('email/verify/{id}/{hash}', [\App\Http\Controllers\Api\Auth\VerificationEmailController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

// ─── AUDIO CHATBOT (public, pas d'auth) ─────────────────────────────────────
Route::get('public/audio/chatbot/{id}', function (string $id) {
    // Sécurité : uniquement alphanumérique + tiret-bas
    if (! preg_match('/^[a-z0-9_]+$/i', $id)) {
        abort(404);
    }

    $mimes = ['mp3' => 'audio/mpeg', 'm4a' => 'audio/mp4', 'aac' => 'audio/aac', 'ogg' => 'audio/ogg'];

    foreach (array_keys($mimes) as $ext) {
        $path = storage_path('app/public/chatbot_audio/' . $id . '.' . $ext);
        if (file_exists($path)) {
            return response()->file($path, [
                'Content-Type'  => $mimes[$ext],
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
    }

    abort(404);
});

// ─── AUTH PATIENTS ───────────────────────────────────────────────────────────
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('inscription', [\App\Http\Controllers\Api\Auth\InscriptionController::class, 'store'])
        ->middleware('throttle:auth-inscription')
        ->name('inscription');
    Route::post('connexion',   [\App\Http\Controllers\Api\Auth\ConnexionController::class,   'store'])
        ->middleware('throttle:auth-connexion')
        ->name('connexion');

    Route::post('email/verify/{id}/{hash}', [\App\Http\Controllers\Api\Auth\VerificationEmailController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend',             [\App\Http\Controllers\Api\Auth\VerificationEmailController::class, 'resend'])->middleware('auth:sanctum')->name('verification.resend');

    Route::post('mot-de-passe/email',       [\App\Http\Controllers\Api\Auth\MotDePasseController::class, 'sendLink'])
        ->middleware('throttle:auth-password')
        ->name('password.email');
    Route::post('mot-de-passe/reset',       [\App\Http\Controllers\Api\Auth\MotDePasseController::class, 'reset'])
        ->middleware('throttle:auth-password')
        ->name('password.reset');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('deconnexion', [\App\Http\Controllers\Api\Auth\ConnexionController::class,    'destroy'])->name('deconnexion');
        Route::post('refresh',     [\App\Http\Controllers\Api\Auth\RefreshTokenController::class, 'refresh'])->name('refresh');
    });
});

// ─── AUTH GYNÉCOLOGUES ────────────────────────────────────────────────────────
Route::prefix('gynecologue/auth')->name('gynecologue.auth.')->group(function () {
    Route::post('connexion',   [\App\Http\Controllers\Api\Gynecologue\Auth\ConnexionGynecologueController::class, 'store'])
        ->middleware('throttle:auth-connexion')
        ->name('connexion');
    Route::middleware('auth:gynecologue')->group(function () {
        Route::post('deconnexion', [\App\Http\Controllers\Api\Gynecologue\Auth\ConnexionGynecologueController::class,    'destroy'])->name('deconnexion');
        Route::post('refresh',     [\App\Http\Controllers\Api\Gynecologue\Auth\RefreshTokenGynecologueController::class, 'refresh'])->name('refresh');
    });
});

// ─── ESPACE PATIENT ───────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:patient', 'email.verifie'])->prefix('patient')->name('patient.')->group(function () {

    Route::get('profil',    [\App\Http\Controllers\Api\Patient\ProfilController::class, 'show'])->name('profil.show');
    Route::put('profil',    [\App\Http\Controllers\Api\Patient\ProfilController::class, 'update'])->name('profil.update');
    Route::post('avatar',   [\App\Http\Controllers\Api\Patient\ProfilController::class, 'avatar'])->name('profil.avatar');

    Route::apiResource('symptomes',  \App\Http\Controllers\Api\Patient\SymptomeController::class);

    Route::apiResource('rendez-vous', \App\Http\Controllers\Api\Patient\RendezVousController::class)->only(['index', 'store', 'show', 'destroy']);

    Route::get('recommandations',        [\App\Http\Controllers\Api\Patient\RecommandationController::class, 'index'])->name('recommandations.index');
    Route::patch('recommandations/{id}/lu', [\App\Http\Controllers\Api\Patient\RecommandationController::class, 'marquerLu'])->name('recommandations.lu');

    Route::get('notifications',            [\App\Http\Controllers\Api\Patient\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{id}/lu',  [\App\Http\Controllers\Api\Patient\NotificationController::class, 'marquerLu'])->name('notifications.lu');

    Route::get('chatbot/historique',   [\App\Http\Controllers\Api\Patient\ChatbotController::class, 'historique'])->name('chatbot.historique');
    Route::post('chatbot',             [\App\Http\Controllers\Api\Patient\ChatbotController::class, 'envoyer'])->name('chatbot.envoyer');
    Route::post('chatbot/audio',       [\App\Http\Controllers\Api\Patient\ChatbotController::class, 'envoyerAudio'])->name('chatbot.audio');

    // ─── GROSSESSE ────────────────────────────────────────────────────────────
    Route::prefix('grossesse')->name('grossesse.')->group(function () {
        Route::get('/',        [\App\Http\Controllers\Api\Patient\GrossesseController::class, 'show'])->name('show');
        Route::post('/',       [\App\Http\Controllers\Api\Patient\GrossesseController::class, 'store'])->name('store');
        Route::patch('cloturer', [\App\Http\Controllers\Api\Patient\GrossesseController::class, 'terminer'])->name('terminer');

        Route::get('suivis',       [\App\Http\Controllers\Api\Patient\SuiviGrossesseController::class, 'index'])->name('suivis.index');
        Route::post('suivis',      [\App\Http\Controllers\Api\Patient\SuiviGrossesseController::class, 'store'])->name('suivis.store');
        Route::delete('suivis/{id}', [\App\Http\Controllers\Api\Patient\SuiviGrossesseController::class, 'destroy'])->name('suivis.destroy');

        Route::get('mouvements',         [\App\Http\Controllers\Api\Patient\MouvementBebeController::class, 'index'])->name('mouvements.index');
        Route::post('mouvements',        [\App\Http\Controllers\Api\Patient\MouvementBebeController::class, 'store'])->name('mouvements.store');
        Route::delete('mouvements/{id}', [\App\Http\Controllers\Api\Patient\MouvementBebeController::class, 'destroy'])->name('mouvements.destroy');
        Route::get('mouvements/resume-jour', [\App\Http\Controllers\Api\Patient\MouvementBebeController::class, 'resumeJour'])->name('mouvements.resume');
    });
});

// ─── ESPACE GYNÉCOLOGUE ───────────────────────────────────────────────────────
Route::middleware(['auth:gynecologue', 'gynecologue.actif'])->prefix('gynecologue')->name('gynecologue.')->group(function () {

    Route::get('profil',           [\App\Http\Controllers\Api\Gynecologue\ProfilGynecologueController::class, 'show'])->name('profil.show');
    Route::put('profil',           [\App\Http\Controllers\Api\Gynecologue\ProfilGynecologueController::class, 'update'])->name('profil.update');
    Route::put('mot-de-passe',     [\App\Http\Controllers\Api\Gynecologue\ProfilGynecologueController::class, 'changerMotDePasse'])->name('profil.mot-de-passe');

    Route::get('rendez-vous',                      [\App\Http\Controllers\Api\Gynecologue\RendezVousGynecologueController::class, 'index'])->name('rendez-vous.index');
    Route::get('rendez-vous/{id}',                 [\App\Http\Controllers\Api\Gynecologue\RendezVousGynecologueController::class, 'show'])->name('rendez-vous.show');
    Route::patch('rendez-vous/{id}/accepter',      [\App\Http\Controllers\Api\Gynecologue\RendezVousGynecologueController::class, 'accepter'])->name('rendez-vous.accepter');
    Route::patch('rendez-vous/{id}/refuser',       [\App\Http\Controllers\Api\Gynecologue\RendezVousGynecologueController::class, 'refuser'])->name('rendez-vous.refuser');
    Route::patch('rendez-vous/{id}/terminer',      [\App\Http\Controllers\Api\Gynecologue\RendezVousGynecologueController::class, 'terminer'])->name('rendez-vous.terminer');

    Route::get('patientes',                        [\App\Http\Controllers\Api\Gynecologue\PatienteController::class, 'index'])->name('patientes.index');
    Route::post('patientes/ajouter',               [\App\Http\Controllers\Api\Gynecologue\PatienteController::class, 'ajouterPatiente'])->name('patientes.ajouter');
    Route::post('patientes/creer',                 [\App\Http\Controllers\Api\Gynecologue\PatienteController::class, 'creerPatiente'])->name('patientes.creer');
    Route::delete('patientes/{femmeId}/retirer',   [\App\Http\Controllers\Api\Gynecologue\PatienteController::class, 'retirerPatiente'])->name('patientes.retirer');
    Route::get('patientes/{femmeId}/symptomes',    [\App\Http\Controllers\Api\Gynecologue\PatienteController::class, 'symptomes'])->name('patientes.symptomes');
    Route::post('patientes/{femmeId}/recommandations', [\App\Http\Controllers\Api\Gynecologue\PatienteController::class, 'recommander'])->name('patientes.recommander');
    Route::get('patientes/{femmeId}/grossesse',    [\App\Http\Controllers\Api\Gynecologue\PatienteController::class, 'grossesse'])->name('patientes.grossesse');

    Route::get('notifications',           [\App\Http\Controllers\Api\Gynecologue\NotificationGynecologueController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{id}/lu', [\App\Http\Controllers\Api\Gynecologue\NotificationGynecologueController::class, 'marquerLu'])->name('notifications.lu');
});

// ─── ESPACE ADMIN ─────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('api.admin.')->group(function () {

    Route::apiResource('utilisateurs',   \App\Http\Controllers\Api\Admin\UtilisateurController::class);
    Route::apiResource('gynecologues',   \App\Http\Controllers\Api\Admin\GynecologueAdminController::class);
    Route::patch('gynecologues/{id}/activer',    [\App\Http\Controllers\Api\Admin\GynecologueAdminController::class, 'activer'])->name('gynecologues.activer');
    Route::patch('gynecologues/{id}/desactiver', [\App\Http\Controllers\Api\Admin\GynecologueAdminController::class, 'desactiver'])->name('gynecologues.desactiver');

    Route::get('demandes-adhesion',              [\App\Http\Controllers\Api\Admin\DemandeAdhesionController::class, 'index'])->name('demandes.index');
    Route::get('demandes-adhesion/{id}',         [\App\Http\Controllers\Api\Admin\DemandeAdhesionController::class, 'show'])->name('demandes.show');
    Route::patch('demandes-adhesion/{id}/approuver', [\App\Http\Controllers\Api\Admin\DemandeAdhesionController::class, 'approuver'])->name('demandes.approuver');
    Route::patch('demandes-adhesion/{id}/rejeter',   [\App\Http\Controllers\Api\Admin\DemandeAdhesionController::class, 'rejeter'])->name('demandes.rejeter');

    Route::apiResource('categories',     \App\Http\Controllers\Api\Admin\CategorieContenuController::class);
    Route::apiResource('contenus',       \App\Http\Controllers\Api\Admin\ContenuController::class);
    Route::apiResource('videos',         \App\Http\Controllers\Api\Admin\VideoController::class);

    Route::get('statistiques',           [\App\Http\Controllers\Api\Admin\StatistiqueController::class, 'index'])->name('statistiques');
    Route::get('rendez-vous',            [\App\Http\Controllers\Api\Admin\RendezVousAdminController::class, 'index'])->name('rendez-vous.index');
    Route::post('notifications/envoyer', [\App\Http\Controllers\Api\Admin\NotificationAdminController::class, 'envoyer'])->name('notifications.envoyer');
});

// ─── CONTENUS PUBLICS (sans auth) ────────────────────────────────────────────
Route::prefix('contenus')->name('contenus.')->group(function () {
    Route::get('/',              [\App\Http\Controllers\Api\Public\ContenuPublicController::class, 'index'])->name('index');
    Route::get('categories',     [\App\Http\Controllers\Api\Public\ContenuPublicController::class, 'categories'])->name('categories');
    Route::get('videos',         [\App\Http\Controllers\Api\Public\VideoPublicController::class,   'index'])->name('videos.index');
    Route::get('videos/{slug}',  [\App\Http\Controllers\Api\Public\VideoPublicController::class, 'show'])->name('videos.show');
    Route::get('{slug}',         [\App\Http\Controllers\Api\Public\ContenuPublicController::class, 'show'])->name('show');
});

// ─── DEMANDE D'ADHÉSION (publique) ───────────────────────────────────────────
Route::post('demandes-adhesion', [\App\Http\Controllers\Api\Public\DemandeAdhesionPublicController::class, 'store'])->name('demandes.store');

// ─── RECHERCHE GYNÉCOLOGUES (publique) ───────────────────────────────────────
Route::get('gynecologues', [\App\Http\Controllers\Api\Public\GynecologuePublicController::class, 'index'])->name('gynecologues.index');
Route::get('gynecologues/{id}', [\App\Http\Controllers\Api\Public\GynecologuePublicController::class, 'show'])->name('gynecologues.show');


// ─── INTERMÉDIATION ──────────────────────────────────────────────────────────
// Public : parcourir les professionnels
Route::prefix('intermediation')->name('intermediation.')->group(function () {

    Route::get('professionnels',      [\App\Http\Controllers\Api\Intermediation\ProfessionnelController::class, 'index'])->name('professionnels.index');
    Route::get('professionnels/{professionnel}', [\App\Http\Controllers\Api\Intermediation\ProfessionnelController::class, 'show'])->name('professionnels.show');

    // Authentifié : femme patiente
    Route::middleware('auth:sanctum')->group(function () {

        // Demandes côté femme
        Route::get   ('demandes',        [\App\Http\Controllers\Api\Intermediation\DemandeIntermediationController::class, 'index'])->name('demandes.index');
        Route::post  ('demandes',        [\App\Http\Controllers\Api\Intermediation\DemandeIntermediationController::class, 'store'])->name('demandes.store');
        Route::delete('demandes/{demande}', [\App\Http\Controllers\Api\Intermediation\DemandeIntermediationController::class, 'destroy'])->name('demandes.destroy');

        // Avis
        Route::post  ('avis',            [\App\Http\Controllers\Api\Intermediation\AvisController::class, 'store'])->name('avis.store');

    });

    // Côté professionnel (gynecologue connecté)
    Route::middleware(['auth:gynecologue', 'gynecologue.actif'])->prefix('pro')->name('pro.')->group(function () {
        Route::get ('demandes',              [\App\Http\Controllers\Api\Intermediation\DemandeIntermediationController::class, 'indexPro'])->name('demandes.index');
        Route::put ('demandes/{demande}',    [\App\Http\Controllers\Api\Intermediation\DemandeIntermediationController::class, 'updatePro'])->name('demandes.update');
    });
});
