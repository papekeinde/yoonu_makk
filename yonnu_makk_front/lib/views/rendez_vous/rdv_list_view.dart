import 'dart:async';
import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../models/gynecologue.dart';
import '../../models/rendez_vous.dart';
import '../../services/api_service.dart';
import '../../widgets/bottom_nav.dart';
import '../../widgets/chatbot_fab.dart';

// ─── VUE RENDEZ-VOUS ──────────────────────────────────────────────────────────
// Onglet 1 "Mes RDV"      : appels réels GET /api/patient/rendez-vous
// Onglet 2 "Spécialistes" : GET /api/gynecologues?search=…&specialite=…
class RdvListView extends StatefulWidget {
  const RdvListView({super.key});
  @override
  State<RdvListView> createState() => _RdvListViewState();
}

class _RdvListViewState extends State<RdvListView>
    with SingleTickerProviderStateMixin {
  late final TabController _tabs;

  // ── Onglet Mes RDV ─────────────────────────────────────────────────────────
  List<RendezVous> _rdvs    = [];
  bool  _rdvLoading         = true;
  String? _rdvError;

  // ── Onglet Spécialistes ────────────────────────────────────────────────────
  List<Gynecologue> _gyneco = [];
  bool   _gynecoLoading     = true;
  bool   _loadingMore       = false;
  String? _gynecoError;
  String  _searchQuery      = '';
  String  _villeQuery       = '';
  String  _filtreSpec       = '';
  int     _page             = 1;
  int     _lastPage         = 1;
  Timer?  _debounce;
  final _searchCtrl = TextEditingController();
  final _villeCtrl  = TextEditingController();
  final _scrollCtrl = ScrollController();

  static const _specs = [
    '', // tous
    'Gynécologie-obstétrique',
    'Gynécologie médicale',
    'Obstétrique',
    'Sage-femme',
    'Endocrinologie',
    'Médecine générale',
  ];

  // ── Formatage date ─────────────────────────────────────────────────────────
  static const _mois = ['jan','fév','mar','avr','mai','juin',
                         'jul','aoû','sep','oct','nov','déc'];
  static String _fmt(DateTime d) => '${d.day} ${_mois[d.month - 1]} ${d.year}';

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 2, vsync: this);
    _tabs.addListener(() {
      if (!_tabs.indexIsChanging) setState(() {});
    });
    _scrollCtrl.addListener(_onScroll);
    _chargerRdvs();
    _chargerGynecologues();
  }

  @override
  void dispose() {
    _tabs.dispose();
    _searchCtrl.dispose();
    _villeCtrl.dispose();
    _scrollCtrl.dispose();
    _debounce?.cancel();
    super.dispose();
  }

  // Charge la page suivante quand on approche du bas de la liste.
  void _onScroll() {
    if (_scrollCtrl.position.pixels >=
        _scrollCtrl.position.maxScrollExtent - 300) {
      _chargerGynecologues(reset: false);
    }
  }

  // ── Chargement Mes RDV ────────────────────────────────────────────────────
  Future<void> _chargerRdvs() async {
    setState(() { _rdvLoading = true; _rdvError = null; });
    final res = await ApiService.instance.get('/patient/rendez-vous');
    if (!mounted) return;
    if (res.ok) {
      final items = (res.data['data'] as List? ?? [])
          .map((e) => RendezVous.fromJson(e as Map<String, dynamic>))
          .toList();
      setState(() { _rdvs = items; _rdvLoading = false; });
    } else {
      setState(() { _rdvError = res.error ?? 'Erreur de chargement'; _rdvLoading = false; });
    }
  }

  // ── Chargement gynécologues (paginé) ───────────────────────────────────────
  // reset = true  → nouvelle recherche (page 1, remplace la liste)
  // reset = false → page suivante (ajoute à la liste existante)
  Future<void> _chargerGynecologues({bool reset = true}) async {
    if (reset) {
      setState(() { _gynecoLoading = true; _gynecoError = null; _page = 1; });
    } else {
      if (_loadingMore || _gynecoLoading || _page >= _lastPage) return;
      setState(() => _loadingMore = true);
    }

    final pageDemandee = reset ? 1 : _page + 1;
    final q = <String, String>{'page': pageDemandee.toString()};
    if (_searchQuery.isNotEmpty) q['search']     = _searchQuery;
    if (_villeQuery.isNotEmpty)  q['ville']       = _villeQuery;
    if (_filtreSpec.isNotEmpty)  q['specialite']  = _filtreSpec;

    final res = await ApiService.instance.get('/gynecologues', query: q);
    if (!mounted) return;

    if (res.ok) {
      final items = (res.data['data'] as List? ?? [])
          .map((e) => Gynecologue.fromJson(e as Map<String, dynamic>))
          .toList();
      final meta = res.data['meta'] as Map<String, dynamic>?;
      setState(() {
        _page     = (meta?['current_page'] as int?) ?? pageDemandee;
        _lastPage = (meta?['last_page'] as int?) ?? _page;
        if (reset) { _gyneco = items; _gynecoLoading = false; }
        else       { _gyneco = [..._gyneco, ...items]; _loadingMore = false; }
      });
    } else {
      setState(() {
        if (reset) { _gynecoError = res.error ?? 'Erreur'; _gynecoLoading = false; }
        else       { _loadingMore = false; }
      });
    }
  }

  // Recherche nom + ville, déclenchée avec un léger délai (debounce).
  void _onFiltreChanged(String _) {
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 400), () {
      setState(() {
        _searchQuery = _searchCtrl.text.trim();
        _villeQuery  = _villeCtrl.text.trim();
      });
      _chargerGynecologues();
    });
  }

  // ── Annuler un RDV ────────────────────────────────────────────────────────
  Future<void> _annulerRdv(RendezVous rdv) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Annuler ce rendez-vous ?',
          style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16)),
        content: Text('RDV avec ${rdv.nomMedecin} le ${_fmt(rdv.dateTime)}.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Non')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.danger, foregroundColor: Colors.white),
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Annuler le RDV'),
          ),
        ],
      ),
    );
    if (confirm != true) return;
    final res = await ApiService.instance.delete('/patient/rendez-vous/${rdv.id}');
    if (!mounted) return;
    if (res.ok) {
      _chargerRdvs();
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
        content: Text('Rendez-vous annulé.'),
        behavior: SnackBarBehavior.floating));
    } else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(res.error ?? 'Impossible d\'annuler.'),
        backgroundColor: AppColors.danger, behavior: SnackBarBehavior.floating));
    }
  }

  // ── Prendre RDV depuis un gynécologue ────────────────────────────────────
  void _prendreRdvAvec(Gynecologue g) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _RdvSheet(
        gyneco: g,
        onConfirme: (_) { _chargerRdvs(); _tabs.animateTo(0); },
      ),
    );
  }

  // ── Prendre RDV libre (FAB) ──────────────────────────────────────────────
  void _prendreRdvLibre() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _RdvSheet(
        gyneco: _gyneco.isNotEmpty ? null : null,
        gynecologues: _gyneco,
        onConfirme: (_) => _chargerRdvs(),
      ),
    );
  }

  // ══════════════════════════════════════════════════════════════════════════════
  // BUILD
  // ══════════════════════════════════════════════════════════════════════════════
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      floatingActionButton: _tabs.index == 0
          ? FloatingActionButton.extended(
              onPressed: _prendreRdvLibre,
              backgroundColor: AppColors.primary, foregroundColor: Colors.white,
              icon: const Icon(Icons.add_rounded),
              label: const Text('Prendre RDV',
                style: TextStyle(fontWeight: FontWeight.w700)),
            )
          : const ChatbotFab(),
      appBar: AppBar(
        title: const Text('Rendez-vous',
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
        bottom: TabBar(
          controller: _tabs,
          indicatorColor: AppColors.primary,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.ink2,
          labelStyle: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13),
          tabs: const [
            Tab(icon: Icon(Icons.calendar_today_rounded, size: 18), text: 'Mes RDV'),
            Tab(icon: Icon(Icons.search_rounded, size: 18), text: 'Spécialistes'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabs,
        children: [_ongletRdvs(), _ongletSpecialistes()],
      ),
      bottomNavigationBar: const BottomNav(currentIndex: 3),
    );
  }

  // ══════════════════════════════════════════════════════════════════════════════
  // ONGLET 1 — MES RDV
  // ══════════════════════════════════════════════════════════════════════════════
  Widget _ongletRdvs() {
    if (_rdvLoading) {
      return const Center(child: CircularProgressIndicator(color: AppColors.primary));
    }
    if (_rdvError != null) {
      return _erreurWidget(_rdvError!, _chargerRdvs);
    }
    final aVenir = _rdvs.where((r) => r.aVenir).toList();
    final passes = _rdvs.where((r) => !r.aVenir).toList();

    return RefreshIndicator(
      onRefresh: _chargerRdvs,
      color: AppColors.primary,
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
        children: [
          _sectionHeader('À VENIR', count: aVenir.length),
          if (aVenir.isEmpty)
            _emptyCard('Aucun rendez-vous à venir', '📅',
              'Appuyez sur "Prendre RDV" pour consulter un spécialiste.'),
          ...aVenir.map((r) => _RdvCard(
            rdv: r,
            onAnnuler: r.statut == 'en_attente' ? () => _annulerRdv(r) : null,
            fmt: _fmt,
          )),
          const SizedBox(height: 20),
          _sectionHeader('PASSÉS', count: passes.length),
          if (passes.isEmpty)
            _emptyCard('Aucun rendez-vous passé', '📋', null),
          ...passes.map((r) => _RdvCard(rdv: r, fmt: _fmt)),
        ],
      ),
    );
  }

  // ══════════════════════════════════════════════════════════════════════════════
  // ONGLET 2 — SPÉCIALISTES
  // ══════════════════════════════════════════════════════════════════════════════
  Widget _ongletSpecialistes() {
    return Column(children: [
      // Barre de recherche (nom + ville)
      Container(
        color: AppColors.surface,
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
        child: Column(children: [
          TextField(
            controller: _searchCtrl,
            onChanged: _onFiltreChanged,
            decoration: _filtreDecoration(
              hint: 'Rechercher un médecin…',
              icon: Icons.search_rounded,
              ctrl: _searchCtrl,
            ),
          ),
          const SizedBox(height: 8),
          TextField(
            controller: _villeCtrl,
            onChanged: _onFiltreChanged,
            decoration: _filtreDecoration(
              hint: 'Filtrer par ville…',
              icon: Icons.location_on_outlined,
              ctrl: _villeCtrl,
            ),
          ),
        ]),
      ),
      // Chips spécialités
      SizedBox(
        height: 44,
        child: ListView.separated(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
          separatorBuilder: (_, idx) => const SizedBox(width: 8),
          itemCount: _specs.length,
          itemBuilder: (_, i) {
            final spec   = _specs[i];
            final label  = spec.isEmpty ? 'Tous' : spec;
            final active = _filtreSpec == spec;
            return GestureDetector(
              onTap: () { setState(() => _filtreSpec = spec); _chargerGynecologues(); },
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 180),
                padding: const EdgeInsets.symmetric(horizontal: 14),
                decoration: BoxDecoration(
                  color: active ? AppColors.primary : Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                    color: active ? AppColors.primary : AppColors.border)),
                alignment: Alignment.center,
                child: Text(label, style: TextStyle(
                  fontSize: 12, fontWeight: FontWeight.w700,
                  color: active ? Colors.white : AppColors.ink2)),
              ),
            );
          },
        ),
      ),
      // Liste
      Expanded(child: _corpsSpecialistes()),
    ]);
  }

  // Décoration commune des champs de filtre (avec bouton "effacer").
  InputDecoration _filtreDecoration({
    required String hint,
    required IconData icon,
    required TextEditingController ctrl,
  }) => InputDecoration(
    hintText: hint,
    prefixIcon: Icon(icon, color: AppColors.ink2, size: 20),
    suffixIcon: ctrl.text.isNotEmpty
        ? IconButton(
            icon: const Icon(Icons.clear_rounded, size: 18, color: AppColors.ink2),
            onPressed: () { ctrl.clear(); _onFiltreChanged(''); })
        : null,
    filled: true, fillColor: AppColors.bg,
    contentPadding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
    border: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
      borderSide: const BorderSide(color: AppColors.border)),
    enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
      borderSide: const BorderSide(color: AppColors.border)),
    focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
      borderSide: const BorderSide(color: AppColors.primary, width: 2)),
  );

  Widget _corpsSpecialistes() {
    if (_gynecoLoading) {
      return const Center(child: CircularProgressIndicator(color: AppColors.primary));
    }
    if (_gynecoError != null) {
      return _erreurWidget(_gynecoError!, _chargerGynecologues);
    }
    if (_gyneco.isEmpty) {
      return _emptyCard('Aucun spécialiste trouvé', '🔍',
        'Essayez un autre terme, une autre ville ou une autre spécialité.');
    }
    return RefreshIndicator(
      onRefresh: _chargerGynecologues,
      color: AppColors.primary,
      child: ListView.separated(
        controller: _scrollCtrl,
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
        // +1 ligne d'en-tête (compteur) et +1 footer de chargement éventuel
        itemCount: _gyneco.length + 1 + (_loadingMore ? 1 : 0),
        separatorBuilder: (_, idx) => const SizedBox(height: 10),
        itemBuilder: (_, i) {
          if (i == 0) {
            final total = _gyneco.length;
            final suffixe = _page < _lastPage ? '+' : '';
            return Padding(
              padding: const EdgeInsets.only(bottom: 4),
              child: Text('$total$suffixe spécialiste${total > 1 ? 's' : ''} disponible${total > 1 ? 's' : ''}',
                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.ink2)),
            );
          }
          final idx = i - 1;
          if (idx >= _gyneco.length) {
            return const Padding(
              padding: EdgeInsets.symmetric(vertical: 16),
              child: Center(child: CircularProgressIndicator(color: AppColors.primary)),
            );
          }
          return _GynecoCard(
            gyneco: _gyneco[idx],
            onReserver: () => _prendreRdvAvec(_gyneco[idx]),
          );
        },
      ),
    );
  }

  // ── Helpers UI ─────────────────────────────────────────────────────────────
  Widget _sectionHeader(String titre, {int count = 0}) => Padding(
    padding: const EdgeInsets.only(bottom: 10),
    child: Row(children: [
      Text(titre, style: const TextStyle(
        fontSize: 11, fontWeight: FontWeight.w800,
        color: AppColors.ink2, letterSpacing: 0.8)),
      const SizedBox(width: 8),
      Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
        decoration: BoxDecoration(
          color: AppColors.primarySoft, borderRadius: BorderRadius.circular(10)),
        child: Text('$count', style: const TextStyle(
          fontSize: 11, fontWeight: FontWeight.w800, color: AppColors.primary)),
      ),
    ]),
  );

  Widget _emptyCard(String msg, String emoji, String? sub) => Container(
    margin: const EdgeInsets.symmetric(vertical: 8),
    padding: const EdgeInsets.all(24),
    decoration: BoxDecoration(
      color: Colors.white, borderRadius: BorderRadius.circular(16),
      border: Border.all(color: AppColors.border)),
    child: Column(children: [
      Text(emoji, style: const TextStyle(fontSize: 36)),
      const SizedBox(height: 10),
      Text(msg, style: const TextStyle(
        fontWeight: FontWeight.w700, color: AppColors.ink, fontSize: 14)),
      if (sub != null) ...[
        const SizedBox(height: 4),
        Text(sub, style: const TextStyle(fontSize: 12, color: AppColors.ink2),
          textAlign: TextAlign.center),
      ],
    ]),
  );

  Widget _erreurWidget(String msg, VoidCallback retry) => Center(
    child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
      const Icon(Icons.wifi_off_rounded, size: 48, color: AppColors.ink2),
      const SizedBox(height: 12),
      Text(msg, style: const TextStyle(color: AppColors.ink2, fontSize: 13)),
      const SizedBox(height: 14),
      ElevatedButton.icon(
        onPressed: retry,
        icon: const Icon(Icons.refresh_rounded, size: 16),
        label: const Text('Réessayer'),
      ),
    ]),
  );
}

// ─── CARTE RENDEZ-VOUS ────────────────────────────────────────────────────────
class _RdvCard extends StatelessWidget {
  final RendezVous rdv;
  final VoidCallback? onAnnuler;
  final String Function(DateTime) fmt;
  const _RdvCard({required this.rdv, required this.fmt, this.onAnnuler});

  static const _statutColors = <String, Color>{
    'confirme':  AppColors.success,
    'en_attente': AppColors.warning,
    'refuse':    AppColors.danger,
    'annule':    AppColors.danger,
    'termine':   AppColors.ink2,
  };
  static const _statutLabels = <String, String>{
    'confirme':  'Confirmé',
    'en_attente': 'En attente',
    'refuse':    'Refusé',
    'annule':    'Annulé',
    'termine':   'Terminé',
  };

  Color get _c => _statutColors[rdv.statut] ?? AppColors.ink2;
  String get _l => _statutLabels[rdv.statut] ?? rdv.statut;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: BoxDecoration(
        color: Colors.white, borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
        boxShadow: [BoxShadow(
          color: Colors.black.withValues(alpha: .04), blurRadius: 8)],
      ),
      child: Column(children: [
        // Bande colorée selon statut
        Container(
          height: 4,
          decoration: BoxDecoration(
            color: _c,
            borderRadius: const BorderRadius.vertical(top: Radius.circular(16))),
        ),
        Padding(
          padding: const EdgeInsets.all(14),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Row(children: [
              CircleAvatar(radius: 22, backgroundColor: AppColors.primarySoft,
                child: Text(rdv.nomMedecin.isNotEmpty ? rdv.nomMedecin[0] : 'G',
                  style: const TextStyle(color: AppColors.primary,
                    fontWeight: FontWeight.w800, fontSize: 16))),
              const SizedBox(width: 12),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(rdv.nomMedecin,
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700,
                    color: AppColors.ink)),
                if (rdv.specialiteMedecin.isNotEmpty)
                  Text(rdv.specialiteMedecin,
                    style: const TextStyle(fontSize: 12, color: AppColors.ink2)),
              ])),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: _c.withValues(alpha: .12),
                  borderRadius: BorderRadius.circular(20)),
                child: Text(_l,
                  style: TextStyle(fontSize: 11, color: _c, fontWeight: FontWeight.w700)),
              ),
            ]),
            const Divider(color: AppColors.border, height: 18),
            Row(children: [
              const Icon(Icons.calendar_today_rounded, size: 14, color: AppColors.primary),
              const SizedBox(width: 6),
              Text('${fmt(rdv.dateTime)} à ${rdv.heureAffichee}',
                style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600,
                  color: AppColors.ink)),
            ]),
            if (rdv.motif != null) ...[
              const SizedBox(height: 5),
              Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
                const Icon(Icons.assignment_outlined, size: 14, color: AppColors.ink2),
                const SizedBox(width: 6),
                Expanded(child: Text(rdv.motif!,
                  style: const TextStyle(fontSize: 12, color: AppColors.ink2))),
              ]),
            ],
            if (rdv.gynecologue?.structureSante.isNotEmpty == true) ...[
              const SizedBox(height: 5),
              Row(children: [
                const Icon(Icons.location_on_outlined, size: 14, color: AppColors.ink2),
                const SizedBox(width: 6),
                Expanded(child: Text(rdv.gynecologue!.structureSante,
                  style: const TextStyle(fontSize: 12, color: AppColors.ink2))),
              ]),
            ],
            if (onAnnuler != null) ...[
              const SizedBox(height: 10),
              Align(alignment: Alignment.centerRight,
                child: TextButton.icon(
                  onPressed: onAnnuler,
                  icon: const Icon(Icons.cancel_outlined, size: 14, color: AppColors.danger),
                  label: const Text('Annuler',
                    style: TextStyle(color: AppColors.danger, fontSize: 12,
                      fontWeight: FontWeight.w600)),
                  style: TextButton.styleFrom(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    backgroundColor: AppColors.danger.withValues(alpha: .06),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8))),
                )),
            ],
          ]),
        ),
      ]),
    );
  }
}

// ─── CARTE GYNÉCOLOGUE ───────────────────────────────────────────────────────
class _GynecoCard extends StatelessWidget {
  final Gynecologue gyneco;
  final VoidCallback onReserver;
  const _GynecoCard({required this.gyneco, required this.onReserver});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white, borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
        boxShadow: [BoxShadow(
          color: Colors.black.withValues(alpha: .04), blurRadius: 8)],
      ),
      child: Row(children: [
        CircleAvatar(
          radius: 26, backgroundColor: AppColors.primarySoft,
          child: Text(gyneco.initiale,
            style: const TextStyle(color: AppColors.primary,
              fontWeight: FontWeight.w800, fontSize: 18))),
        const SizedBox(width: 12),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(gyneco.nomComplet,
            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: AppColors.ink)),
          Text(gyneco.specialite,
            style: const TextStyle(fontSize: 12, color: AppColors.primary,
              fontWeight: FontWeight.w600)),
          const SizedBox(height: 3),
          Row(children: [
            const Icon(Icons.location_on_outlined, size: 13, color: AppColors.ink2),
            const SizedBox(width: 3),
            Text(gyneco.ville, style: const TextStyle(fontSize: 11, color: AppColors.ink2)),
            const SizedBox(width: 8),
            const Icon(Icons.business_outlined, size: 13, color: AppColors.ink2),
            const SizedBox(width: 3),
            Expanded(child: Text(gyneco.structureSante,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 11, color: AppColors.ink2))),
          ]),
          if (gyneco.anneesExperience > 0) ...[
            const SizedBox(height: 3),
            Text('${gyneco.anneesExperience} ans d\'expérience',
              style: const TextStyle(fontSize: 11, color: AppColors.success,
                fontWeight: FontWeight.w700)),
          ],
          const SizedBox(height: 3),
          Row(children: [
            const Icon(Icons.payments_outlined, size: 13, color: AppColors.primary),
            const SizedBox(width: 4),
            Text(gyneco.tarifAffiche ?? 'Tarif sur demande',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800,
                color: gyneco.tarifAffiche != null ? AppColors.primary : AppColors.ink2)),
          ]),
        ])),
        const SizedBox(width: 8),
        GestureDetector(
          onTap: onReserver,
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 9),
            decoration: BoxDecoration(
              color: AppColors.primary, borderRadius: BorderRadius.circular(12)),
            child: const Column(mainAxisSize: MainAxisSize.min, children: [
              Icon(Icons.calendar_month_rounded, size: 15, color: Colors.white),
              SizedBox(height: 2),
              Text('Réserver', style: TextStyle(color: Colors.white,
                fontSize: 11, fontWeight: FontWeight.w700)),
            ]),
          ),
        ),
      ]),
    );
  }
}

// ─── BOTTOM SHEET — PRISE DE RDV ─────────────────────────────────────────────
class _RdvSheet extends StatefulWidget {
  final Gynecologue?       gyneco;
  final List<Gynecologue>  gynecologues;
  final ValueChanged<void> onConfirme;
  const _RdvSheet({
    this.gyneco,
    this.gynecologues = const [],
    required this.onConfirme,
  });
  @override
  State<_RdvSheet> createState() => _RdvSheetState();
}

class _RdvSheetState extends State<_RdvSheet> {
  final _motifCtrl = TextEditingController();
  DateTime  _date   = DateTime.now().add(const Duration(days: 3));
  String    _heure  = '09:00';
  bool      _loading = false;
  Gynecologue? _gyneco;

  static const _heures = ['07:00','08:00','09:00','10:00','11:00',
                           '14:00','15:00','16:00','17:00'];
  static const _mois = ['jan','fév','mar','avr','mai','juin',
                         'jul','aoû','sep','oct','nov','déc'];

  @override
  void initState() {
    super.initState();
    _gyneco = widget.gyneco;
  }

  @override
  void dispose() { _motifCtrl.dispose(); super.dispose(); }

  String _fmt(DateTime d) => '${d.day} ${_mois[d.month - 1]} ${d.year}';

  Future<void> _choisirDate() async {
    final d = await showDatePicker(
      context: context,
      initialDate: _date,
      firstDate: DateTime.now().add(const Duration(days: 1)),
      lastDate: DateTime.now().add(const Duration(days: 180)),
    );
    if (d != null) setState(() => _date = d);
  }

  // Sélecteur d'heure libre (toutes les heures possibles, pas seulement les créneaux).
  Future<void> _choisirHeureLibre() async {
    final parts = _heure.split(':');
    final t = await showTimePicker(
      context: context,
      initialEntryMode: TimePickerEntryMode.input,
      initialTime: TimeOfDay(
        hour:   int.tryParse(parts.first) ?? 9,
        minute: parts.length > 1 ? (int.tryParse(parts[1]) ?? 0) : 0),
    );
    if (t != null) {
      setState(() => _heure =
        '${t.hour.toString().padLeft(2, '0')}:${t.minute.toString().padLeft(2, '0')}');
    }
  }

  Widget _chipHeure(String label, bool sel, VoidCallback? onTap, {IconData? icon}) =>
    GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: sel ? AppColors.primary : Colors.white,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(
            color: sel ? AppColors.primary : AppColors.border, width: 1.5)),
        child: Row(mainAxisSize: MainAxisSize.min, children: [
          if (icon != null) ...[
            Icon(icon, size: 14, color: sel ? Colors.white : AppColors.primary),
            const SizedBox(width: 4),
          ],
          Text(label, style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700,
            color: sel ? Colors.white : AppColors.ink)),
        ]),
      ),
    );

  Future<void> _valider() async {
    if (_gyneco == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
        content: Text('Choisissez un médecin.'),
        behavior: SnackBarBehavior.floating)); return;
    }
    setState(() => _loading = true);
    final res = await ApiService.instance.post('/patient/rendez-vous', body: {
      'gynecologue_id':  _gyneco!.id,
      'date_souhaitee':  '${_date.year}-${_date.month.toString().padLeft(2,'0')}-${_date.day.toString().padLeft(2,'0')}',
      'heure_souhaitee': _heure,
      'motif':           _motifCtrl.text.trim().isEmpty ? null : _motifCtrl.text.trim(),
    });
    if (!mounted) return;
    setState(() => _loading = false);
    if (res.ok) {
      Navigator.pop(context);
      widget.onConfirme(null);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
        content: Text('Demande de rendez-vous envoyée !'),
        behavior: SnackBarBehavior.floating));
    } else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(res.error ?? 'Erreur lors de la demande.'),
        backgroundColor: AppColors.danger, behavior: SnackBarBehavior.floating));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: AppColors.surface,
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
          const Text('Demander un rendez-vous',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.ink)),
          const SizedBox(height: 16),

          // Médecin sélectionné (ou sélecteur)
          if (_gyneco != null)
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: AppColors.primarySoft, borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.border)),
              child: Row(children: [
                CircleAvatar(radius: 20, backgroundColor: AppColors.primary,
                  child: Text(_gyneco!.initiale,
                    style: const TextStyle(color: Colors.white,
                      fontWeight: FontWeight.w800))),
                const SizedBox(width: 10),
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(_gyneco!.nomComplet,
                    style: const TextStyle(fontWeight: FontWeight.w800,
                      fontSize: 13, color: AppColors.ink)),
                  Text(_gyneco!.specialite,
                    style: const TextStyle(fontSize: 11, color: AppColors.primary)),
                  Text(
                    _gyneco!.tarifAffiche != null
                        ? 'Consultation : ${_gyneco!.tarifAffiche}'
                        : 'Tarif communiqué par le cabinet',
                    style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700,
                      color: AppColors.ink2)),
                ])),
                if (widget.gyneco == null)
                  IconButton(icon: const Icon(Icons.close_rounded, size: 18, color: AppColors.ink2),
                    onPressed: () => setState(() => _gyneco = null)),
              ]),
            )
          else if (widget.gynecologues.isNotEmpty) ...[
            const Text('MÉDECIN', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700,
              color: AppColors.ink2, letterSpacing: 0.8)),
            const SizedBox(height: 6),
            Container(
              decoration: BoxDecoration(color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.border, width: 1.5)),
              padding: const EdgeInsets.symmetric(horizontal: 14),
              child: DropdownButtonHideUnderline(
                child: DropdownButton<Gynecologue>(
                  hint: const Text('Choisir un médecin'),
                  value: _gyneco, isExpanded: true,
                  items: widget.gynecologues.map((g) => DropdownMenuItem(
                    value: g,
                    child: Text('${g.nomComplet} — ${g.specialite}',
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 13)))).toList(),
                  onChanged: (v) => setState(() => _gyneco = v),
                ),
              ),
            ),
          ],
          const SizedBox(height: 14),

          // Date
          const Text('DATE SOUHAITÉE', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700,
            color: AppColors.ink2, letterSpacing: 0.8)),
          const SizedBox(height: 6),
          GestureDetector(
            onTap: _choisirDate,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              decoration: BoxDecoration(color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.border, width: 1.5)),
              child: Row(children: [
                const Icon(Icons.calendar_today_rounded, color: AppColors.primary, size: 18),
                const SizedBox(width: 10),
                Text(_fmt(_date),
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600,
                    color: AppColors.ink)),
                const Spacer(),
                const Icon(Icons.chevron_right_rounded, color: AppColors.ink2),
              ]),
            ),
          ),
          const SizedBox(height: 14),

          // Heure
          const Text('HEURE SOUHAITÉE', style: TextStyle(fontSize: 11,
            fontWeight: FontWeight.w700, color: AppColors.ink2, letterSpacing: 0.8)),
          const SizedBox(height: 8),
          Wrap(spacing: 8, runSpacing: 8,
            children: [
              ..._heures.map((h) => _chipHeure(h, _heure == h, () => setState(() => _heure = h))),
              // Heure personnalisée (hors créneaux prédéfinis)
              if (!_heures.contains(_heure))
                _chipHeure(_heure, true, null),
              // Bouton "Autre heure" → sélecteur libre
              _chipHeure('Autre…', false, _choisirHeureLibre,
                icon: Icons.schedule_rounded),
            ]),
          const SizedBox(height: 14),

          // Motif
          const Text('MOTIF (optionnel)', style: TextStyle(fontSize: 11,
            fontWeight: FontWeight.w700, color: AppColors.ink2, letterSpacing: 0.8)),
          const SizedBox(height: 6),
          TextField(controller: _motifCtrl,
            decoration: const InputDecoration(
              hintText: 'Ex: Suivi grossesse, bilan hormonal…')),
          const SizedBox(height: 20),

          _loading
              ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
              : ElevatedButton(onPressed: _valider,
                  child: const Text('Envoyer la demande')),
        ],
      )),
    );
  }
}

