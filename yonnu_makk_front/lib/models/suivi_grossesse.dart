// ─── MODÈLE SUIVI DE GROSSESSE ────────────────────────────────────────────────
// Correspond à SuiviGrossesseResource du back-end.
class SuiviGrossesse {
  final int     id;
  final int     semainesAmenorrhee;
  final num?    poidsKg;
  final int?    tensionSystolique;
  final int?    tensionDiastolique;
  final num?    glycemie;
  final String? notes;
  final String? dateSaisie;        // 'Y-m-d'

  const SuiviGrossesse({
    required this.id,
    required this.semainesAmenorrhee,
    this.poidsKg,
    this.tensionSystolique,
    this.tensionDiastolique,
    this.glycemie,
    this.notes,
    this.dateSaisie,
  });

  factory SuiviGrossesse.fromJson(Map<String, dynamic> json) => SuiviGrossesse(
    id:                 json['id'] as int,
    semainesAmenorrhee: (json['semaines_amenorrhee'] as int?) ?? 0,
    poidsKg:            json['poids_kg'] as num?,
    tensionSystolique:  json['tension_systolique'] as int?,
    tensionDiastolique: json['tension_diastolique'] as int?,
    glycemie:           json['glycemie'] as num?,
    notes:              json['notes'] as String?,
    dateSaisie:         json['date_saisie'] as String?,
  );

  String? get tension => (tensionSystolique != null && tensionDiastolique != null)
      ? '$tensionSystolique/$tensionDiastolique'
      : null;
}
