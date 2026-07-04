import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../config/routes.dart';
import '../../models/grossesse.dart';
import '../../models/suivi_grossesse.dart';
import '../../models/mouvement_bebe.dart';
import '../../services/api_service.dart';
import '../../widgets/bottom_nav.dart';

// Formatage de dates minimal (sans dépendance locale).
const _mois = ['jan','fév','mar','avr','mai','juin','jul','aoû','sep','oct','nov','déc'];
String _fmtDate(String? ymd) {
  if (ymd == null) return '—';
  final d = DateTime.tryParse(ymd);
  if (d == null) return ymd;
  return '${d.day} ${_mois[d.month - 1]} ${d.year}';
}
String _fmtDateHeure(DateTime d) =>
    '${d.day} ${_mois[d.month - 1]} à ${d.hour.toString().padLeft(2,'0')}:${d.minute.toString().padLeft(2,'0')}';
String _ymd(DateTime d) =>
    '${d.year}-${d.month.toString().padLeft(2,'0')}-${d.day.toString().padLeft(2,'0')}';

const _groupesSanguins = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];

// ══════════════════════════════════════════════════════════════════════════════
// VUE GROSSESSE
// ══════════════════════════════════════════════════════════════════════════════
class GrossesseView extends StatefulWidget {
  const GrossesseView({super.key});
  @override
  State<GrossesseView> createState() => _GrossesseViewState();
}

class _GrossesseViewState extends State<GrossesseView> {
  bool       _loading = true;
  String?    _error;
  Grossesse? _grossesse;
  List<SuiviGrossesse> _suivis = [];

  @override
  void initState() {
    super.initState();
    _charger();
  }

  Future<void> _charger() async {
    setState(() { _loading = true; _error = null; });
    final res = await ApiService.instance.get('/patient/grossesse');
    if (!mounted) return;
    if (res.ok) {
      _grossesse = Grossesse.fromJson(res.data as Map<String, dynamic>);
      await _chargerSuivis();
      if (!mounted) return;
      setState(() => _loading = false);
    } else if (res.status == 404) {
      // Pas de grossesse active → on proposera le formulaire de création.
      setState(() { _grossesse = null; _loading = false; });
    } else {
      setState(() { _error = res.error ?? 'Erreur de chargement'; _loading = false; });
    }
  }

  Future<void> _chargerSuivis() async {
    try {
      final res = await ApiService.instance.get('/patient/grossesse/suivis');
      if (!mounted || !res.ok) return;

      final raw = res.data;
      final items = raw is List
          ? raw
          : raw is Map<String, dynamic>
              ? raw['data'] as List?
              : null;

      _suivis = (items ?? [])
          .map((e) => SuiviGrossesse.fromJson(e as Map<String, dynamic>))
          .toList();
    } catch (_) {
      _suivis = [];
    }
  }

  void _snack(String msg, {bool erreur = false}) =>
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(msg),
        backgroundColor: erreur ? AppColors.danger : null,
        behavior: SnackBarBehavior.floating));

  // ── Clôturer la grossesse ──────────────────────────────────────────────────
  Future<void> _cloturer() async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Clôturer la grossesse ?',
          style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16)),
        content: const Text('Cette grossesse sera archivée et votre profil reviendra au suivi standard.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Annuler')),
          ElevatedButton(onPressed: () => Navigator.pop(context, true),
            child: const Text('Clôturer')),
        ],
      ),
    );
    if (ok != true) return;
    final res = await ApiService.instance.patch('/patient/grossesse/cloturer');
    if (!mounted) return;
    if (res.ok) { _snack('Grossesse clôturée.'); _charger(); }
    else        { _snack(res.error ?? 'Erreur.', erreur: true); }
  }

  // ── Ajouter un suivi ───────────────────────────────────────────────────────
  void _ajouterSuivi() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _SuiviSheet(
        saInitiale: _grossesse?.semainesAmenorrhee,
        onConfirme: () { _chargerSuivis().then((_) { if (mounted) setState(() {}); }); },
      ),
    );
  }

  Future<void> _supprimerSuivi(SuiviGrossesse s) async {
    final res = await ApiService.instance.delete('/patient/grossesse/suivis/${s.id}');
    if (!mounted) return;
    if (res.ok) { await _chargerSuivis(); setState(() {}); _snack('Suivi supprimé.'); }
    else        { _snack(res.error ?? 'Erreur.', erreur: true); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Ma grossesse',
        style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
        actions: [
          if (_grossesse != null)
            IconButton(
              tooltip: 'Clôturer',
              icon: const Icon(Icons.flag_outlined, color: AppColors.ink2),
              onPressed: _cloturer),
        ],
      ),
      bottomNavigationBar: const BottomNav(currentIndex: 1),
      body: _body(),
    );
  }

  Widget _body() {
    if (_loading) {
      return const Center(child: CircularProgressIndicator(color: AppColors.primary));
    }
    if (_error != null) {
      return _ErreurWidget(message: _error!, onRetry: _charger);
    }
    if (_grossesse == null) {
      return _FormulaireGrossesse(onCree: _charger);
    }
    return _dashboard(_grossesse!);
  }

  Widget _dashboard(Grossesse g) {
    return RefreshIndicator(
      onRefresh: _charger,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 24),
        children: [
          // Bannière progression
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [AppColors.primary, AppColors.primaryDark],
                begin: Alignment.topLeft, end: Alignment.bottomRight),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Text('SEMAINES D\'AMÉNORRHÉE',
                style: TextStyle(color: Colors.white70, fontSize: 11,
                  fontWeight: FontWeight.w700, letterSpacing: 1)),
              const SizedBox(height: 6),
              Row(crossAxisAlignment: CrossAxisAlignment.baseline,
                textBaseline: TextBaseline.alphabetic, children: [
                Text('${g.semainesAmenorrhee ?? '—'}',
                  style: const TextStyle(color: Colors.white, fontSize: 44,
                    fontWeight: FontWeight.w800, height: 1)),
                const SizedBox(width: 6),
                const Text('semaines',
                  style: TextStyle(color: Colors.white70, fontSize: 14)),
                const Spacer(),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(color: Colors.white.withValues(alpha: .2),
                    borderRadius: BorderRadius.circular(20)),
                  child: Text('Trimestre ${g.trimestre}',
                    style: const TextStyle(color: Colors.white,
                      fontSize: 12, fontWeight: FontWeight.w700)),
                ),
              ]),
              if (g.joursAvantAccouchement != null) ...[
                const SizedBox(height: 10),
                Text(g.joursAvantAccouchement! >= 0
                    ? '🍼 Plus que ${g.joursAvantAccouchement} jours avant le terme'
                    : '🍼 Terme dépassé de ${-g.joursAvantAccouchement!} jours',
                  style: const TextStyle(color: Colors.white, fontSize: 13,
                    fontWeight: FontWeight.w600)),
              ],
            ]),
          ),
          const SizedBox(height: 16),

          // Infos clés
          Row(children: [
            Expanded(child: _InfoTile(icon: Icons.event_outlined,
              label: 'Terme prévu', value: _fmtDate(g.dateAccouchementPrevue))),
            const SizedBox(width: 12),
            Expanded(child: _InfoTile(icon: Icons.bloodtype_outlined,
              label: 'Groupe sanguin', value: g.groupeSanguin ?? '—')),
          ]),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(child: _InfoTile(icon: Icons.pregnant_woman,
              label: 'Grossesses ant.', value: '${g.nombreGrossessesAnterieures}')),
            const SizedBox(width: 12),
            Expanded(child: _InfoTile(icon: Icons.child_friendly_outlined,
              label: 'Accouchements ant.', value: '${g.nombreAccouchementsAnterieurs}')),
          ]),

          // Lien compteur de mouvements
          const SizedBox(height: 16),
          GestureDetector(
            onTap: () => Navigator.pushNamed(context, Routes.mouvements),
            child: Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(color: AppColors.primarySoft,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.border)),
              child: Row(children: [
                const Text('👶', style: TextStyle(fontSize: 28)),
                const SizedBox(width: 12),
                const Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Compteur de mouvements',
                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: AppColors.ink)),
                    Text('Enregistrez les coups de bébé',
                      style: TextStyle(fontSize: 12, color: AppColors.ink2)),
                  ])),
                const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: AppColors.primary),
              ]),
            ),
          ),

          // Suivis médicaux
          const SizedBox(height: 24),
          Row(children: [
            const Text('Suivis médicaux',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: AppColors.ink)),
            const Spacer(),
            TextButton.icon(
              onPressed: _ajouterSuivi,
              icon: const Icon(Icons.add_rounded, size: 18),
              label: const Text('Ajouter')),
          ]),
          const SizedBox(height: 4),
          if (_suivis.isEmpty)
            const Padding(
              padding: EdgeInsets.symmetric(vertical: 20),
              child: Center(child: Text('Aucun suivi enregistré pour le moment.',
                style: TextStyle(color: AppColors.ink2, fontSize: 13))),
            )
          else
            ..._suivis.map((s) => _SuiviCard(suivi: s, onSupprimer: () => _supprimerSuivi(s))),
        ],
      ),
    );
  }
}

// ─── FORMULAIRE DE CRÉATION DE GROSSESSE ─────────────────────────────────────
class _FormulaireGrossesse extends StatefulWidget {
  final VoidCallback onCree;
  const _FormulaireGrossesse({required this.onCree});
  @override
  State<_FormulaireGrossesse> createState() => _FormulaireGrossesseState();
}

class _FormulaireGrossesseState extends State<_FormulaireGrossesse> {
  DateTime? _debut;
  DateTime? _dpa;
  String?   _groupe;
  int       _grossessesAnt = 0;
  int       _accouchementsAnt = 0;
  final _antecedentsCtrl = TextEditingController();
  bool _loading = false;

  @override
  void dispose() { _antecedentsCtrl.dispose(); super.dispose(); }

  void _snack(String m) => ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(content: Text(m), backgroundColor: AppColors.danger,
      behavior: SnackBarBehavior.floating));

  Future<void> _pickDebut() async {
    final now = DateTime.now();
    final d = await showDatePicker(context: context,
      initialDate: _debut ?? now.subtract(const Duration(days: 60)),
      firstDate: now.subtract(const Duration(days: 300)), lastDate: now,
      helpText: 'Date de début de grossesse');
    if (d != null) {
      setState(() {
        _debut = d;
        // DPA par défaut : début + 280 jours (40 SA)
        _dpa ??= d.add(const Duration(days: 280));
      });
    }
  }

  Future<void> _pickDpa() async {
    final base = _debut ?? DateTime.now();
    final d = await showDatePicker(context: context,
      initialDate: _dpa ?? base.add(const Duration(days: 280)),
      firstDate: base.add(const Duration(days: 1)),
      lastDate: base.add(const Duration(days: 320)),
      helpText: 'Date prévue d\'accouchement');
    if (d != null) setState(() => _dpa = d);
  }

  Future<void> _soumettre() async {
    if (_debut == null) { _snack('Indiquez la date de début.'); return; }
    if (_dpa == null)   { _snack('Indiquez la date prévue d\'accouchement.'); return; }
    setState(() => _loading = true);
    final res = await ApiService.instance.post('/patient/grossesse', body: {
      'date_debut_grossesse':            _ymd(_debut!),
      'date_accouchement_prevue':        _ymd(_dpa!),
      if (_groupe != null) 'groupe_sanguin': _groupe,
      'nombre_grossesses_anterieures':   _grossessesAnt,
      'nombre_accouchements_anterieurs': _accouchementsAnt,
      if (_antecedentsCtrl.text.trim().isNotEmpty)
        'antecedents_obstetricaux': _antecedentsCtrl.text.trim(),
    });
    if (!mounted) return;
    setState(() => _loading = false);
    if (res.ok) { widget.onCree(); }
    else        { _snack(res.error ?? 'Erreur lors de l\'enregistrement.'); }
  }

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.fromLTRB(20, 20, 20, 32),
      children: [
        const Center(child: Text('🤰', style: TextStyle(fontSize: 56))),
        const SizedBox(height: 12),
        const Text('Démarrez votre suivi de grossesse',
          textAlign: TextAlign.center,
          style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.primaryDark)),
        const SizedBox(height: 6),
        const Text('Renseignez quelques informations pour suivre votre grossesse semaine par semaine.',
          textAlign: TextAlign.center,
          style: TextStyle(fontSize: 13, color: AppColors.ink2)),
        const SizedBox(height: 24),

        _DateField(label: 'DATE DE DÉBUT *',
          value: _debut == null ? null : _fmtDate(_ymd(_debut!)), onTap: _pickDebut),
        const SizedBox(height: 14),
        _DateField(label: 'TERME PRÉVU (DPA) *',
          value: _dpa == null ? null : _fmtDate(_ymd(_dpa!)), onTap: _pickDpa),
        const SizedBox(height: 14),

        const _Label('GROUPE SANGUIN (optionnel)'),
        const SizedBox(height: 6),
        Container(
          decoration: BoxDecoration(color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.border, width: 1.5)),
          padding: const EdgeInsets.symmetric(horizontal: 14),
          child: DropdownButtonHideUnderline(
            child: DropdownButton<String>(
              value: _groupe, isExpanded: true,
              hint: const Text('Sélectionner'),
              items: _groupesSanguins.map((g) =>
                DropdownMenuItem(value: g, child: Text(g))).toList(),
              onChanged: (v) => setState(() => _groupe = v),
            ),
          ),
        ),
        const SizedBox(height: 16),

        _Stepper(label: 'GROSSESSES ANTÉRIEURES', value: _grossessesAnt,
          onChanged: (v) => setState(() => _grossessesAnt = v)),
        const SizedBox(height: 14),
        _Stepper(label: 'ACCOUCHEMENTS ANTÉRIEURS', value: _accouchementsAnt,
          onChanged: (v) => setState(() => _accouchementsAnt = v)),
        const SizedBox(height: 16),

        const _Label('ANTÉCÉDENTS OBSTÉTRICAUX (optionnel)'),
        const SizedBox(height: 6),
        TextField(controller: _antecedentsCtrl, maxLines: 3,
          decoration: const InputDecoration(
            hintText: 'Diabète gestationnel, césarienne…')),
        const SizedBox(height: 24),

        _loading
            ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
            : ElevatedButton(onPressed: _soumettre,
                child: const Text('Démarrer le suivi')),
      ],
    );
  }
}

// ══════════════════════════════════════════════════════════════════════════════
// VUE COMPTEUR DE MOUVEMENTS
// ══════════════════════════════════════════════════════════════════════════════
class MouvementsView extends StatefulWidget {
  const MouvementsView({super.key});
  @override
  State<MouvementsView> createState() => _MouvementsViewState();
}

class _MouvementsViewState extends State<MouvementsView> {
  bool _loading = true;
  String? _error;
  int  _total      = 0;
  int  _objectif   = 10;
  bool _atteint    = false;
  List<MouvementBebe> _mouvements = [];

  // Saisie
  int    _count     = 1;
  String _intensite = 'modere';
  bool   _saving    = false;

  static const _intensites = {'leger': 'Léger', 'modere': 'Modéré', 'fort': 'Fort'};

  @override
  void initState() {
    super.initState();
    _charger();
  }

  Future<void> _charger() async {
    setState(() { _loading = true; _error = null; });
    final resume = await ApiService.instance.get('/patient/grossesse/mouvements/resume-jour');
    if (!mounted) return;
    if (resume.status == 404) {
      setState(() { _error = 'Aucune grossesse active. Démarrez d\'abord votre suivi de grossesse.'; _loading = false; });
      return;
    }
    if (resume.ok) {
      final d = resume.data as Map<String, dynamic>;
      _total    = (d['total_mouvements'] as int?) ?? 0;
      _objectif = (d['objectif_journalier'] as int?) ?? 10;
      _atteint  = d['objectif_atteint'] as bool? ?? false;
    }
    final liste = await ApiService.instance.get('/patient/grossesse/mouvements');
    if (!mounted) return;
    if (liste.ok) {
      _mouvements = ((liste.data as Map<String, dynamic>)['data'] as List? ?? [])
          .map((e) => MouvementBebe.fromJson(e as Map<String, dynamic>))
          .toList();
    }
    setState(() => _loading = false);
  }

  void _snack(String m, {bool erreur = false}) =>
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(m),
        backgroundColor: erreur ? AppColors.danger : null,
        behavior: SnackBarBehavior.floating));

  Future<void> _enregistrer() async {
    setState(() => _saving = true);
    final now = DateTime.now();
    final res = await ApiService.instance.post('/patient/grossesse/mouvements', body: {
      'date_heure':        '${_ymd(now)} ${now.hour.toString().padLeft(2,'0')}:${now.minute.toString().padLeft(2,'0')}',
      'nombre_mouvements': _count,
      'intensite':         _intensite,
    });
    if (!mounted) return;
    setState(() => _saving = false);
    if (res.ok) { setState(() => _count = 1); _charger(); _snack('Mouvement enregistré 👶'); }
    else        { _snack(res.error ?? 'Erreur.', erreur: true); }
  }

  Future<void> _supprimer(MouvementBebe m) async {
    final res = await ApiService.instance.delete('/patient/grossesse/mouvements/${m.id}');
    if (!mounted) return;
    if (res.ok) { _charger(); }
    else        { _snack(res.error ?? 'Erreur.', erreur: true); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Mouvements bébé',
        style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700))),
      bottomNavigationBar: const BottomNav(currentIndex: 2),
      body: _loading
          ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
          : _error != null
              ? _ErreurWidget(message: _error!, onRetry: _charger)
              : _corps(),
    );
  }

  Widget _corps() {
    final progress = _objectif == 0 ? 0.0 : (_total / _objectif).clamp(0.0, 1.0);
    return RefreshIndicator(
      onRefresh: _charger,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 20, 16, 24),
        children: [
          // Compteur du jour
          Center(child: SizedBox(
            width: 180, height: 180,
            child: Stack(alignment: Alignment.center, children: [
              SizedBox(width: 180, height: 180,
                child: CircularProgressIndicator(
                  value: progress, strokeWidth: 12,
                  backgroundColor: AppColors.primarySoft,
                  valueColor: const AlwaysStoppedAnimation(AppColors.primary))),
              Column(mainAxisSize: MainAxisSize.min, children: [
                Text('$_total',
                  style: const TextStyle(fontSize: 48, fontWeight: FontWeight.w800,
                    color: AppColors.primaryDark, height: 1)),
                Text('/ $_objectif aujourd\'hui',
                  style: const TextStyle(fontSize: 13, color: AppColors.ink2)),
              ]),
            ]),
          )),
          const SizedBox(height: 12),
          if (_atteint)
            Center(child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
              decoration: BoxDecoration(color: AppColors.success.withValues(alpha: .12),
                borderRadius: BorderRadius.circular(20)),
              child: const Text('🎉 Objectif du jour atteint !',
                style: TextStyle(color: AppColors.success, fontWeight: FontWeight.w700, fontSize: 13)),
            )),
          const SizedBox(height: 24),

          // Saisie rapide
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.border)),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Text('Enregistrer un mouvement',
                style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: AppColors.ink)),
              const SizedBox(height: 14),
              const _Label('NOMBRE DE MOUVEMENTS'),
              const SizedBox(height: 8),
              _Stepper(label: '', value: _count, min: 1, max: 100,
                onChanged: (v) => setState(() => _count = v)),
              const SizedBox(height: 14),
              const _Label('INTENSITÉ'),
              const SizedBox(height: 8),
              Row(children: _intensites.entries.map((e) {
                final sel = _intensite == e.key;
                return Expanded(child: Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: GestureDetector(
                    onTap: () => setState(() => _intensite = e.key),
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      alignment: Alignment.center,
                      decoration: BoxDecoration(
                        color: sel ? AppColors.primary : Colors.white,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: sel ? AppColors.primary : AppColors.border, width: 1.5)),
                      child: Text(e.value, style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700,
                        color: sel ? Colors.white : AppColors.ink)),
                    ),
                  ),
                ));
              }).toList()),
              const SizedBox(height: 16),
              _saving
                  ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
                  : ElevatedButton.icon(
                      onPressed: _enregistrer,
                      icon: const Icon(Icons.add_rounded),
                      label: const Text('Enregistrer')),
            ]),
          ),

          const SizedBox(height: 24),
          const Text('Historique',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: AppColors.ink)),
          const SizedBox(height: 8),
          if (_mouvements.isEmpty)
            const Padding(padding: EdgeInsets.symmetric(vertical: 20),
              child: Center(child: Text('Aucun mouvement enregistré.',
                style: TextStyle(color: AppColors.ink2, fontSize: 13))))
          else
            ..._mouvements.map((m) => _MouvementCard(mouvement: m, onSupprimer: () => _supprimer(m))),
        ],
      ),
    );
  }
}

// ══════════════════════════════════════════════════════════════════════════════
// BOTTOM SHEET — AJOUT DE SUIVI
// ══════════════════════════════════════════════════════════════════════════════
class _SuiviSheet extends StatefulWidget {
  final int? saInitiale;
  final VoidCallback onConfirme;
  const _SuiviSheet({this.saInitiale, required this.onConfirme});
  @override
  State<_SuiviSheet> createState() => _SuiviSheetState();
}

class _SuiviSheetState extends State<_SuiviSheet> {
  late final TextEditingController _saCtrl;
  final _poidsCtrl   = TextEditingController();
  final _sysCtrl     = TextEditingController();
  final _diaCtrl     = TextEditingController();
  final _glyCtrl     = TextEditingController();
  final _notesCtrl   = TextEditingController();
  bool _loading = false;

  @override
  void initState() {
    super.initState();
    _saCtrl = TextEditingController(text: widget.saInitiale?.toString() ?? '');
  }

  @override
  void dispose() {
    for (final c in [_saCtrl, _poidsCtrl, _sysCtrl, _diaCtrl, _glyCtrl, _notesCtrl]) {
      c.dispose();
    }
    super.dispose();
  }

  void _snack(String m) => ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(content: Text(m), backgroundColor: AppColors.danger,
      behavior: SnackBarBehavior.floating));

  Future<void> _valider() async {
    final sa = int.tryParse(_saCtrl.text.trim());
    if (sa == null || sa < 1 || sa > 42) {
      _snack('Semaines d\'aménorrhée invalides (1–42).'); return;
    }
    setState(() => _loading = true);
    final now = DateTime.now();
    final res = await ApiService.instance.post('/patient/grossesse/suivis', body: {
      'semaines_amenorrhee': sa,
      if (_poidsCtrl.text.trim().isNotEmpty) 'poids_kg': num.tryParse(_poidsCtrl.text.trim()),
      if (_sysCtrl.text.trim().isNotEmpty)   'tension_systolique': int.tryParse(_sysCtrl.text.trim()),
      if (_diaCtrl.text.trim().isNotEmpty)   'tension_diastolique': int.tryParse(_diaCtrl.text.trim()),
      if (_glyCtrl.text.trim().isNotEmpty)   'glycemie': num.tryParse(_glyCtrl.text.trim()),
      if (_notesCtrl.text.trim().isNotEmpty) 'notes': _notesCtrl.text.trim(),
      'date_saisie': _ymd(now),
    });
    if (!mounted) return;
    setState(() => _loading = false);
    if (res.ok) { Navigator.pop(context); widget.onConfirme(); }
    else        { _snack(res.error ?? 'Erreur lors de l\'enregistrement.'); }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(color: AppColors.surface,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      padding: EdgeInsets.fromLTRB(20, 16, 20,
        MediaQuery.of(context).viewInsets.bottom + 24),
      child: SingleChildScrollView(child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Center(child: Container(width: 36, height: 4,
            decoration: BoxDecoration(color: AppColors.border,
              borderRadius: BorderRadius.circular(2)))),
          const SizedBox(height: 16),
          const Text('Nouveau suivi médical',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.ink)),
          const SizedBox(height: 16),
          const _Label('SEMAINES D\'AMÉNORRHÉE *'),
          const SizedBox(height: 6),
          TextField(controller: _saCtrl, keyboardType: TextInputType.number,
            decoration: const InputDecoration(hintText: 'Ex : 24')),
          const SizedBox(height: 14),
          Row(children: [
            Expanded(child: _miniField('POIDS (kg)', _poidsCtrl)),
            const SizedBox(width: 12),
            Expanded(child: _miniField('GLYCÉMIE', _glyCtrl)),
          ]),
          const SizedBox(height: 14),
          Row(children: [
            Expanded(child: _miniField('TENSION SYS.', _sysCtrl)),
            const SizedBox(width: 12),
            Expanded(child: _miniField('TENSION DIA.', _diaCtrl)),
          ]),
          const SizedBox(height: 14),
          const _Label('NOTES (optionnel)'),
          const SizedBox(height: 6),
          TextField(controller: _notesCtrl, maxLines: 2,
            decoration: const InputDecoration(hintText: 'Observations…')),
          const SizedBox(height: 20),
          _loading
              ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
              : ElevatedButton(onPressed: _valider, child: const Text('Enregistrer le suivi')),
        ],
      )),
    );
  }

  Widget _miniField(String label, TextEditingController ctrl) =>
    Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      _Label(label),
      const SizedBox(height: 6),
      TextField(controller: ctrl, keyboardType: TextInputType.number,
        decoration: const InputDecoration(hintText: '—')),
    ]);
}

// ══════════════════════════════════════════════════════════════════════════════
// WIDGETS PARTAGÉS
// ══════════════════════════════════════════════════════════════════════════════
class _InfoTile extends StatelessWidget {
  final IconData icon;
  final String label, value;
  const _InfoTile({required this.icon, required this.label, required this.value});
  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.all(14),
    decoration: BoxDecoration(color: Colors.white,
      borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.border)),
    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Icon(icon, size: 18, color: AppColors.primary),
      const SizedBox(height: 8),
      Text(value, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: AppColors.ink)),
      Text(label, style: const TextStyle(fontSize: 11, color: AppColors.ink2)),
    ]),
  );
}

class _SuiviCard extends StatelessWidget {
  final SuiviGrossesse suivi;
  final VoidCallback onSupprimer;
  const _SuiviCard({required this.suivi, required this.onSupprimer});
  @override
  Widget build(BuildContext context) => Container(
    margin: const EdgeInsets.only(bottom: 10),
    padding: const EdgeInsets.all(14),
    decoration: BoxDecoration(color: Colors.white,
      borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.border)),
    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Row(children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
          decoration: BoxDecoration(color: AppColors.primarySoft,
            borderRadius: BorderRadius.circular(20)),
          child: Text('${suivi.semainesAmenorrhee} SA',
            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: AppColors.primary))),
        const Spacer(),
        Text(_fmtDate(suivi.dateSaisie),
          style: const TextStyle(fontSize: 12, color: AppColors.ink2)),
        IconButton(
          visualDensity: VisualDensity.compact,
          icon: const Icon(Icons.delete_outline_rounded, size: 18, color: AppColors.ink2),
          onPressed: onSupprimer),
      ]),
      const SizedBox(height: 6),
      Wrap(spacing: 16, runSpacing: 4, children: [
        if (suivi.poidsKg != null) _metric('⚖️', '${suivi.poidsKg} kg'),
        if (suivi.tension != null) _metric('🩺', '${suivi.tension} mmHg'),
        if (suivi.glycemie != null) _metric('🩸', '${suivi.glycemie} g/L'),
      ]),
      if (suivi.notes != null && suivi.notes!.isNotEmpty) ...[
        const SizedBox(height: 6),
        Text(suivi.notes!, style: const TextStyle(fontSize: 12, color: AppColors.ink2)),
      ],
    ]),
  );

  Widget _metric(String emoji, String val) => Text('$emoji $val',
    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.ink));
}

class _MouvementCard extends StatelessWidget {
  final MouvementBebe mouvement;
  final VoidCallback onSupprimer;
  const _MouvementCard({required this.mouvement, required this.onSupprimer});
  @override
  Widget build(BuildContext context) => Container(
    margin: const EdgeInsets.only(bottom: 8),
    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
    decoration: BoxDecoration(color: Colors.white,
      borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.border)),
    child: Row(children: [
      CircleAvatar(radius: 18, backgroundColor: AppColors.primarySoft,
        child: Text('${mouvement.nombreMouvements}',
          style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 14))),
      const SizedBox(width: 12),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text('${mouvement.nombreMouvements} mouvement${mouvement.nombreMouvements > 1 ? 's' : ''}'
            '${mouvement.intensiteLabel != null ? ' • ${mouvement.intensiteLabel}' : ''}',
          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.ink)),
        Text(_fmtDateHeure(mouvement.dateTime),
          style: const TextStyle(fontSize: 12, color: AppColors.ink2)),
      ])),
      IconButton(
        visualDensity: VisualDensity.compact,
        icon: const Icon(Icons.delete_outline_rounded, size: 18, color: AppColors.ink2),
        onPressed: onSupprimer),
    ]),
  );
}

class _DateField extends StatelessWidget {
  final String label;
  final String? value;
  final VoidCallback onTap;
  const _DateField({required this.label, required this.value, required this.onTap});
  @override
  Widget build(BuildContext context) =>
    Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      _Label(label),
      const SizedBox(height: 6),
      GestureDetector(
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          decoration: BoxDecoration(color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.border, width: 1.5)),
          child: Row(children: [
            const Icon(Icons.calendar_today_rounded, color: AppColors.primary, size: 18),
            const SizedBox(width: 10),
            Text(value ?? 'Choisir une date',
              style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600,
                color: value == null ? AppColors.ink2 : AppColors.ink)),
            const Spacer(),
            const Icon(Icons.chevron_right_rounded, color: AppColors.ink2),
          ]),
        ),
      ),
    ]);
}

class _Stepper extends StatelessWidget {
  final String label;
  final int value, min, max;
  final ValueChanged<int> onChanged;
  const _Stepper({required this.label, required this.value,
    required this.onChanged, this.min = 0, this.max = 20});
  @override
  Widget build(BuildContext context) {
    final row = Row(children: [
      _btn(Icons.remove, () { if (value > min) onChanged(value - 1); }),
      Expanded(child: Center(child: Text('$value',
        style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.ink)))),
      _btn(Icons.add, () { if (value < max) onChanged(value + 1); }),
    ]);
    if (label.isEmpty) return row;
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      _Label(label), const SizedBox(height: 8), row,
    ]);
  }

  Widget _btn(IconData icon, VoidCallback onTap) => InkWell(
    onTap: onTap, borderRadius: BorderRadius.circular(10),
    child: Container(
      width: 42, height: 42,
      decoration: BoxDecoration(color: AppColors.primarySoft,
        borderRadius: BorderRadius.circular(10)),
      child: Icon(icon, color: AppColors.primary, size: 20)),
  );
}

class _Label extends StatelessWidget {
  final String text;
  const _Label(this.text);
  @override
  Widget build(BuildContext context) => Text(text,
    style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700,
      color: AppColors.ink2, letterSpacing: 0.8));
}

class _ErreurWidget extends StatelessWidget {
  final String message;
  final VoidCallback onRetry;
  const _ErreurWidget({required this.message, required this.onRetry});
  @override
  Widget build(BuildContext context) => Center(
    child: Padding(
      padding: const EdgeInsets.all(32),
      child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
        const Icon(Icons.error_outline_rounded, size: 48, color: AppColors.ink2),
        const SizedBox(height: 12),
        Text(message, textAlign: TextAlign.center,
          style: const TextStyle(color: AppColors.ink2, fontSize: 13)),
        const SizedBox(height: 14),
        ElevatedButton.icon(onPressed: onRetry,
          icon: const Icon(Icons.refresh_rounded, size: 16),
          label: const Text('Réessayer')),
      ]),
    ),
  );
}
