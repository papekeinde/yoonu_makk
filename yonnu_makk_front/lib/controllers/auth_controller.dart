import 'package:flutter/material.dart';
import '../models/user.dart';
import '../services/auth_service.dart';
import '../services/storage_service.dart';

// ─── CONTROLLER D'AUTHENTIFICATION ───────────────────────────────────────────
// Gère l'état global de l'utilisateur connecté.
// Les vues écoutent ce controller via Provider/ChangeNotifier.
class AuthController extends ChangeNotifier {
  User?   _user;
  bool    _loading = false;
  String? _erreur;

  User?   get user      => _user;
  bool    get loading   => _loading;
  String? get erreur    => _erreur;
  bool    get connecte  => _user != null;

  final _authService    = AuthService.instance;
  final _storageService = StorageService.instance;

  // Appelé au démarrage pour restaurer la session
  Future<void> initialiser() async {
    final token = await _storageService.readToken();
    if (token == null) return;
    // Le token existe → on recharge le profil depuis l'API
    // (non implémenté ici : on laisse la session valide jusqu'à 401)
  }

  Future<bool> connecter({
    required String identifiant,
    required String motDePasse,
  }) async {
    _loading = true;
    _erreur  = null;
    notifyListeners();

    final result = await _authService.connecter(
      identifiant: identifiant,
      motDePasse:  motDePasse,
    );

    _loading = false;
    if (result.error != null) {
      _erreur = result.error;
      notifyListeners();
      return false;
    }

    _user = result.user;
    notifyListeners();
    return true;
  }

  Future<bool> inscrire({
    required String nom,
    required String prenom,
    required String email,
    required String password,
    required String passwordConfirmation,
    required String genre,
    required String typeProfil,
    String? telephone,
    String? dateNaissance,
    String? ville,
  }) async {
    _loading = true;
    _erreur  = null;
    notifyListeners();

    final result = await _authService.inscrire(
      nom:                   nom,
      prenom:                prenom,
      email:                 email,
      password:              password,
      passwordConfirmation:  passwordConfirmation,
      genre:                 genre,
      typeProfil:            typeProfil,
      telephone:             telephone,
      dateNaissance:         dateNaissance,
      ville:                 ville,
    );

    _loading = false;
    if (result.error != null) {
      _erreur = result.error;
      notifyListeners();
      return false;
    }

    _user = result.user;
    notifyListeners();
    return true;
  }

  Future<void> deconnecter() async {
    await _authService.deconnecter();
    _user = null;
    notifyListeners();
  }

  void clearError() {
    _erreur = null;
    notifyListeners();
  }
}
