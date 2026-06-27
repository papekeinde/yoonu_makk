import 'package:flutter/foundation.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';

// ─── STOCKAGE LOCAL ───────────────────────────────────────────────────────────
// • Token d'authentification → flutter_secure_storage (chiffré par l'OS)
// • Préférences utilisateur  → shared_preferences
class StorageService {
  StorageService._();
  static final StorageService instance = StorageService._();

  static const _secureStorage = FlutterSecureStorage(
    aOptions: AndroidOptions(encryptedSharedPreferences: true),
  );

  static const _keyToken   = 'auth_token';
  static const _keyLangue  = 'langue';

  // ── TOKEN ─────────────────────────────────────────────────────────────────

  Future<String?> readToken() async {
    if (kIsWeb) {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getString(_keyToken);
    }
    return _secureStorage.read(key: _keyToken);
  }

  Future<void> saveToken(String token) async {
    if (kIsWeb) {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(_keyToken, token);
    } else {
      await _secureStorage.write(key: _keyToken, value: token);
    }
  }

  Future<void> deleteToken() async {
    if (kIsWeb) {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(_keyToken);
    } else {
      await _secureStorage.delete(key: _keyToken);
    }
  }

  // ── LANGUE ────────────────────────────────────────────────────────────────

  Future<String> readLangue() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_keyLangue) ?? 'fr';
  }

  Future<void> saveLangue(String langue) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_keyLangue, langue);
  }
}
