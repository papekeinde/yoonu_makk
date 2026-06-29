import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../config/theme.dart';
import '../../config/routes.dart';
import '../../controllers/auth_controller.dart';
import '../../services/auth_service.dart';

// ─── VUE INSCRIPTION ─────────────────────────────────────────────────────────
// Toggle Patient | Gynécologue en haut.
//   Patient    → 3 étapes : Identité / Profil santé / Sécurité + recap
//   Gynécologue → 2 étapes : Identité / Infos professionnelles
class RegisterView extends StatefulWidget {
  const RegisterView({super.key});
  @override
  State<RegisterView> createState() => _RegisterViewState();
}

class _RegisterViewState extends State<RegisterView> {
  // ── Mode ────────────────────────────────────────────────────────────────────
  bool _modeGyneco = false;

  // ══════════════════════════════════════════════════════════════════════════════
  // PATIENT
  // ══════════════════════════════════════════════════════════════════════════════
  int _etape = 1; // 1 = Identité | 2 = Profil | 3 = Sécurité

  // Étape 1
  final _prenomCtrl = TextEditingController();
  final _nomCtrl    = TextEditingController();
  final _telCtrl    = TextEditingController();
  final _villeCtrl  = TextEditingController();
  String    _genre         = 'femme';
  DateTime? _dateNaissance;
  bool      _profilTouche  = false;

  // Étape 2
  String _typeProfil = 'menopause';

  // Étape 3
  final _emailCtrl    = TextEditingController();
  final _passCtrl     = TextEditingController();
  final _passConfCtrl = TextEditingController();
  String _langue  = 'Français';
  bool   _obscure = true;

  int? get _age {
    if (_dateNaissance == null) return null;
    final now = DateTime.now();
    var a = now.year - _dateNaissance!.year;
    if (now.month < _dateNaissance!.month ||
        (now.month == _dateNaissance!.month && now.day < _dateNaissance!.day)) { a--; }
    return a;
  }

  // ══════════════════════════════════════════════════════════════════════════════
  // GYNÉCOLOGUE
  // ══════════════════════════════════════════════════════════════════════════════
  int  _gEtape = 1;
  final _gPrenomCtrl      = TextEditingController();
  final _gNomCtrl         = TextEditingController();
  final _gEmailCtrl       = TextEditingController();
  final _gTelCtrl         = TextEditingController();
  final _gVilleCtrl       = TextEditingController();
  final _gNumeroOrdreCtrl = TextEditingController();
  final _gStructureCtrl   = TextEditingController();
  final _gBioCtrl         = TextEditingController();
  String _gSpecialite = 'Gynécologie-obstétrique';
  int    _gExperience = 0;
  bool   _gLoading    = false;
  bool   _gSucces     = false;

  // Étape 3 — documents
  String? _gDiplomePath,      _gDiplomeNom;
  String? _gJustificatifPath, _gJustificatifNom;

  static const _specialites = [
    'Gynécologie-obstétrique',
    'Gynécologie médicale',
    'Obstétrique',
    'Sage-femme',
    'Endocrinologie',
    'Médecine générale',
  ];

  @override
  void dispose() {
    for (final c in [
      _prenomCtrl, _nomCtrl, _telCtrl, _villeCtrl,
      _emailCtrl, _passCtrl, _passConfCtrl,
      _gPrenomCtrl, _gNomCtrl, _gEmailCtrl, _gTelCtrl, _gVilleCtrl,
      _gNumeroOrdreCtrl, _gStructureCtrl, _gBioCtrl,
    ]) { c.dispose(); }
    super.dispose();
  }

  void _snack(String msg) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(
    content: Text(msg), backgroundColor: AppColors.danger,
    behavior: SnackBarBehavior.floating));

  void _switchMode(bool gyneco) => setState(() {
    _modeGyneco = gyneco;
    _etape = 1; _gEtape = 1; _gSucces = false;
  });

  // ── Validation patient par étape ───────────────────────────────────────────
  bool _valideEtape1() {
    if (_prenomCtrl.text.trim().isEmpty) { _snack('Veuillez saisir votre prénom.'); return false; }
    if (_nomCtrl.text.trim().isEmpty)    { _snack('Veuillez saisir votre nom.'); return false; }
    if (_dateNaissance == null)          { _snack('Veuillez indiquer votre date de naissance.'); return false; }
    if ((_age ?? 0) < 12)               { _snack('Vous devez avoir au moins 12 ans.'); return false; }
    return true;
  }

  bool _valideEtape3() {
    final email = _emailCtrl.text.trim();
    if (!RegExp(r'^[\w.\-+]+@[\w\-]+\.[\w.\-]+$').hasMatch(email)) {
      _snack('Adresse email invalide.'); return false;
    }
    if (_passCtrl.text.length < 8)            { _snack('Mot de passe : 8 caractères minimum.'); return false; }
    if (_passCtrl.text != _passConfCtrl.text)  { _snack('Les mots de passe ne correspondent pas.'); return false; }
    return true;
  }

  void _suivantPatient() {
    if (_etape == 1) {
      if (!_valideEtape1()) return;
      if (_genre == 'femme' && !_profilTouche && _age != null) {
        _typeProfil = _age! >= 45 ? 'menopause' : 'grossesse';
      }
    }
    setState(() => _etape++);
  }

  Future<void> _finaliserPatient() async {
    if (!_valideEtape3()) return;
    final ctrl = context.read<AuthController>();
    final ok = await ctrl.inscrire(
      nom:                  _nomCtrl.text.trim(),
      prenom:               _prenomCtrl.text.trim(),
      email:                _emailCtrl.text.trim(),
      password:             _passCtrl.text,
      passwordConfirmation: _passConfCtrl.text,
      genre:                _genre,
      typeProfil:           _typeProfil,
      telephone:            _telCtrl.text.trim().isEmpty ? null : _telCtrl.text.trim(),
    );
    if (!mounted) return;
    if (ok) Navigator.pushReplacementNamed(context, Routes.home);
  }

  Future<void> _pickDateNaissance() async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: _dateNaissance ?? DateTime(now.year - 30),
      firstDate: DateTime(1930),
      lastDate: now,
      helpText: 'Votre date de naissance',
    );
    if (picked != null) setState(() => _dateNaissance = picked);
  }

  // ── Demande gynéco ─────────────────────────────────────────────────────────
  bool _valideGEtape1() {
    if (_gPrenomCtrl.text.trim().isEmpty) { _snack('Prénom requis.'); return false; }
    if (_gNomCtrl.text.trim().isEmpty)    { _snack('Nom requis.'); return false; }
    if (!RegExp(r'^[\w.\-+]+@[\w\-]+\.[\w.\-]+$').hasMatch(_gEmailCtrl.text.trim())) {
      _snack('Email invalide.'); return false;
    }
    if (_gTelCtrl.text.trim().isEmpty)    { _snack('Téléphone requis.'); return false; }
    if (_gVilleCtrl.text.trim().isEmpty)  { _snack('Ville requise.'); return false; }
    return true;
  }

  bool _valideGEtape2() {
    if (_gNumeroOrdreCtrl.text.trim().isEmpty) { _snack('N° d\'ordre requis.'); return false; }
    if (_gStructureCtrl.text.trim().isEmpty)   { _snack('Structure de santé requise.'); return false; }
    return true;
  }

  bool _valideGEtape3() {
    if (_gDiplomePath == null)      { _snack('Le diplôme est requis.'); return false; }
    if (_gJustificatifPath == null) { _snack('La carte professionnelle / justificatif est requis.'); return false; }
    return true;
  }

  // Sélection d'un document (PDF/JPG/PNG, max 5 Mo).
  Future<void> _choisirDocument({required bool diplome}) async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: const ['pdf', 'jpg', 'jpeg', 'png'],
    );
    if (result == null || result.files.single.path == null) return;
    final f = result.files.single;
    if (f.size > 5 * 1024 * 1024) {
      _snack('Fichier trop volumineux (max 5 Mo).');
      return;
    }
    setState(() {
      if (diplome) { _gDiplomePath = f.path; _gDiplomeNom = f.name; }
      else         { _gJustificatifPath = f.path; _gJustificatifNom = f.name; }
    });
  }

  Future<void> _soumettreDemandeGyneco() async {
    if (!_valideGEtape3()) return;
    setState(() => _gLoading = true);
    final result = await AuthService.instance.soumettreDemandeAdhesion(
      nom:              _gNomCtrl.text.trim(),
      prenom:           _gPrenomCtrl.text.trim(),
      email:            _gEmailCtrl.text.trim(),
      telephone:        _gTelCtrl.text.trim(),
      numeroOrdre:      _gNumeroOrdreCtrl.text.trim(),
      specialite:       _gSpecialite,
      anneesExperience: _gExperience,
      structureSante:   _gStructureCtrl.text.trim(),
      ville:            _gVilleCtrl.text.trim(),
      bio:              _gBioCtrl.text.trim().isEmpty ? null : _gBioCtrl.text.trim(),
      diplomePath:      _gDiplomePath,
      justificatifPath: _gJustificatifPath,
    );
    if (!mounted) return;
    setState(() { _gLoading = false; _gSucces = result.ok; });
    if (!result.ok) _snack(result.error ?? 'Erreur lors de l\'envoi.');
  }

  // ══════════════════════════════════════════════════════════════════════════════
  // BUILD
  // ══════════════════════════════════════════════════════════════════════════════
  @override
  Widget build(BuildContext context) {
    String title;
    if (_modeGyneco) {
      title = _gSucces ? 'Demande envoyée' : 'Professionnel — Étape $_gEtape / 3';
    } else {
      title = 'Inscription — Étape $_etape / 3';
    }

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        leading: BackButton(onPressed: () {
          if (!_modeGyneco && _etape > 1)  { setState(() => _etape--); return; }
          if (_modeGyneco && _gEtape > 1)  { setState(() => _gEtape--); return; }
          Navigator.pop(context);
        }),
        title: Text(title),
      ),
      body: SafeArea(
        child: Column(children: [
          // ── Indicateur d'étapes ────────────────────────────────────────
          if (!_gSucces)
            _StepBar(
              etape: _modeGyneco ? _gEtape : _etape,
              total: 3,
              labels: _modeGyneco
                  ? const ['Identité', 'Professionnel', 'Documents']
                  : const ['Identité', 'Profil', 'Sécurité'],
            ),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
              child: Column(children: [
                // ── Toggle ───────────────────────────────────────────────
                if (!_gSucces) ...[
                  _ModeToggle(gyneco: _modeGyneco, onChanged: _switchMode),
                  const SizedBox(height: 24),
                ],
                // ── Contenu ──────────────────────────────────────────────
                if (_modeGyneco) _bodyGyneco()
                else             _bodyPatient(),
              ]),
            ),
          ),
        ]),
      ),
    );
  }

  // ══════════════════════════════════════════════════════════════════════════════
  // PATIENT — CORPS
  // ══════════════════════════════════════════════════════════════════════════════
  Widget _bodyPatient() {
    final ctrl = context.watch<AuthController>();
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      if (_etape == 1) ..._p1Identite(),
      if (_etape == 2) ..._p2Profil(),
      if (_etape == 3) ..._p3Securite(ctrl),
      const SizedBox(height: 24),
      // Bouton principal
      if (ctrl.loading && _etape == 3)
        const Center(child: CircularProgressIndicator(color: AppColors.primary))
      else
        ElevatedButton(
          onPressed: _etape < 3 ? _suivantPatient : _finaliserPatient,
          child: Text(_etape < 3 ? 'Continuer' : 'Créer mon compte'),
        ),
      // Lien connexion
      const SizedBox(height: 14),
      Center(child: TextButton(
        onPressed: () => Navigator.pushReplacementNamed(context, Routes.login),
        child: RichText(text: const TextSpan(
          text: 'Déjà inscrit ? ',
          style: TextStyle(color: AppColors.ink2, fontSize: 13),
          children: [TextSpan(text: 'Se connecter',
            style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w700))],
        )),
      )),
    ]);
  }

  // ── Étape 1 : Identité ─────────────────────────────────────────────────────
  List<Widget> _p1Identite() => [
    const Text('Qui êtes-vous ?',
      style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: AppColors.primaryDark)),
    const SizedBox(height: 6),
    const Text('Ces informations personnalisent votre espace',
      style: TextStyle(fontSize: 14, color: AppColors.ink2)),
    const SizedBox(height: 24),

    _field('PRÉNOM *', 'Awa', _prenomCtrl),
    _field('NOM *', 'Diop', _nomCtrl),

    // Genre — tiles avec emoji
    _label('VOUS ÊTES *'),
    const SizedBox(height: 8),
    Row(children: [
      _GenreTile(value: 'femme', emoji: '👩', label: 'Une femme',
        selected: _genre == 'femme', onTap: () => setState(() => _genre = 'femme')),
      const SizedBox(width: 10),
      _GenreTile(value: 'homme', emoji: '👨', label: 'Un homme',
        selected: _genre == 'homme', onTap: () => setState(() => _genre = 'homme')),
    ]),
    if (_genre == 'homme')
      Padding(
        padding: const EdgeInsets.only(top: 8),
        child: Text(
          'Bienvenue ! Vous aurez accès aux contenus éducatifs pour accompagner vos proches.',
          style: const TextStyle(fontSize: 12, color: AppColors.primary),
        ),
      ),
    const SizedBox(height: 16),

    // Date de naissance
    _label('DATE DE NAISSANCE *'),
    const SizedBox(height: 8),
    GestureDetector(
      onTap: _pickDateNaissance,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 15),
        decoration: BoxDecoration(
          color: Colors.white,
          border: Border.all(color: AppColors.border, width: 1.5),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Row(children: [
          const Icon(Icons.cake_outlined, size: 18, color: AppColors.ink2),
          const SizedBox(width: 10),
          Text(
            _dateNaissance == null
                ? 'Choisir ma date de naissance'
                : '${_dateNaissance!.day.toString().padLeft(2,'0')}/${_dateNaissance!.month.toString().padLeft(2,'0')}/${_dateNaissance!.year}',
            style: TextStyle(
              fontSize: 14,
              color: _dateNaissance == null ? AppColors.ink2 : AppColors.ink,
              fontWeight: FontWeight.w600),
          ),
          const Spacer(),
          if (_age != null)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: AppColors.primarySoft, borderRadius: BorderRadius.circular(12)),
              child: Text('$_age ans', style: const TextStyle(
                fontSize: 12, fontWeight: FontWeight.w800, color: AppColors.primary)),
            ),
        ]),
      ),
    ),
    const SizedBox(height: 14),
    _field('TÉLÉPHONE', '+221 77 000 00 00', _telCtrl, type: TextInputType.phone),
    _field('VILLE', 'Dakar', _villeCtrl),
  ];

  // ── Étape 2 : Profil santé ─────────────────────────────────────────────────
  List<Widget> _p2Profil() => [
    Text(
      _genre == 'femme' ? 'Votre profil santé' : 'Votre espace',
      style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: AppColors.primaryDark)),
    const SizedBox(height: 6),
    Text(
      _genre == 'femme'
          ? 'Choisissez le suivi adapté à votre situation'
          : 'Un espace pour vous informer et accompagner vos proches',
      style: const TextStyle(fontSize: 14, color: AppColors.ink2)),
    const SizedBox(height: 22),

    if (_genre == 'femme') ...[
      if (_age != null)
        Container(
          margin: const EdgeInsets.only(bottom: 14),
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
          decoration: BoxDecoration(
            color: AppColors.primarySoft, borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.primary.withValues(alpha: .25))),
          child: Row(children: [
            const Text('💡', style: TextStyle(fontSize: 16)),
            const SizedBox(width: 8),
            Expanded(child: Text(
              _age! >= 45
                  ? 'À $_age ans, le suivi ménopause est souvent le plus adapté.'
                  : 'Profil suggéré selon votre âge — vous pouvez le modifier.',
              style: const TextStyle(fontSize: 12, color: AppColors.primaryDark))),
          ]),
        ),
      _ProfilCard(
        value: 'menopause', emoji: '🌸',
        titre: 'Ménopause / Péri-ménopause', couleur: AppColors.primary,
        desc: 'Journal des symptômes, conseils bien-être, suivi gynécologique personnalisé.',
        selected: _typeProfil == 'menopause',
        onTap: () => setState(() { _typeProfil = 'menopause'; _profilTouche = true; }),
      ),
      const SizedBox(height: 12),
      _ProfilCard(
        value: 'grossesse', emoji: '🤰',
        titre: 'Grossesse', couleur: AppColors.green,
        desc: 'Suivi semaine par semaine, CPN, mouvements bébé, nutrition OMS.',
        selected: _typeProfil == 'grossesse',
        onTap: () => setState(() { _typeProfil = 'grossesse'; _profilTouche = true; }),
      ),
    ] else
      Container(
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: AppColors.primarySoft, borderRadius: BorderRadius.circular(18),
          border: Border.all(color: AppColors.primary.withValues(alpha: .3))),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          const Text('🤝', style: TextStyle(fontSize: 34)),
          const SizedBox(height: 10),
          const Text('Espace découverte',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: AppColors.primary)),
          const SizedBox(height: 8),
          const Text(
            'En tant qu\'homme, vous aurez accès à :\n\n'
            '📚  Tous les contenus éducatifs (grossesse, ménopause)\n'
            '🤖  Le chatbot santé pour poser vos questions\n'
            '🗺️  La carte des structures de santé\n'
            '🔗  L\'annuaire des spécialistes\n\n'
            'Idéal pour accompagner votre épouse, votre sœur ou votre mère.',
            style: TextStyle(fontSize: 13, color: AppColors.ink, height: 1.6)),
        ]),
      ),
    const SizedBox(height: 20),
    Row(children: [
      Expanded(child: OutlinedButton(
        onPressed: () => setState(() => _etape = 1),
        child: const Text('Retour'),
      )),
      const SizedBox(width: 12),
      Expanded(flex: 2, child: ElevatedButton(
        onPressed: () => setState(() => _etape = 3),
        child: const Text('Continuer'),
      )),
    ]),
  ];

  // ── Étape 3 : Sécurité + récap ─────────────────────────────────────────────
  List<Widget> _p3Securite(AuthController ctrl) => [
    const Text('Dernière étape !',
      style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: AppColors.primaryDark)),
    const SizedBox(height: 6),
    const Text('Sécurisez votre compte', style: TextStyle(fontSize: 14, color: AppColors.ink2)),
    const SizedBox(height: 22),

    _field('EMAIL *', 'awa@example.com', _emailCtrl, type: TextInputType.emailAddress),

    _label('MOT DE PASSE * (8 caractères min)'),
    const SizedBox(height: 6),
    TextField(
      controller: _passCtrl, obscureText: _obscure,
      decoration: InputDecoration(
        hintText: '••••••••',
        suffixIcon: IconButton(
          icon: Icon(_obscure ? Icons.visibility_off_rounded : Icons.visibility_rounded,
            size: 20, color: AppColors.ink2),
          onPressed: () => setState(() => _obscure = !_obscure),
        ),
      ),
    ),
    const SizedBox(height: 14),
    _label('CONFIRMER LE MOT DE PASSE *'),
    const SizedBox(height: 6),
    TextField(controller: _passConfCtrl, obscureText: true,
      decoration: const InputDecoration(hintText: '••••••••')),
    const SizedBox(height: 14),

    // Langue préférée
    _label('LANGUE PRÉFÉRÉE'),
    const SizedBox(height: 8),
    Row(children: ['Français', 'Wolof', 'Pulaar'].map((l) => Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _langue = l),
        child: Container(
          margin: const EdgeInsets.symmetric(horizontal: 3),
          padding: const EdgeInsets.symmetric(vertical: 11),
          decoration: BoxDecoration(
            color: _langue == l ? AppColors.primary : Colors.white,
            border: Border.all(
              color: _langue == l ? AppColors.primary : AppColors.border, width: 1.5),
            borderRadius: BorderRadius.circular(12),
          ),
          alignment: Alignment.center,
          child: Text(l, style: TextStyle(
            fontSize: 12, fontWeight: FontWeight.w700,
            color: _langue == l ? Colors.white : AppColors.ink)),
        ),
      ),
    )).toList()),
    const SizedBox(height: 20),

    // ── Récapitulatif ────────────────────────────────────────────────────────
    Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white, borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border)),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        _label('RÉCAPITULATIF'),
        const SizedBox(height: 10),
        _recapLigne('Nom complet', '${_prenomCtrl.text.trim()} ${_nomCtrl.text.trim()}'),
        _recapLigne('Genre', _genre == 'femme' ? '👩 Femme' : '👨 Homme'),
        if (_age != null) _recapLigne('Âge', '$_age ans'),
        if (_villeCtrl.text.trim().isNotEmpty) _recapLigne('Ville', _villeCtrl.text.trim()),
        _recapLigne('Profil',
          _genre == 'homme' ? '🤝 Espace découverte'
          : _typeProfil == 'grossesse' ? '🤰 Suivi grossesse'
          : '🌸 Suivi ménopause'),
        _recapLigne('Langue', _langue),
      ]),
    ),
    if (ctrl.erreur != null) ...[
      const SizedBox(height: 12),
      Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: AppColors.danger.withValues(alpha: .08),
          borderRadius: BorderRadius.circular(10)),
        child: Text(ctrl.erreur!,
          style: const TextStyle(color: AppColors.danger, fontSize: 13)),
      ),
    ],
    const SizedBox(height: 16),
    Row(children: [
      Expanded(child: OutlinedButton(
        onPressed: ctrl.loading ? null : () => setState(() => _etape = 2),
        child: const Text('Retour'),
      )),
    ]),
    const SizedBox(height: 6),
    Center(child: Text('En continuant, vous acceptez nos conditions.',
      style: const TextStyle(fontSize: 11, color: AppColors.ink2))),
  ];

  // ══════════════════════════════════════════════════════════════════════════════
  // GYNÉCOLOGUE — CORPS
  // ══════════════════════════════════════════════════════════════════════════════
  Widget _bodyGyneco() {
    if (_gSucces) return _succesGyneco();
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      if (_gEtape == 1) ..._g1Identite(),
      if (_gEtape == 2) ..._g2Professionnel(),
      const SizedBox(height: 24),
      _gLoading
          ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
          : ElevatedButton(
              onPressed: () {
                if (_gEtape == 1) {
                  if (_valideGEtape1()) { setState(() => _gEtape = 2); }
                } else {
                  _soumettreDemandeGyneco();
                }
              },
              child: Text(_gEtape == 1 ? 'Continuer' : 'Envoyer ma demande'),
            ),
      const SizedBox(height: 12),
      _infoBox(
        icon: Icons.info_outline_rounded,
        text: 'Vos documents (diplôme, justificatif) vous seront demandés lors de la validation par notre équipe.',
      ),
    ]);
  }

  List<Widget> _g1Identite() => [
    const Text('Informations personnelles',
      style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: AppColors.primaryDark)),
    const SizedBox(height: 6),
    const Text('Vos coordonnées de contact',
      style: TextStyle(fontSize: 14, color: AppColors.ink2)),
    const SizedBox(height: 22),
    _field('PRÉNOM *', 'Dr Awa', _gPrenomCtrl),
    _field('NOM *', 'Diop', _gNomCtrl),
    _field('EMAIL PROFESSIONNEL *', 'dr.diop@clinic.sn', _gEmailCtrl,
      type: TextInputType.emailAddress),
    _field('TÉLÉPHONE *', '+221 77 000 00 00', _gTelCtrl, type: TextInputType.phone),
    _field('VILLE *', 'Dakar', _gVilleCtrl),
  ];

  List<Widget> _g2Professionnel() => [
    const Text('Informations professionnelles',
      style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: AppColors.primaryDark)),
    const SizedBox(height: 6),
    const Text('Vos qualifications médicales',
      style: TextStyle(fontSize: 14, color: AppColors.ink2)),
    const SizedBox(height: 22),
    _label('SPÉCIALITÉ'),
    const SizedBox(height: 8),
    Container(
      decoration: BoxDecoration(
        color: Colors.white, borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border, width: 1.5)),
      padding: const EdgeInsets.symmetric(horizontal: 14),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          value: _gSpecialite, isExpanded: true,
          style: const TextStyle(fontSize: 14, color: AppColors.ink),
          items: _specialites.map((s) => DropdownMenuItem(value: s, child: Text(s))).toList(),
          onChanged: (v) => setState(() => _gSpecialite = v!),
        ),
      ),
    ),
    const SizedBox(height: 14),
    _field('N° ORDRE (SYNGOB / ONMSP) *', 'SN-GYN-XXXX', _gNumeroOrdreCtrl),
    _field('STRUCTURE DE SANTÉ *', 'Clinique / Hôpital', _gStructureCtrl),
    const SizedBox(height: 4),
    _label('ANNÉES D\'EXPÉRIENCE'),
    const SizedBox(height: 8),
    Row(children: [
      _RoundBtn(icon: Icons.remove, onTap: () { if (_gExperience > 0) setState(() => _gExperience--); }),
      Expanded(child: Center(child: Text('$_gExperience an${_gExperience > 1 ? 's' : ''}',
        style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.ink)))),
      _RoundBtn(icon: Icons.add, onTap: () => setState(() => _gExperience++)),
    ]),
    const SizedBox(height: 14),
    _label('PRÉSENTATION (optionnel)'),
    const SizedBox(height: 6),
    TextField(controller: _gBioCtrl, maxLines: 3,
      decoration: const InputDecoration(hintText: 'Décrivez votre expérience…')),
    const SizedBox(height: 14),
    // Récap identité
    Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.primarySoft, borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border)),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        _label('RÉCAPITULATIF ÉTAPE 1'),
        const SizedBox(height: 8),
        _recapLigne2(Icons.person_outline, '${_gPrenomCtrl.text} ${_gNomCtrl.text}'),
        _recapLigne2(Icons.email_outlined,   _gEmailCtrl.text),
        _recapLigne2(Icons.phone_outlined,   _gTelCtrl.text),
        _recapLigne2(Icons.location_on_outlined, _gVilleCtrl.text),
      ]),
    ),
  ];

  Widget _succesGyneco() => Center(
    child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
      const SizedBox(height: 20),
      Container(
        width: 90, height: 90,
        decoration: const BoxDecoration(color: AppColors.primarySoft, shape: BoxShape.circle),
        child: const Icon(Icons.check_circle_rounded, color: AppColors.primary, size: 52),
      ),
      const SizedBox(height: 24),
      const Text('Demande envoyée !',
        style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: AppColors.primaryDark),
        textAlign: TextAlign.center),
      const SizedBox(height: 12),
      const Text(
        'Notre équipe examinera votre dossier et vous contactera par email dans les 48–72h.',
        style: TextStyle(fontSize: 14, color: AppColors.ink2, height: 1.6),
        textAlign: TextAlign.center),
      const SizedBox(height: 20),
      _infoBox(icon: Icons.article_outlined,
        text: 'Préparez : diplôme, carte professionnelle, certificat SYNGOB.'),
      const SizedBox(height: 28),
      ElevatedButton(
        onPressed: () => Navigator.pushReplacementNamed(context, Routes.login),
        child: const Text('Retour à la connexion'),
      ),
    ]),
  );

  // ── Helpers ────────────────────────────────────────────────────────────────
  Widget _field(String lbl, String hint, TextEditingController ctrl,
      {TextInputType? type}) =>
    Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        _label(lbl),
        const SizedBox(height: 6),
        TextField(controller: ctrl, keyboardType: type,
          decoration: InputDecoration(hintText: hint)),
      ]),
    );

  Widget _label(String t) => Text(t,
    style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700,
      color: AppColors.ink2, letterSpacing: 0.8));

  Widget _recapLigne(String label, String val) => Padding(
    padding: const EdgeInsets.only(bottom: 6),
    child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
      SizedBox(width: 90, child: Text(label,
        style: const TextStyle(fontSize: 12, color: AppColors.ink2))),
      Expanded(child: Text(val,
        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.ink))),
    ]),
  );

  Widget _recapLigne2(IconData icon, String val) => Padding(
    padding: const EdgeInsets.only(bottom: 5),
    child: Row(children: [
      Icon(icon, size: 14, color: AppColors.primary),
      const SizedBox(width: 8),
      Expanded(child: Text(val.isEmpty ? '—' : val,
        style: const TextStyle(fontSize: 12, color: AppColors.ink))),
    ]),
  );

  Widget _infoBox({required IconData icon, required String text}) => Container(
    padding: const EdgeInsets.all(12),
    decoration: BoxDecoration(
      color: AppColors.primarySoft, borderRadius: BorderRadius.circular(10),
      border: Border.all(color: AppColors.border)),
    child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Icon(icon, size: 15, color: AppColors.primary),
      const SizedBox(width: 8),
      Expanded(child: Text(text,
        style: const TextStyle(fontSize: 12, color: AppColors.primaryDark))),
    ]),
  );
}

// ─── TOGGLE PATIENT / GYNÉCOLOGUE ─────────────────────────────────────────────
class _ModeToggle extends StatelessWidget {
  final bool gyneco;
  final ValueChanged<bool> onChanged;
  const _ModeToggle({required this.gyneco, required this.onChanged});

  @override
  Widget build(BuildContext context) {
    if (gyneco) {
      // Bandeau "mode médecin" avec bouton retour
      return Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        decoration: BoxDecoration(
          color: AppColors.primaryDark,
          borderRadius: BorderRadius.circular(14),
        ),
        child: Row(children: [
          const Icon(Icons.medical_services_rounded, size: 18, color: Colors.white),
          const SizedBox(width: 10),
          const Expanded(
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text('Inscription Médecin', style: TextStyle(
                fontSize: 13, fontWeight: FontWeight.w700, color: Colors.white)),
              Text('Compte activé après vérification par notre équipe.',
                style: TextStyle(fontSize: 10, color: Colors.white70)),
            ]),
          ),
          GestureDetector(
            onTap: () => onChanged(false),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
              decoration: BoxDecoration(
                color: Colors.white.withValues(alpha: .15),
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.white38)),
              child: const Text('← Patient·e',
                style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600,
                  color: Colors.white)),
            ),
          ),
        ]),
      );
    }

    // Mode patient : bouton discret "Vous êtes médecin ?"
    return Row(children: [
      const Expanded(
        child: Text('Inscription patient·e',
          style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700,
            color: AppColors.ink)),
      ),
      GestureDetector(
        onTap: () => onChanged(true),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: AppColors.primary, width: 1.5),
          ),
          child: const Row(mainAxisSize: MainAxisSize.min, children: [
            Icon(Icons.medical_services_outlined, size: 14, color: AppColors.primary),
            SizedBox(width: 5),
            Text('Je suis médecin',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600,
                color: AppColors.primary)),
          ]),
        ),
      ),
    ]);
  }
}

// ─── BARRE D'ÉTAPES ────────────────────────────────────────────────────────────
class _StepBar extends StatelessWidget {
  final int etape, total;
  final List<String> labels;
  const _StepBar({required this.etape, required this.total, required this.labels});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(24, 16, 24, 4),
      child: Row(
        children: List.generate(total, (i) {
          final done   = i + 1 < etape;
          final active = i + 1 == etape;
          final color  = (done || active) ? AppColors.primary : AppColors.border;
          return Expanded(
            child: Row(children: [
              Container(
                width: 26, height: 26,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: (done || active) ? AppColors.primary : Colors.white,
                  border: Border.all(color: color, width: 2)),
                alignment: Alignment.center,
                child: done
                    ? const Icon(Icons.check, size: 13, color: Colors.white)
                    : Text('${i + 1}', style: TextStyle(
                        fontSize: 11, fontWeight: FontWeight.w800,
                        color: active ? Colors.white : AppColors.ink2)),
              ),
              const SizedBox(width: 4),
              Text(labels[i], style: TextStyle(
                fontSize: 11, fontWeight: FontWeight.w700,
                color: (done || active) ? AppColors.ink : AppColors.ink2)),
              if (i < total - 1)
                Expanded(child: Container(
                  height: 2, margin: const EdgeInsets.symmetric(horizontal: 6),
                  color: done ? AppColors.primary : AppColors.border)),
            ]),
          );
        }),
      ),
    );
  }
}

// ─── TILE GENRE ───────────────────────────────────────────────────────────────
class _GenreTile extends StatelessWidget {
  final String value, emoji, label; final bool selected; final VoidCallback onTap;
  const _GenreTile({required this.value, required this.emoji, required this.label,
    required this.selected, required this.onTap});
  @override
  Widget build(BuildContext context) => Expanded(
    child: GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14),
        decoration: BoxDecoration(
          color: selected ? AppColors.primarySoft : Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: selected ? AppColors.primary : AppColors.border,
            width: selected ? 2 : 1.5)),
        child: Column(children: [
          Text(emoji, style: const TextStyle(fontSize: 26)),
          const SizedBox(height: 4),
          Text(label, style: TextStyle(
            fontSize: 13, fontWeight: FontWeight.w800,
            color: selected ? AppColors.primary : AppColors.ink)),
        ]),
      ),
    ),
  );
}

// ─── CARTE PROFIL ─────────────────────────────────────────────────────────────
class _ProfilCard extends StatelessWidget {
  final String value, emoji, titre, desc; final Color couleur;
  final bool selected; final VoidCallback onTap;
  const _ProfilCard({required this.value, required this.emoji, required this.titre,
    required this.desc, required this.couleur, required this.selected, required this.onTap});
  @override
  Widget build(BuildContext context) => GestureDetector(
    onTap: onTap,
    child: Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: selected ? couleur.withValues(alpha: .07) : Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(
          color: selected ? couleur : AppColors.border, width: selected ? 2 : 1.5)),
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(emoji, style: const TextStyle(fontSize: 32)),
        const SizedBox(width: 14),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Expanded(child: Text(titre, style: TextStyle(
              fontWeight: FontWeight.w800, fontSize: 15,
              color: selected ? couleur : AppColors.ink))),
            if (selected) Icon(Icons.check_circle, color: couleur, size: 20),
          ]),
          const SizedBox(height: 4),
          Text(desc, style: const TextStyle(fontSize: 12, color: AppColors.ink2, height: 1.4)),
        ])),
      ]),
    ),
  );
}

// ─── BOUTON ROND +/- ──────────────────────────────────────────────────────────
class _RoundBtn extends StatelessWidget {
  final IconData icon; final VoidCallback onTap;
  const _RoundBtn({required this.icon, required this.onTap});
  @override
  Widget build(BuildContext context) => GestureDetector(
    onTap: onTap,
    child: Container(
      width: 44, height: 44,
      decoration: BoxDecoration(
        color: AppColors.primarySoft, borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border)),
      child: Icon(icon, color: AppColors.primary, size: 20)),
  );
}
