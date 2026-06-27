// ─── MODÈLE NOTIFICATION ─────────────────────────────────────────────────────
class AppNotification {
  final int    id;
  final String titre;
  final String message;
  final bool   lu;
  final String createdAt;

  const AppNotification({
    required this.id,
    required this.titre,
    required this.message,
    required this.lu,
    required this.createdAt,
  });

  factory AppNotification.fromJson(Map<String, dynamic> json) => AppNotification(
    id:        json['id'] as int,
    titre:     json['titre'] as String? ?? '',
    message:   json['message'] as String? ?? '',
    lu:        json['lu'] as bool? ?? false,
    createdAt: json['created_at'] as String? ?? '',
  );
}
