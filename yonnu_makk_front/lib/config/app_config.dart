// ─── PARAMÈTRES GLOBAUX DE L'APPLICATION ───────────────────────────────────
// Modifiez ici pour pointer vers votre back-end (dev / prod).

class AppConfig {
  AppConfig._();

  // Nom & version
  static const appName    = 'Yoonu Jigeen';
  static const appVersion = '1.0.0';

  // URL de base de l'API Laravel
  static const apiBaseUrl = 'https://yoonu-makk-1.onrender.com/api';

  // Durée d'attente maximale pour chaque requête
  static const requestTimeout = Duration(seconds: 15);

  // Langues disponibles dans l'app
  static const langues = ['fr', 'wo', 'pu'];
}
