import 'gynecologue.dart';

// ─── MODÈLE RENDEZ-VOUS ───────────────────────────────────────────────────────
// Correspond à RendezVousResource du back-end.
class RendezVous {
  final int    id;
  final String dateSouhaitee;   // format 'Y-m-d'
  final String? heureSouhaitee; // format 'H:i'
  final String? dateConfirmee;
  final String? heureConfirmee;
  final String  statut;         // en_attente | confirme | refuse | termine | annule
  final String? motif;
  final String? noteGynecologue;
  final String? annulePar;
  final Gynecologue? gynecologue;

  const RendezVous({
    required this.id,
    required this.dateSouhaitee,
    required this.statut,
    this.heureSouhaitee,
    this.dateConfirmee,
    this.heureConfirmee,
    this.motif,
    this.noteGynecologue,
    this.annulePar,
    this.gynecologue,
  });

  factory RendezVous.fromJson(Map<String, dynamic> json) => RendezVous(
    id:               json['id'] as int,
    dateSouhaitee:    json['date_souhaitee'] as String,
    heureSouhaitee:   json['heure_souhaitee'] as String?,
    dateConfirmee:    json['date_confirmee'] as String?,
    heureConfirmee:   json['heure_confirmee'] as String?,
    statut:           json['statut'] is String
                          ? json['statut'] as String
                          : (json['statut']?['value'] as String? ?? 'en_attente'),
    motif:            json['motif'] as String?,
    noteGynecologue:  json['note_gynecologue'] as String?,
    annulePar:        json['annule_par'] as String?,
    gynecologue:      json['gynecologue'] != null
                          ? Gynecologue.fromJson(json['gynecologue'] as Map<String, dynamic>)
                          : null,
  );

  DateTime get dateTime => DateTime.parse(dateSouhaitee);

  bool get aVenir => dateTime.isAfter(DateTime.now());

  String get heureAffichee =>
      dateConfirmee != null && heureConfirmee != null
          ? heureConfirmee!
          : heureSouhaitee ?? '--:--';

  String get nomMedecin => gynecologue?.nomComplet ?? 'À assigner';
  String get specialiteMedecin => gynecologue?.specialite ?? '';
}

