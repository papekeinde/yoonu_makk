// ─── MODÈLE UTILISATEUR ──────────────────────────────────────────────────────
// Correspond à la table `users` du back-end Laravel.

class User {
  final int    id;
  final String nom;
  final String prenom;
  final String email;
  final String? telephone;
  final String role;          // 'patient' | 'admin'
  final String? genre;        // 'femme' | 'homme'
  final String? typeProfil;   // 'grossesse' | 'menopause' | null
  final String? photo;
  final String? ville;
  final String? dateNaissance;
  final bool   emailVerifie;

  const User({
    required this.id,
    required this.nom,
    required this.prenom,
    required this.email,
    this.telephone,
    required this.role,
    this.genre,
    this.typeProfil,
    this.photo,
    this.ville,
    this.dateNaissance,
    this.emailVerifie = false,
  });

  String get nomComplet => '$prenom $nom';

  bool get estPatient  => role == 'patient';
  bool get estAdmin    => role == 'admin';
  bool get estFemme    => genre == 'femme';
  bool get estEnceinte => typeProfil == 'grossesse';
  bool get estMenopause => typeProfil == 'menopause';

  factory User.fromJson(Map<String, dynamic> json) => User(
    id:             json['id'] as int,
    nom:            json['nom'] as String,
    prenom:         json['prenom'] as String,
    email:          json['email'] as String,
    telephone:      json['telephone'] as String?,
    role:           json['role'] as String? ?? 'patient',
    genre:          json['genre'] as String?,
    typeProfil:     (json['type_profil'] ?? json['femme']?['type_profil']) as String?,
    photo:          (json['photo'] ?? json['avatar']) as String?,
    ville:          json['ville'] as String?,
    dateNaissance:  json['date_naissance'] as String?,
    emailVerifie:   json['email_verifie'] == true || json['email_verified_at'] != null,
  );
}
