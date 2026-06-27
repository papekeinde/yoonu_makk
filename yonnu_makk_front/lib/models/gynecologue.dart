// ─── MODÈLE GYNÉCOLOGUE (public) ─────────────────────────────────────────────
// Correspond à GynecologueResource du back-end.
class Gynecologue {
  final int    id;
  final String nom;
  final String prenom;
  final String nomComplet;
  final String specialite;
  final int    anneesExperience;
  final String structureSante;
  final String ville;
  final int?   tarifConsultation; // en FCFA
  final String? bio;
  final String? photo;

  const Gynecologue({
    required this.id,
    required this.nom,
    required this.prenom,
    required this.nomComplet,
    required this.specialite,
    required this.anneesExperience,
    required this.structureSante,
    required this.ville,
    this.tarifConsultation,
    this.bio,
    this.photo,
  });

  factory Gynecologue.fromJson(Map<String, dynamic> json) => Gynecologue(
    id:               json['id'] as int,
    nom:              json['nom'] as String,
    prenom:           json['prenom'] as String,
    nomComplet:       json['nom_complet'] as String? ??
                      '${json['prenom']} ${json['nom']}',
    specialite:       json['specialite'] as String? ?? '',
    anneesExperience: json['annees_experience'] as int? ?? 0,
    structureSante:   json['structure_sante'] as String? ?? '',
    ville:            json['ville'] as String? ?? '',
    tarifConsultation: json['tarif_consultation'] as int?,
    bio:              json['bio'] as String?,
    photo:            (json['photo'] ?? json['avatar']) as String?,
  );

  /// Initiale pour la photo circulaire
  String get initiale => nomComplet.isNotEmpty ? nomComplet[0].toUpperCase() : 'G';

  /// Tarif formaté "15 000 FCFA" (ou null si non renseigné)
  String? get tarifAffiche {
    if (tarifConsultation == null) return null;
    final s = tarifConsultation.toString();
    final buf = StringBuffer();
    for (var i = 0; i < s.length; i++) {
      if (i > 0 && (s.length - i) % 3 == 0) buf.write(' ');
      buf.write(s[i]);
    }
    return '$buf FCFA';
  }
}
