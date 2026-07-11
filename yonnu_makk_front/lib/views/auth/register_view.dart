import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../config/theme.dart';
import '../../config/routes.dart';
import '../../controllers/auth_controller.dart';
import '../../services/auth_service.dart';

// ─── VUE INSCRIPTION ─────────────────────────────────────────────────────────
class RegisterView extends StatefulWidget {
  const RegisterView({super.key});
  @override
  State<RegisterView> createState() => _RegisterViewState();
}

class _RegisterViewState extends State<RegisterView> {
  bool _modeGyneco = false;

  // ── PATIENT ─────────────────────────────────────────────────────────────────
  int _etape = 1;
  final _prenomCtrl = TextEditingController();
  final _nomCtrl    = TextEditingController();
  final _telCtrl    = TextEditingController();
  final _villeCtrl  = TextEditingController();
  String    _genre         = 'femme';
  DateTime? _dateNaissance;
  bool      _profilTouche  = false;
  String    _typeProfil    = 'menopause';
  final _emailCtrl    = TextEditingController();
  final _passCtrl     = TextEditingController();
  final _passConfCtrl = TextEditingController();
  String _langue  = 'Français';
  bool   _obscure = true;

  int? get _age {
    if (_dateNaissance == null) return null;
    final now = DateTime.now();
    var a = now.year - _dateNaissance!.year;
    if (now.month < _dateNaissance!.month || (now.month == _dateNaissance!.month && now.day < _dateNaissance!.day)) { a--; }
    return a;
  }

  // ── GYNÉCOLOGUE ─────────────────────────────────────────────────────────────
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

  String? _gDiplomePath,      _gDiplomeNom;
  String? _gJustificatifPath, _gJustificatifNom;

  static const _specialites = [
    'Gynécologie-obstétrique', 'Gynécologie médicale', 'Obstétrique', 'Sage-femme', 'Endocrinologie', 'Médecine générale',
  ];

  @override
  void dispose() {
    for (final c in [
      _prenomCtrl, _nomCtrl, _telCtrl, _villeCtrl, _emailCtrl, _passCtrl, _passConfCtrl,
      _gPrenomCtrl, _gNomCtrl, _gEmailCtrl, _gTelCtrl, _gVilleCtrl, _gNumeroOrdreCtrl, _gStructureCtrl, _gBioCtrl,
    ]) { c.dispose(); }
    super.dispose();
  }

  void _snack(String msg, {bool error = true}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(
    content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success,
    behavior: SnackBarBehavior.floating));

  void _switchMode(bool gyneco) => setState(() {
    _modeGyneco = gyneco;
    _etape = 1; _gEtape = 1; _gSucces = false;
  });

  bool _valideEtape1() {
    if (_prenomCtrl.text.trim().isEmpty) { _snack('Prénom requis.'); return false; }
    if (_nomCtrl.text.trim().isEmpty)    { _snack('Nom requis.'); return false; }
    if (_dateNaissance == null)          { _snack('Date de naissance requise.'); return false; }
    return true;
  }

  bool _valideEtape3() {
    final email = _emailCtrl.text.trim();
    if (!RegExp(r'^[\w.\-+]+@[\w\-]+\.[\w.\-]+$').hasMatch(email)) { _snack('Email invalide.'); return false; }
    if (_passCtrl.text.length < 8) { _snack('Mot de passe : 8 caractères min.'); return false; }
    if (_passCtrl.text != _passConfCtrl.text) { _snack('Mots de passe différents.'); return false; }
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
      nom: _nomCtrl.text.trim(), prenom: _prenomCtrl.text.trim(), email: _emailCtrl.text.trim(),
      password: _passCtrl.text, passwordConfirmation: _passConfCtrl.text, genre: _genre, typeProfil: _typeProfil,
      telephone: _telCtrl.text.trim().isEmpty ? null : _telCtrl.text.trim(),
    );
    if (mounted && ok) Navigator.pushReplacementNamed(context, Routes.home);
  }

  Future<void> _pickDateNaissance() async {
    final picked = await showDatePicker(
      context: context, initialDate: _dateNaissance ?? DateTime(DateTime.now().year - 30),
      firstDate: DateTime(1930), lastDate: DateTime.now(),
    );
    if (picked != null) setState(() => _dateNaissance = picked);
  }

  bool _valideGEtape1() {
    if (_gPrenomCtrl.text.trim().isEmpty || _gNomCtrl.text.trim().isEmpty) { _snack('Nom et prénom requis.'); return false; }
    if (!RegExp(r'^[\w.\-+]+@[\w\-]+\.[\w.\-]+$').hasMatch(_gEmailCtrl.text.trim())) { _snack('Email invalide.'); return false; }
    return true;
  }

  bool _valideGEtape2() {
    if (_gNumeroOrdreCtrl.text.trim().isEmpty) { _snack('N° d\'ordre requis.'); return false; }
    return true;
  }

  Future<void> _choisirDocument({required bool diplome}) async {
    try {
      final result = await FilePicker.platform.pickFiles(
        type: FileType.custom, allowedExtensions: const ['pdf', 'jpg', 'jpeg', 'png'],
      );
      if (result == null || result.files.single.path == null) return;
      final f = result.files.single;
      if (f.size > 5 * 1024 * 1024) { _snack('Fichier trop lourd (max 5 Mo).'); return; }
      setState(() {
        if (diplome) { _gDiplomePath = f.path; _gDiplomeNom = f.name; }
        else         { _gJustificatifPath = f.path; _gJustificatifNom = f.name; }
      });
    } catch (e) {
      _snack('Erreur lors du choix du fichier.');
    }
  }

  Future<void> _soumettreDemandeGyneco() async {
    if (_gDiplomePath == null || _gJustificatifPath == null) { _snack('Documents requis.'); return; }
    setState(() => _gLoading = true);
    final result = await AuthService.instance.soumettreDemandeAdhesion(
      nom: _gNomCtrl.text.trim(), prenom: _gPrenomCtrl.text.trim(), email: _gEmailCtrl.text.trim(),
      telephone: _gTelCtrl.text.trim(), numeroOrdre: _gNumeroOrdreCtrl.text.trim(), specialite: _gSpecialite,
      anneesExperience: _gExperience, structureSante: _gStructureCtrl.text.trim(), ville: _gVilleCtrl.text.trim(),
      bio: _gBioCtrl.text.trim(), diplomePath: _gDiplomePath, justificatifPath: _gJustificatifPath,
    );
    if (mounted) {
      setState(() { _gLoading = false; _gSucces = result.ok; });
      if (!result.ok) _snack(result.error ?? 'Erreur.');
    }
  }

  @override
  Widget build(BuildContext context) {
    final title = _modeGyneco 
      ? (_gSucces ? 'Succès' : 'Pro — $_gEtape/3')
      : 'Inscription — $_etape/3';

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 20),
          onPressed: () {
            if (!_modeGyneco && _etape > 1) { setState(() => _etape--); return; }
            if (_modeGyneco && _gEtape > 1) { setState(() => _gEtape--); return; }
            Navigator.pop(context);
          },
        ),
      ),
      body: SafeArea(
        child: Column(
          children: [
            if (!_gSucces) _StepIndicator(etape: _modeGyneco ? _gEtape : _etape, modeGyneco: _modeGyneco),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
                child: Column(
                  children: [
                    if (!_gSucces) ...[
                      _ModeSwitch(gyneco: _modeGyneco, onToggle: _switchMode),
                      const SizedBox(height: 24),
                    ],
                    if (_modeGyneco) _bodyGyneco() else _bodyPatient(),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _bodyPatient() {
    final ctrl = context.watch<AuthController>();
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      if (_etape == 1) ...[
        _input('PRÉNOM', 'Awa', _prenomCtrl),
        _input('NOM', 'Diop', _nomCtrl),
        const Text('VOUS ÊTES', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.ink2)),
        const SizedBox(height: 8),
        Row(children: [
          _ChoiceBox(label: 'Femme', emoji: '👩', selected: _genre == 'femme', onTap: () => setState(() => _genre = 'femme')),
          const SizedBox(width: 12),
          _ChoiceBox(label: 'Homme', emoji: '👨', selected: _genre == 'homme', onTap: () => setState(() => _genre = 'homme')),
        ]),
        const SizedBox(height: 16),
        _inputDate('DATE DE NAISSANCE', _dateNaissance, _pickDateNaissance),
        _input('TÉLÉPHONE', '77...', _telCtrl, type: TextInputType.phone),
      ],
      if (_etape == 2) ...[
        if (_genre == 'femme') ...[
          _ProfilCard(title: 'Ménopause', desc: 'Suivi adapté pour la ménopause.', emoji: '🌸', selected: _typeProfil == 'menopause', color: AppColors.primary, onTap: () => setState(() => _typeProfil = 'menopause')),
          const SizedBox(height: 12),
          _ProfilCard(title: 'Grossesse', desc: 'Suivi semaine par semaine.', emoji: '🤰', selected: _typeProfil == 'grossesse', color: AppColors.green, onTap: () => setState(() => _typeProfil = 'grossesse')),
        ] else
          const Text('Espace d\'accompagnement pour les hommes.', style: TextStyle(color: AppColors.ink2)),
      ],
      if (_etape == 3) ...[
        _input('EMAIL', 'email@test.sn', _emailCtrl, type: TextInputType.emailAddress),
        _input('MOT DE PASSE', '••••••••', _passCtrl, obscure: _obscure, onToggle: () => setState(() => _obscure = !_obscure)),
        _input('CONFIRMATION', '••••••••', _passConfCtrl, obscure: true),
      ],
      const SizedBox(height: 32),
      ElevatedButton(
        onPressed: _etape < 3 ? _suivantPatient : (ctrl.loading ? null : _finaliserPatient),
        child: ctrl.loading ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : Text(_etape < 3 ? 'Suivant' : 'S\'inscrire'),
      ),
    ]);
  }

  Widget _bodyGyneco() {
    if (_gSucces) return _SuccesWidget();
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      if (_gEtape == 1) ...[
        _input('PRÉNOM', 'Dr...', _gPrenomCtrl),
        _input('NOM', '...', _gNomCtrl),
        _input('EMAIL PRO', '...', _gEmailCtrl, type: TextInputType.emailAddress),
        _input('TÉLÉPHONE', '...', _gTelCtrl, type: TextInputType.phone),
        _input('VILLE', 'Dakar', _gVilleCtrl),
      ],
      if (_gEtape == 2) ...[
        const Text('SPÉCIALITÉ', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.ink2)),
        const SizedBox(height: 8),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.border)),
          child: DropdownButton<String>(
            value: _gSpecialite, isExpanded: true, underline: const SizedBox(),
            items: _specialites.map((s) => DropdownMenuItem(value: s, child: Text(s, style: const TextStyle(fontSize: 14)))).toList(),
            onChanged: (v) => setState(() => _gSpecialite = v!),
          ),
        ),
        const SizedBox(height: 16),
        _input('N° ORDRE', '...', _gNumeroOrdreCtrl),
        _input('STRUCTURE', '...', _gStructureCtrl),
        const Text('EXPÉRIENCE (ANS)', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.ink2)),
        Row(children: [
          IconButton(onPressed: () => setState(() { if (_gExperience > 0) _gExperience--; }), icon: const Icon(Icons.remove_circle_outline)),
          Text('$_gExperience ans', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
          IconButton(onPressed: () => setState(() => _gExperience++), icon: const Icon(Icons.add_circle_outline)),
        ]),
      ],
      if (_gEtape == 3) ...[
        _fileBtn('DIPLÔME', _gDiplomeNom, () => _choisirDocument(diplome: true)),
        const SizedBox(height: 16),
        _fileBtn('CARTE PRO', _gJustificatifNom, () => _choisirDocument(diplome: false)),
      ],
      const SizedBox(height: 32),
      ElevatedButton(
        onPressed: () {
          if (_gEtape == 1 && _valideGEtape1()) { setState(() => _gEtape = 2); }
          else if (_gEtape == 2 && _valideGEtape2()) { setState(() => _gEtape = 3); }
          else if (_gEtape == 3) { _soumettreDemandeGyneco(); }
        },
        child: _gLoading ? const CircularProgressIndicator(color: Colors.white) : Text(_gEtape < 3 ? 'Suivant' : 'Envoyer ma demande'),
      ),
    ]);
  }

  Widget _input(String label, String hint, TextEditingController ctrl, {TextInputType? type, bool obscure = false, VoidCallback? onToggle}) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.ink2)),
      const SizedBox(height: 8),
      TextField(
        controller: ctrl, keyboardType: type, obscureText: obscure,
        decoration: InputDecoration(
          hintText: hint,
          suffixIcon: onToggle != null ? IconButton(icon: Icon(obscure ? Icons.visibility_off : Icons.visibility, size: 20), onPressed: onToggle) : null,
        ),
      ),
      const SizedBox(height: 16),
    ]);
  }

  Widget _inputDate(String label, DateTime? date, VoidCallback onTap) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.ink2)),
      const SizedBox(height: 8),
      InkWell(
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.border)),
          child: Row(children: [
            const Icon(Icons.calendar_today, size: 18, color: AppColors.primary),
            const SizedBox(width: 12),
            Text(date == null ? 'Sélectionner' : '${date.day}/${date.month}/${date.year}', style: const TextStyle(fontSize: 14)),
          ]),
        ),
      ),
      const SizedBox(height: 16),
    ]);
  }

  Widget _fileBtn(String label, String? name, VoidCallback onTap) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.ink2)),
      const SizedBox(height: 8),
      InkWell(
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(color: name == null ? Colors.white : AppColors.primarySoft, borderRadius: BorderRadius.circular(12), border: Border.all(color: name == null ? AppColors.border : AppColors.primary)),
          child: Row(children: [
            Icon(name == null ? Icons.upload_file : Icons.check_circle, color: name == null ? AppColors.ink2 : AppColors.primary),
            const SizedBox(width: 12),
            Expanded(child: Text(name ?? 'Choisir un fichier', overflow: TextOverflow.ellipsis, style: const TextStyle(fontSize: 14))),
          ]),
        ),
      ),
    ]);
  }
}

class _StepIndicator extends StatelessWidget {
  final int etape; final bool modeGyneco;
  const _StepIndicator({required this.etape, required this.modeGyneco});
  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 24),
      child: Row(children: List.generate(5, (i) {
        if (i.isOdd) return Expanded(child: Container(height: 2, color: (i ~/ 2 + 1) < etape ? AppColors.primary : AppColors.border));
        final step = i ~/ 2 + 1;
        return CircleAvatar(radius: 12, backgroundColor: step <= etape ? AppColors.primary : AppColors.border, child: Text('$step', style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)));
      })),
    );
  }
}

class _ModeSwitch extends StatelessWidget {
  final bool gyneco; final ValueChanged<bool> onToggle;
  const _ModeSwitch({required this.gyneco, required this.onToggle});
  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(4),
      decoration: BoxDecoration(color: AppColors.primarySoft, borderRadius: BorderRadius.circular(12)),
      child: Row(children: [
        Expanded(child: _btn('Patient', !gyneco, () => onToggle(false))),
        Expanded(child: _btn('Professionnel', gyneco, () => onToggle(true))),
      ]),
    );
  }
  Widget _btn(String t, bool sel, VoidCallback onTap) => GestureDetector(
    onTap: onTap,
    child: Container(
      padding: const EdgeInsets.symmetric(vertical: 10),
      decoration: BoxDecoration(color: sel ? AppColors.primary : Colors.transparent, borderRadius: BorderRadius.circular(10)),
      alignment: Alignment.center,
      child: Text(t, style: TextStyle(color: sel ? Colors.white : AppColors.primary, fontWeight: FontWeight.bold, fontSize: 13)),
    ),
  );
}

class _ChoiceBox extends StatelessWidget {
  final String label, emoji; final bool selected; final VoidCallback onTap;
  const _ChoiceBox({required this.label, required this.emoji, required this.selected, required this.onTap});
  @override
  Widget build(BuildContext context) {
    return Expanded(child: GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: BoxDecoration(color: selected ? AppColors.primarySoft : Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: selected ? AppColors.primary : AppColors.border, width: 2)),
        child: Column(children: [Text(emoji, style: const TextStyle(fontSize: 24)), const SizedBox(height: 4), Text(label, style: TextStyle(fontWeight: FontWeight.bold, color: selected ? AppColors.primary : AppColors.ink))]),
      ),
    ));
  }
}

class _ProfilCard extends StatelessWidget {
  final String title, desc, emoji; final bool selected; final Color color; final VoidCallback onTap;
  const _ProfilCard({required this.title, required this.desc, required this.emoji, required this.selected, required this.color, required this.onTap});
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(color: selected ? color.withValues(alpha: .05) : Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: selected ? color : AppColors.border, width: 2)),
        child: Row(children: [
          Text(emoji, style: const TextStyle(fontSize: 32)),
          const SizedBox(width: 16),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(title, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: selected ? color : AppColors.ink)),
            Text(desc, style: const TextStyle(fontSize: 12, color: AppColors.ink2)),
          ])),
          if (selected) Icon(Icons.check_circle, color: color),
        ]),
      ),
    );
  }
}

class _SuccesWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Center(child: Column(children: [
      const Icon(Icons.check_circle, size: 80, color: AppColors.primary),
      const SizedBox(height: 24),
      const Text('Demande envoyée !', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
      const SizedBox(height: 12),
      const Text('Votre dossier sera examiné sous 48-72h. Vous recevrez un email de confirmation.', textAlign: TextAlign.center, style: TextStyle(color: AppColors.ink2)),
      const SizedBox(height: 32),
      ElevatedButton(onPressed: () => Navigator.pushReplacementNamed(context, Routes.login), child: const Text('Retour à la connexion')),
    ]));
  }
}
