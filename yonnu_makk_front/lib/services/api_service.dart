import 'dart:convert';
import 'dart:io' show Platform, SocketException;
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../config/app_config.dart';
import 'storage_service.dart';

// ─── RÉPONSE API STANDARDISÉE ────────────────────────────────────────────────
class ApiResponse {
  final int              status;
  final dynamic          data;
  final String?          error;

  const ApiResponse({required this.status, this.data, this.error});

  bool get ok       => status >= 200 && status < 300;
  bool get offline  => status == 0;

  factory ApiResponse.from(http.Response r) {
    dynamic body;
    try { body = jsonDecode(r.body); } catch (_) { body = r.body; }
    return ApiResponse(
      status: r.statusCode,
      data:   body,
      error:  r.statusCode >= 400 ? (body?['message'] ?? r.body) : null,
    );
  }

  factory ApiResponse.networkError(Object e) =>
      ApiResponse(status: 0, error: 'Erreur réseau : $e');
}

// ─── CLIENT HTTP ─────────────────────────────────────────────────────────────
// Singleton qui centralise tous les appels vers le back-end Laravel.
class ApiService {
  ApiService._();
  static final ApiService instance = ApiService._();

  // URL de base calculée selon la plateforme
  String get _base {
    if (kIsWeb) return 'http://localhost:8000/api';
    try {
      if (Platform.isAndroid) return 'http://10.0.2.2:8000/api';
    } catch (_) {}
    return AppConfig.apiBaseUrl;
  }

  // En-têtes HTTP standards (JSON + token si disponible)
  Future<Map<String, String>> _headers() async {
    final token = await StorageService.instance.readToken();
    return {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  // ── MÉTHODES HTTP ─────────────────────────────────────────────────────────

  Future<ApiResponse> get(String path, {Map<String, String>? query}) async {
    try {
      final uri = Uri.parse('$_base$path').replace(queryParameters: query);
      final res = await http.get(uri, headers: await _headers())
          .timeout(AppConfig.requestTimeout);
      return ApiResponse.from(res);
    } on SocketException catch (e) {
      return ApiResponse.networkError(e);
    } catch (e) {
      return ApiResponse.networkError(e);
    }
  }

  Future<ApiResponse> post(String path, {Map<String, dynamic>? body}) async {
    try {
      final uri = Uri.parse('$_base$path');
      final res = await http.post(uri,
        headers: await _headers(), body: jsonEncode(body ?? {}))
          .timeout(AppConfig.requestTimeout);
      return ApiResponse.from(res);
    } on SocketException catch (e) {
      return ApiResponse.networkError(e);
    } catch (e) {
      return ApiResponse.networkError(e);
    }
  }

  Future<ApiResponse> put(String path, {Map<String, dynamic>? body}) async {
    try {
      final uri = Uri.parse('$_base$path');
      final res = await http.put(uri,
        headers: await _headers(), body: jsonEncode(body ?? {}))
          .timeout(AppConfig.requestTimeout);
      return ApiResponse.from(res);
    } on SocketException catch (e) {
      return ApiResponse.networkError(e);
    } catch (e) {
      return ApiResponse.networkError(e);
    }
  }

  Future<ApiResponse> patch(String path, {Map<String, dynamic>? body}) async {
    try {
      final uri = Uri.parse('$_base$path');
      final res = await http.patch(uri,
        headers: await _headers(), body: jsonEncode(body ?? {}))
          .timeout(AppConfig.requestTimeout);
      return ApiResponse.from(res);
    } on SocketException catch (e) {
      return ApiResponse.networkError(e);
    } catch (e) {
      return ApiResponse.networkError(e);
    }
  }

  // Envoi multipart/form-data (champs texte + fichiers).
  // [files] : nom du champ → chemin local du fichier.
  Future<ApiResponse> postMultipart(
    String path, {
    Map<String, String>? fields,
    Map<String, String>? files,
  }) async {
    try {
      final uri   = Uri.parse('$_base$path');
      final token = await StorageService.instance.readToken();
      final req   = http.MultipartRequest('POST', uri);
      req.headers['Accept'] = 'application/json';
      if (token != null) req.headers['Authorization'] = 'Bearer $token';
      if (fields != null) req.fields.addAll(fields);
      if (files != null) {
        for (final e in files.entries) {
          req.files.add(await http.MultipartFile.fromPath(e.key, e.value));
        }
      }
      final streamed = await req.send().timeout(AppConfig.requestTimeout);
      final res      = await http.Response.fromStream(streamed);
      return ApiResponse.from(res);
    } on SocketException catch (e) {
      return ApiResponse.networkError(e);
    } catch (e) {
      return ApiResponse.networkError(e);
    }
  }

  Future<ApiResponse> delete(String path) async {
    try {
      final uri = Uri.parse('$_base$path');
      final res = await http.delete(uri, headers: await _headers())
          .timeout(AppConfig.requestTimeout);
      return ApiResponse.from(res);
    } on SocketException catch (e) {
      return ApiResponse.networkError(e);
    } catch (e) {
      return ApiResponse.networkError(e);
    }
  }
}
