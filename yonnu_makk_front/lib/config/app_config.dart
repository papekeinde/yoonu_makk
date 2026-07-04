// ─── PARAMÈTRES GLOBAUX DE L'APPLICATION ───────────────────────────────────
// Modifiez ici pour pointer vers votre back-end (dev / prod).

class AppConfig {
  AppConfig._();

  // Nom & version
  static const appName    = 'Yoonu Jigeen';
  static const appVersion = '1.0.0';

  // URL de base de l'API Laravel
  // • Android émulateur : 10.0.2.2 = localhost de la machine hôte
  // • iOS simulateur / Web : 127.0.0.1
  // • Appareil physique   : adresse IP locale de votre machine (ex: 192.168.x.x)
  static const apiBaseUrl = 'http://10.0.2.2:8000/api';

  // Durée d'attente maximale pour chaque requête
  static const requestTimeout = Duration(seconds: 15);

  // Langues disponibles dans l'app
  static const langues = ['fr', 'wo', 'pu'];
}
