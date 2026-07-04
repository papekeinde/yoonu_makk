import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../config/theme.dart';
import '../../config/routes.dart';
import '../../controllers/auth_controller.dart';
import '../../widgets/bottom_nav.dart';

// ─── VUE PROFIL ──────────────────────────────────────────────────────────────
// Inspirée du PatientProfileScreen du projet de référence.
class ProfilView extends StatefulWidget {
  const ProfilView({super.key});
  @override
  State<ProfilView> createState() => _ProfilViewState();
}

class _ProfilViewState extends State<ProfilView> {
  bool _editMode = false;
  bool _saving   = false;

  late TextEditingController _prenomCtrl;
  late TextEditingController _nomCtrl;
  late TextEditingController _telCtrl;
  late TextEditingController _villeCtrl;

  @override
  void initState() {
    super.initState();
    final user = context.read<AuthController>().user;
    _prenomCtrl = TextEditingController(text: user?.prenom ?? '');
    _nomCtrl    = TextEditingController(text: user?.nom ?? '');
    _telCtrl    = TextEditingController(text: user?.telephone ?? '');
    _villeCtrl  = TextEditingController(text: user?.ville ?? '');
  }

  @override
  void dispose() {
    _prenomCtrl.dispose();
    _nomCtrl.dispose();
    _telCtrl.dispose();
    _villeCtrl.dispose();
    super.dispose();
  }

  Future<void> _sauvegarder() async {
    setState(() => _saving = true);
    await Future.delayed(const Duration(milliseconds: 800)); // simulation API
    if (!mounted) return;
    setState(() { _saving = false; _editMode = false; });
    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
      content: Text('Profil mis à jour.'),
      behavior: SnackBarBehavior.floating,
    ));
  }

  Future<void> _deconnecter() async {
    await context.read<AuthController>().deconnecter();
    if (!mounted) return;
    Navigator.pushNamedAndRemoveUntil(context, Routes.landing, (_) => false);
  }

  void _retour() {
    if (!Navigator.canPop(context)) {
      Navigator.pushReplacementNamed(context, Routes.home);
      return;
    }
    Navigator.maybePop(context);
  }

  void _bientot(String fonc) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(
    content: Text('$fonc — bientôt disponible.'),
    behavior: SnackBarBehavior.floating,
  ));

  String _profilLabel(String? type, String? genre) {
    if (genre == 'homme') return 'Espace découverte';
    return switch (type) {
      'grossesse' => 'Suivi grossesse 🤰',
      'menopause' => 'Suivi ménopause 🌸',
      _ => 'Patient·e',
    };
  }

  Color _profilColor(String? type, String? genre) {
    if (genre == 'homme') return const Color(0xFF1565C0);
    return AppColors.primary;
  }

  @override
  Widget build(BuildContext context) {
    final auth     = context.watch<AuthController>();
    final user     = auth.user;
    final enceinte = user?.estEnceinte ?? false;
    final accent   = _profilColor(user?.typeProfil, user?.genre);
    final prenom   = user?.prenom ?? '';
    final nom      = user?.nom ?? '';
    final initiale = prenom.isNotEmpty ? prenom[0].toUpperCase() : '?';

    return PopScope(
      canPop: true,
      onPopInvokedWithResult: (didPop, _) {
        if (!didPop) _retour();
      },
      child: Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded),
          tooltip: 'Retour',
          onPressed: _retour,
        ),
        title: const Text('Mon profil',
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
        actions: [
          if (!_editMode)
            IconButton(
              icon: const Icon(Icons.edit_outlined),
              tooltip: 'Modifier',
              onPressed: () => setState(() => _editMode = true),
            ),
        ],
      ),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          // ── Photo & identité ───────────────────────────────────────────────
          Center(child: Column(children: [
            Stack(children: [
              Container(
                width: 88, height: 88,
                decoration: BoxDecoration(
                  color: accent.withValues(alpha: .15),
                  shape: BoxShape.circle,
                  border: Border.all(color: accent.withValues(alpha: .4), width: 2),
                ),
                alignment: Alignment.center,
                child: Text(initiale, style: TextStyle(
                  color: accent, fontWeight: FontWeight.w800, fontSize: 34)),
              ),
              if (_editMode)
                Positioned(right: 0, bottom: 0,
                  child: GestureDetector(
                    onTap: () => _bientot('Changer la photo'),
                    child: Tooltip(
                      message: 'Changer la photo',
                      child: Container(
                        padding: const EdgeInsets.all(6),
                        decoration: BoxDecoration(color: accent, shape: BoxShape.circle,
                          border: Border.all(color: Colors.white, width: 2)),
                        child: const Icon(Icons.camera_alt_rounded, color: Colors.white, size: 14),
                      ),
                    ),
                  )),
            ]),
            const SizedBox(height: 12),
            Text('$prenom $nom'.trim().isEmpty ? 'Mon profil' : '$prenom $nom',
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.ink)),
            const SizedBox(height: 2),
            Text(user?.email ?? '', style: const TextStyle(fontSize: 13, color: AppColors.ink2)),
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 5),
              decoration: BoxDecoration(
                color: accent.withValues(alpha: .12),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(_profilLabel(user?.typeProfil, user?.genre),
                style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: accent)),
            ),
          ])),

          const SizedBox(height: 28),

          // ── Informations personnelles ─────────────────────────────────────
          _sectionTitre('Informations personnelles'),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.border),
            ),
            child: _editMode
                ? _formulaireEdit()
                : Column(children: [
                    _infoLigne(Icons.person_outline,   'Prénom',    prenom.isEmpty ? '—' : prenom),
                    _infoLigne(Icons.badge_outlined,   'Nom',       nom.isEmpty ? '—' : nom),
                    _infoLigne(Icons.phone_outlined,   'Téléphone', user?.telephone ?? '—'),
                    _infoLigne(Icons.location_on_outlined, 'Ville', user?.ville?.isEmpty ?? true ? '—' : user!.ville!),
                    _infoLigne(Icons.cake_outlined,    'Naissance', user?.dateNaissance ?? '—'),
                  ]),
          ),

          if (_editMode) ...[
            const SizedBox(height: 14),
            Row(children: [
              Expanded(child: OutlinedButton(
                onPressed: _saving ? null : () {
                  _prenomCtrl.text = user?.prenom ?? '';
                  _nomCtrl.text    = user?.nom ?? '';
                  _telCtrl.text    = user?.telephone ?? '';
                  _villeCtrl.text  = user?.ville ?? '';
                  setState(() => _editMode = false);
                },
                child: const Text('Annuler'),
              )),
              const SizedBox(width: 12),
              Expanded(child: ElevatedButton(
                onPressed: _saving ? null : _sauvegarder,
                child: _saving
                    ? const SizedBox(width: 18, height: 18,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                    : const Text('Enregistrer'),
              )),
            ]),
          ],

          if (!_editMode) ...[
            const SizedBox(height: 20),

            // ── Mon parcours ──────────────────────────────────────────────
            _sectionTitre('Mon parcours santé'),
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.border),
              ),
              child: Column(children: [
                _actionLigne(
                  enceinte ? Icons.pregnant_woman : Icons.spa_rounded,
                  enceinte ? 'Mon suivi grossesse' : 'Mon suivi ménopause',
                  onTap: () => Navigator.pushNamed(context, enceinte ? Routes.grossesse : Routes.symptomes),
                  accent: accent,
                ),
                _actionLigne(Icons.favorite_rounded, 'Mes symptômes',
                  onTap: () => Navigator.pushNamed(context, Routes.symptomes), accent: accent),
                _actionLigne(Icons.calendar_month_rounded, 'Mes rendez-vous',
                  onTap: () => Navigator.pushNamed(context, Routes.rendezVous), accent: accent),
                _actionLigne(Icons.smart_toy_rounded, 'Chatbot IA',
                  onTap: () => Navigator.pushNamed(context, Routes.chatbot), accent: accent,
                  showDivider: false),
              ]),
            ),

            const SizedBox(height: 20),

            // ── Compte ────────────────────────────────────────────────────
            _sectionTitre('Compte'),
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.border),
              ),
              child: Column(children: [
                _actionLigne(Icons.lock_outline_rounded, 'Changer le mot de passe',
                  onTap: () => _bientot('Changement de mot de passe')),
                _actionLigne(Icons.notifications_outlined, 'Notifications',
                  onTap: () => Navigator.pushNamed(context, Routes.notifications)),
                _actionLigne(Icons.help_outline_rounded, 'Aide & support',
                  onTap: () => _bientot('Aide')),
                _actionLigne(Icons.logout_rounded, 'Se déconnecter',
                  onTap: _deconnecter,
                  color: AppColors.danger,
                  showDivider: false),
              ]),
            ),

            const SizedBox(height: 32),
            const Center(child: Text('YOONU JIGEEN v1.0.0',
              style: TextStyle(fontSize: 11, color: AppColors.ink2))),
            const SizedBox(height: 20),
          ],
        ],
      ),
      bottomNavigationBar: BottomNav(
        currentIndex: enceinte ? 4 : 4,
        estEnceinte: enceinte,
      ),
      ),
    );
  }

  Widget _sectionTitre(String t) => Padding(
    padding: const EdgeInsets.only(bottom: 10),
    child: Text(t, style: const TextStyle(
      fontSize: 15, fontWeight: FontWeight.w800, color: AppColors.ink)),
  );

  Widget _infoLigne(IconData icon, String label, String valeur) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 9),
    child: Row(children: [
      Icon(icon, size: 18, color: AppColors.ink2),
      const SizedBox(width: 12),
      Text(label, style: const TextStyle(fontSize: 13, color: AppColors.ink2)),
      const Spacer(),
      Text(valeur, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.ink)),
    ]),
  );

  Widget _actionLigne(IconData icon, String label, {
    required VoidCallback onTap, Color? color, Color? accent, bool showDivider = true,
  }) {
    final fg = color ?? AppColors.ink;
    return Column(children: [
      InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(10),
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 10),
          child: Row(children: [
            Icon(icon, size: 20, color: color ?? accent ?? AppColors.ink2),
            const SizedBox(width: 12),
            Expanded(child: Text(label,
              style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: fg))),
            Icon(Icons.chevron_right_rounded, size: 18, color: AppColors.ink2.withValues(alpha: .5)),
          ]),
        ),
      ),
      if (showDivider) const Divider(color: AppColors.border, height: 1),
    ]);
  }

  Widget _formulaireEdit() => Column(children: [
    TextField(controller: _prenomCtrl,
      decoration: const InputDecoration(labelText: 'Prénom')),
    const SizedBox(height: 10),
    TextField(controller: _nomCtrl,
      decoration: const InputDecoration(labelText: 'Nom')),
    const SizedBox(height: 10),
    TextField(controller: _telCtrl, keyboardType: TextInputType.phone,
      decoration: const InputDecoration(labelText: 'Téléphone')),
    const SizedBox(height: 10),
    TextField(controller: _villeCtrl,
      decoration: const InputDecoration(labelText: 'Ville')),
  ]);
}

