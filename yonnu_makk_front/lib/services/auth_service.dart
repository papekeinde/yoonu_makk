import '../models/user.dart';
import 'api_service.dart';
import 'storage_service.dart';

// ─── SERVICE D'AUTHENTIFICATION ───────────────────────────────────────────────
// Gère : connexion, inscription, déconnexion, vérification du token.
class AuthService {
  AuthService._();
  static final AuthService instance = AuthService._();

  final _api     = ApiService.instance;
  final _storage = StorageService.instance;

  // Connexion (patient/admin via l'API users, gynécologue via son endpoint dédié)
  Future<({User? user, String? error})> connecter({
    required String identifiant,
    required String motDePasse,
  }) async {
    final baseBody = {
      'identifiant': identifiant,
      'password':    motDePasse,
    };

    final res = await _api.post('/auth/connexion', body: baseBody);
    if (res.ok) {
      final token = res.data['token'] as String?;
      if (token == null) return (user: null, error: 'Token manquant.');

      await _storage.saveToken(token);
      final user = User.fromJson(res.data['user'] as Map<String, dynamic>);
      return (user: user, error: null);
    }

    final gynecoRes = await _api.post('/gynecologue/auth/connexion', body: baseBody);
    if (gynecoRes.ok) {
      final token = gynecoRes.data['token'] as String?;
      if (token == null) return (user: null, error: 'Token manquant.');

      await _storage.saveToken(token);
      final raw = gynecoRes.data['gynecologue'] as Map<String, dynamic>?;
      if (raw == null) return (user: null, error: 'Profil gynécologue introuvable.');

      final user = User(
        id: raw['id'] as int? ?? 0,
        nom: raw['nom'] as String? ?? '',
        prenom: raw['prenom'] as String? ?? '',
        email: raw['email'] as String? ?? identifiant,
        telephone: raw['telephone'] as String?,
        role: 'gynecologue',
        genre: raw['genre'] as String?,
        photo: raw['avatar'] as String?,
        ville: raw['ville'] as String?,
        emailVerifie: true,
      );
      return (user: user, error: null);
    }

    final message = (res.error ?? gynecoRes.error ?? 'Erreur de connexion.').toString();
    return (user: null, error: message);
  }

  // Inscription patient
  Future<({User? user, String? error})> inscrire({
    required String nom,
    required String prenom,
    required String email,
    required String password,
    required String passwordConfirmation,
    required String genre,
    required String typeProfil,
    String?  telephone,
    String?  dateNaissance,
    String?  ville,
  }) async {
    final body = <String, dynamic>{
      'nom':                    nom,
      'prenom':                 prenom,
      'email':                  email,
      'password':               password,
      'password_confirmation':  passwordConfirmation,
      'genre':                  genre,
      'type_profil':            typeProfil,
    };
    if (telephone     != null) body['telephone']      = telephone;
    if (dateNaissance != null) body['date_naissance'] = dateNaissance;
    if (ville         != null) body['ville']          = ville;

    final res = await _api.post('/auth/inscription', body: body);

    if (!res.ok) return (user: null, error: res.error ?? 'Erreur d\'inscription.');

    final token = res.data['token'] as String?;
    if (token == null) return (user: null, error: 'Token manquant.');

    await _storage.saveToken(token);
    final user = User.fromJson(res.data['user'] as Map<String, dynamic>);
    return (user: user, error: null);
  }

  // Mot de passe oublié : demande l'envoi d'un lien de réinitialisation par email.
  // Appelle POST /api/auth/mot-de-passe/email.
  Future<({bool ok, String message})> motDePasseOublie(String email) async {
    final res = await _api.post('/auth/mot-de-passe/email', body: {'email': email});
    final msg = (res.data is Map ? res.data['message'] as String? : null)
        ?? res.error
        ?? (res.ok ? 'Lien de réinitialisation envoyé.' : 'Erreur, réessayez plus tard.');
    return (ok: res.ok, message: msg);
  }

  // Déconnexion
  Future<void> deconnecter() async {
    await _api.post('/auth/deconnexion');
    await _storage.deleteToken();
  }

  // Soumettre une demande d'adhésion (gynécologue)
  // Appelle POST /api/demandes-adhesion (route publique, multipart à cause des fichiers).
  // [diplomePath] / [justificatifPath] : chemins locaux des documents (PDF/JPG/PNG).
  Future<({bool ok, String? error})> soumettreDemandeAdhesion({
    required String nom,
    required String prenom,
    required String email,
    required String telephone,
    required String numeroOrdre,
    required String specialite,
    required int    anneesExperience,
    required String structureSante,
    required String ville,
    String? bio,
    String? diplomePath,
    String? justificatifPath,
  }) async {
    final fields = <String, String>{
      'nom':               nom,
      'prenom':            prenom,
      'email':             email,
      'telephone':         telephone,
      'numero_ordre':      numeroOrdre,
      'specialite':        specialite,
      'annees_experience': anneesExperience.toString(),
      'structure_sante':   structureSante,
      'ville':             ville,
    };

    final files = <String, String>{};
    if (diplomePath      != null) files['diplome']      = diplomePath;
    if (justificatifPath != null) files['justificatif'] = justificatifPath;

    final res = await _api.postMultipart('/demandes-adhesion',
        fields: fields, files: files);
    if (!res.ok) return (ok: false, error: res.error ?? 'Erreur lors de l\'envoi.');
    return (ok: true, error: null);
  }

  // Vérifie si un token est stocké (pour décider de la page de démarrage)
  Future<bool> estConnecte() async {
    final token = await _storage.readToken();
    return token != null;
  }
}
