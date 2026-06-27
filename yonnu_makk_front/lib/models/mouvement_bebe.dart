// ─── MODÈLE MOUVEMENT BÉBÉ ────────────────────────────────────────────────────
// Correspond à MouvementBebeResource du back-end.
class MouvementBebe {
  final int     id;
  final String  dateHeure;        // 'Y-m-d H:i'
  final int     nombreMouvements;
  final String? intensite;        // leger | modere | fort
  final String? intensiteLabel;
  final String? notes;

  const MouvementBebe({
    required this.id,
    required this.dateHeure,
    required this.nombreMouvements,
    this.intensite,
    this.intensiteLabel,
    this.notes,
  });

  factory MouvementBebe.fromJson(Map<String, dynamic> json) => MouvementBebe(
    id:               json['id'] as int,
    dateHeure:        json['date_heure'] as String,
    nombreMouvements: (json['nombre_mouvements'] as int?) ?? 1,
    intensite:        json['intensite'] as String?,
    intensiteLabel:   json['intensite_label'] as String?,
    notes:            json['notes'] as String?,
  );

  DateTime get dateTime => DateTime.parse(dateHeure);
}
