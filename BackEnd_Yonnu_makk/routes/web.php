<?php

use App\Http\Controllers\Web\Admin\CategorieController;
use App\Http\Controllers\Web\Admin\ContenuController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\DemandeAdhesionController;
use App\Http\Controllers\Web\Admin\GynecologueController;
use App\Http\Controllers\Web\Admin\NotificationController;
use App\Http\Controllers\Web\Admin\RendezVousController;
use App\Http\Controllers\Web\Admin\StatistiqueController;
use App\Http\Controllers\Web\Admin\UtilisateurController;
use App\Http\Controllers\Web\Admin\VideoController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Gynecologue\Auth\LoginController as ProLoginController;
use App\Http\Controllers\Web\Gynecologue\DashboardController as ProDashboardController;
use App\Http\Controllers\Web\Gynecologue\NotificationController as ProNotificationController;
use App\Http\Controllers\Web\Gynecologue\PatienteController as ProPatienteController;
use App\Http\Controllers\Web\Gynecologue\ProfilController as ProProfilController;
use App\Http\Controllers\Web\Gynecologue\RendezVousController as ProRendezVousController;
use Illuminate\Support\Facades\Route;

Route::view('/assistant-ia', 'assistant')->name('assistant');
Route::view('/agenda',       'agenda')->name('agenda');

// ─── AUTHENTIFICATION ESPACE WEB (session) ───────────────────────────────────
Route::get('/connexion',  [LoginController::class, 'show'])->name('login');
Route::post('/connexion', [LoginController::class, 'store'])->name('login.store');
Route::post('/deconnexion', [LoginController::class, 'destroy'])->name('logout');

// ─── ESPACE ADMINISTRATION (web, session) ────────────────────────────────────
Route::middleware('admin.web')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Statistiques
    Route::get('statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');

    // Utilisateurs
    Route::get('utilisateurs',            [UtilisateurController::class, 'index'])->name('utilisateurs.index');
    Route::get('utilisateurs/{id}',       [UtilisateurController::class, 'show'])->name('utilisateurs.show');
    Route::delete('utilisateurs/{id}',    [UtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');

    // Gynécologues
    Route::get('gynecologues',                  [GynecologueController::class, 'index'])->name('gynecologues.index');
    Route::get('gynecologues/{id}',             [GynecologueController::class, 'show'])->name('gynecologues.show');
    Route::delete('gynecologues/{id}',          [GynecologueController::class, 'destroy'])->name('gynecologues.destroy');
    Route::patch('gynecologues/{id}/activer',    [GynecologueController::class, 'activer'])->name('gynecologues.activer');
    Route::patch('gynecologues/{id}/desactiver', [GynecologueController::class, 'desactiver'])->name('gynecologues.desactiver');

    // Demandes d'adhésion
    Route::get('demandes',                 [DemandeAdhesionController::class, 'index'])->name('demandes.index');
    Route::get('demandes/{id}',            [DemandeAdhesionController::class, 'show'])->name('demandes.show');
    Route::patch('demandes/{id}/approuver', [DemandeAdhesionController::class, 'approuver'])->name('demandes.approuver');
    Route::patch('demandes/{id}/rejeter',   [DemandeAdhesionController::class, 'rejeter'])->name('demandes.rejeter');

    // Rendez-vous
    Route::get('rendez-vous', [RendezVousController::class, 'index'])->name('rendez-vous.index');

    // Contenus (articles)
    Route::resource('contenus', ContenuController::class)->except(['show']);

    // Catégories
    Route::resource('categories', CategorieController::class)->except(['show']);

    // Vidéos
    Route::resource('videos', VideoController::class)->except(['show']);

    // Notifications
    Route::get('notifications',         [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/envoyer', [NotificationController::class, 'envoyer'])->name('notifications.envoyer');
});

// ─── AUTHENTIFICATION ESPACE GYNÉCOLOGUE (web, session) ──────────────────────
Route::get('/pro/connexion',    [ProLoginController::class, 'show'])->name('pro.login');
Route::post('/pro/connexion',   [ProLoginController::class, 'store'])->name('pro.login.store');
Route::post('/pro/deconnexion', [ProLoginController::class, 'destroy'])->name('pro.logout');

// ─── ESPACE GYNÉCOLOGUE (web, session) ───────────────────────────────────────
Route::middleware('gynecologue.web')->prefix('pro')->name('pro.')->group(function () {
    Route::get('/', [ProDashboardController::class, 'index'])->name('dashboard');

    // Patientes
    Route::get('patientes',      [ProPatienteController::class, 'index'])->name('patientes.index');
    Route::get('patientes/{id}', [ProPatienteController::class, 'show'])->name('patientes.show');

    // Rendez-vous
    Route::get('rendez-vous',                  [ProRendezVousController::class, 'index'])->name('rendez-vous.index');
    Route::get('rendez-vous/events',           [ProRendezVousController::class, 'events'])->name('rendez-vous.events');
    Route::get('rendez-vous/{id}',             [ProRendezVousController::class, 'show'])->name('rendez-vous.show');
    Route::patch('rendez-vous/{id}/accepter',  [ProRendezVousController::class, 'accepter'])->name('rendez-vous.accepter');
    Route::patch('rendez-vous/{id}/refuser',   [ProRendezVousController::class, 'refuser'])->name('rendez-vous.refuser');
    Route::patch('rendez-vous/{id}/terminer',  [ProRendezVousController::class, 'terminer'])->name('rendez-vous.terminer');
    Route::post('rendez-vous/{id}/recommander', [ProRendezVousController::class, 'recommander'])->name('rendez-vous.recommander');

    // Profil
    Route::get('profil',        [ProProfilController::class, 'edit'])->name('profil.edit');
    Route::put('profil',        [ProProfilController::class, 'update'])->name('profil.update');
    Route::put('mot-de-passe',  [ProProfilController::class, 'changerMotDePasse'])->name('profil.mot-de-passe');

    // Notifications
    Route::get('notifications',          [ProNotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{id}/lu', [ProNotificationController::class, 'marquerLu'])->name('notifications.lu');
});

Route::get('/', function () {
    $frontendUrl = env('FRONTEND_URL');
    $frontendUrl = filter_var($frontendUrl, FILTER_VALIDATE_URL) ? $frontendUrl : null;

    $chatbotUrl = env('FRONTEND_CHATBOT_URL');
    $chatbotUrl = filter_var($chatbotUrl, FILTER_VALIDATE_URL)
        ? $chatbotUrl
        : route('assistant');

    return view('welcome', [
        'frontendUrl' => $frontendUrl,
        'chatbotUrl' => $chatbotUrl,
    ]);
});
