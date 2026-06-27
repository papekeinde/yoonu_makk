// ─── MODÈLE GROSSESSE ─────────────────────────────────────────────────────────
// Correspond à GrossesseResource du back-end.
class Grossesse {
  final int     id;
  final String? dateDebutGrossesse;      // 'Y-m-d'
  final String? dateAccouchementPrevue;  // 'Y-m-d'
  final String? groupeSanguin;
  final int     nombreGrossessesAnterieures;
  final int     nombreAccouchementsAnterieurs;
  final String? antecedentsObstetricaux;
  final bool    grossesseActive;
  final int?    semainesAmenorrhee;      // semaines_amenorrhee_actuelles
  final int?    joursAvantAccouchement;

  const Grossesse({
    required this.id,
    this.dateDebutGrossesse,
    this.dateAccouchementPrevue,
    this.groupeSanguin,
    this.nombreGrossessesAnterieures = 0,
    this.nombreAccouchementsAnterieurs = 0,
    this.antecedentsObstetricaux,
    this.grossesseActive = true,
    this.semainesAmenorrhee,
    this.joursAvantAccouchement,
  });

  factory Grossesse.fromJson(Map<String, dynamic> json) => Grossesse(
    id:                            json['id'] as int,
    dateDebutGrossesse:            json['date_debut_grossesse'] as String?,
    dateAccouchementPrevue:        json['date_accouchement_prevue'] as String?,
    groupeSanguin:                 json['groupe_sanguin'] as String?,
    nombreGrossessesAnterieures:   (json['nombre_grossesses_anterieures'] as int?) ?? 0,
    nombreAccouchementsAnterieurs: (json['nombre_accouchements_anterieurs'] as int?) ?? 0,
    antecedentsObstetricaux:       json['antecedents_obstetricaux'] as String?,
    grossesseActive:               json['grossesse_active'] as bool? ?? true,
    semainesAmenorrhee:            json['semaines_amenorrhee_actuelles'] as int?,
    joursAvantAccouchement:        json['jours_avant_accouchement'] as int?,
  );

  // Trimestre déduit des semaines d'aménorrhée.
  int get trimestre {
    final sa = semainesAmenorrhee ?? 0;
    if (sa <= 13) return 1;
    if (sa <= 27) return 2;
    return 3;
  }
}
