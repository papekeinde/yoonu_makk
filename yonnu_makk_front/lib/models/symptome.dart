// ─── MODÈLE SYMPTÔME ─────────────────────────────────────────────────────────
class Symptome {
  final int    id;
  final String nom;
  final int    intensite;   // 1 à 5
  final String date;
  final String? notes;

  const Symptome({
    required this.id,
    required this.nom,
    required this.intensite,
    required this.date,
    this.notes,
  });

  factory Symptome.fromJson(Map<String, dynamic> json) => Symptome(
    id:        json['id'] as int,
    nom:       json['nom'] as String,
    intensite: json['intensite'] as int? ?? 1,
    date:      json['date'] as String,
    notes:     json['notes'] as String?,
  );
}
