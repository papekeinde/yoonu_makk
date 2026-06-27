import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../config/theme.dart';
import '../../config/routes.dart';
import '../../controllers/auth_controller.dart';
import '../../widgets/bottom_nav.dart';

// ─── VUE SPÉCIALISTES ────────────────────────────────────────────────────────
// Inspirée du MapScreen & AppointmentsScreen du projet de référence.
class SpecialistesView extends StatefulWidget {
  const SpecialistesView({super.key});
  @override
  State<SpecialistesView> createState() => _SpecialistesViewState();
}

class _SpecialistesViewState extends State<SpecialistesView> {
  int    _tab      = 0;
  String _recherche = '';
  final _searchCtrl = TextEditingController();
  final _tabs = ['Plus proche', 'Mieux noté', 'Disponible'];

  static const _gynecologues = [
    _Spec(nom: 'Dr. Fatou Sow',     spec: 'Gynécologue-obstétricienne', ville: 'Dakar Plateau', distance: '2,3 km', note: 4.8, dispo: 'Disponible aujourd\'hui', specialite: 'Gynécologue'),
    _Spec(nom: 'Dr. Awa Diop',      spec: 'Gynécologue',                ville: 'Pikine',        distance: '5,1 km', note: 4.6, dispo: 'Disponible demain',       specialite: 'Gynécologue'),
    _Spec(nom: 'Dr. Khady Faye',    spec: 'Gynécologue-obstétricienne', ville: 'Yoff',          distance: '8,4 km', note: 4.9, dispo: 'Sous 3 jours',           specialite: 'Gynécologue'),
    _Spec(nom: 'Dr. Aminata Ba',    spec: 'Endocrinologue',             ville: 'Almadies',      distance: '6,2 km', note: 4.7, dispo: 'Disponible aujourd\'hui', specialite: 'Endocrinologue'),
    _Spec(nom: 'Dr. Mariama Ndiaye',spec: 'Gynécologue',                ville: 'Thiès',         distance: '3,5 km', note: 4.5, dispo: 'Disponible demain',       specialite: 'Gynécologue'),
    _Spec(nom: 'Dr. Rokhaya Fall',  spec: 'Sage-femme',                 ville: 'Parcelles',     distance: '1,8 km', note: 4.9, dispo: 'Disponible aujourd\'hui', specialite: 'Sage-femme'),
  ];

  List<_Spec> get _filtered {
    List<_Spec> liste = List.of(_gynecologues);
    if (_recherche.isNotEmpty) {
      final q = _recherche.toLowerCase();
      liste = liste.where((s) =>
        s.nom.toLowerCase().contains(q) ||
        s.spec.toLowerCase().contains(q) ||
        s.ville.toLowerCase().contains(q)).toList();
    }
    if (_tab == 1) liste.sort((a, b) => b.note.compareTo(a.note));
    if (_tab == 2) {
      liste = liste.where((s) => s.dispo.contains('aujourd\'hui')).toList();
    }
    return liste;
  }

  @override
  void dispose() { _searchCtrl.dispose(); super.dispose(); }

  void _voirDetails(BuildContext context, _Spec spec, Color accent) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (_) => Padding(
        padding: const EdgeInsets.fromLTRB(24, 20, 24, 28),
        child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
          // Poignée
          Center(child: Container(width: 40, height: 4,
            decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(2)))),
          const SizedBox(height: 18),
          // En-tête
          Row(children: [
            _CircleInitiales(initiales: spec.nom.split(' ').last[0], accent: accent),
            const SizedBox(width: 14),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(spec.nom, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: AppColors.ink)),
              Text(spec.spec, style: const TextStyle(fontSize: 13, color: AppColors.ink2)),
            ])),
          ]),
          const SizedBox(height: 18),
          _infoLigne(Icons.place_outlined,         'Localisation', spec.ville),
          _infoLigne(Icons.near_me_outlined,       'Distance',     spec.distance),
          _infoLigne(Icons.star_rounded,           'Note patients','${spec.note} / 5'),
          _infoLigne(Icons.event_available_rounded,'Disponibilité',spec.dispo),
          const SizedBox(height: 22),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: () {
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                  content: Text('Demande de RDV envoyée à ${spec.nom}.'),
                  behavior: SnackBarBehavior.floating,
                  backgroundColor: accent,
                ));
              },
              style: ElevatedButton.styleFrom(backgroundColor: accent),
              icon: const Icon(Icons.calendar_today_rounded, size: 18),
              label: const Text('Prendre rendez-vous'),
            ),
          ),
          const SizedBox(height: 8),
          SizedBox(
            width: double.infinity,
            child: OutlinedButton.icon(
              onPressed: () {
                Navigator.pop(context);
                Navigator.pushNamed(context, Routes.chatbot);
              },
              style: OutlinedButton.styleFrom(side: BorderSide(color: accent), foregroundColor: accent),
              icon: const Icon(Icons.smart_toy_outlined, size: 18),
              label: const Text('Demander à l\'assistant IA'),
            ),
          ),
        ]),
      ),
    );
  }

  Widget _infoLigne(IconData icon, String label, String valeur) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 7),
    child: Row(children: [
      Icon(icon, size: 18, color: AppColors.primary),
      const SizedBox(width: 12),
      Text(label, style: const TextStyle(fontSize: 13, color: AppColors.ink2)),
      const Spacer(),
      Text(valeur, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.ink)),
    ]),
  );

  @override
  Widget build(BuildContext context) {
    final user    = context.watch<AuthController>().user;
    final enceinte = user?.estEnceinte ?? false;
    final accent   = AppColors.primary;
    final accentSoft = AppColors.primarySoft;
    final liste    = _filtered;

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        backgroundColor: accent,
        foregroundColor: Colors.white,
        elevation: 0,
        title: const Text('Spécialistes',
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 16)),
      ),
      body: Column(children: [
        // Barre de recherche
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 14, 16, 8),
          child: TextField(
            controller: _searchCtrl,
            onChanged: (v) => setState(() => _recherche = v),
            decoration: InputDecoration(
              hintText: 'Rechercher un médecin, une spécialité…',
              prefixIcon: const Icon(Icons.search_rounded, color: AppColors.ink2),
              suffixIcon: _recherche.isNotEmpty
                  ? IconButton(
                      icon: const Icon(Icons.close_rounded, color: AppColors.ink2, size: 18),
                      onPressed: () { _searchCtrl.clear(); setState(() => _recherche = ''); })
                  : null,
              filled: true,
              fillColor: AppColors.surface,
              contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.border)),
              enabledBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.border)),
              focusedBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: accent, width: 1.5)),
            ),
          ),
        ),
        // Carte "carte simulée"
        Container(
          height: 160,
          margin: const EdgeInsets.symmetric(horizontal: 16),
          decoration: BoxDecoration(
            color: const Color(0xFFEEF4F0),
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: AppColors.border),
          ),
          child: Stack(children: [
            Center(child: Icon(Icons.map_outlined, size: 64, color: AppColors.ink2.withValues(alpha: .2))),
            Positioned(top: 50, left: 90, child: Icon(Icons.location_on, color: accent, size: 32)),
            Positioned(top: 70, right: 70, child: Icon(Icons.location_on, color: AppColors.primaryDark.withValues(alpha: .5), size: 24)),
            Positioned(bottom: 12, left: 12, child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20),
                border: Border.all(color: AppColors.border)),
              child: Row(mainAxisSize: MainAxisSize.min, children: [
                Container(width: 7, height: 7,
                  decoration: const BoxDecoration(color: AppColors.success, shape: BoxShape.circle)),
                const SizedBox(width: 5),
                const Text('Ma position', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppColors.ink)),
              ]),
            )),
          ]),
        ),
        const SizedBox(height: 12),
        // Onglets de tri
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Container(
            padding: const EdgeInsets.all(4),
            decoration: BoxDecoration(color: accentSoft, borderRadius: BorderRadius.circular(14)),
            child: Row(children: List.generate(_tabs.length, (i) => Expanded(
              child: GestureDetector(
                onTap: () => setState(() => _tab = i),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  padding: const EdgeInsets.symmetric(vertical: 8),
                  decoration: BoxDecoration(
                    color: _tab == i ? accent : Colors.transparent,
                    borderRadius: BorderRadius.circular(11),
                  ),
                  alignment: Alignment.center,
                  child: Text(_tabs[i], style: TextStyle(
                    fontSize: 11, fontWeight: FontWeight.w700,
                    color: _tab == i ? Colors.white : AppColors.ink2)),
                ),
              ),
            ))),
          ),
        ),
        const SizedBox(height: 8),
        // Liste
        Expanded(
          child: liste.isEmpty
              ? const Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  Text('🔍', style: TextStyle(fontSize: 40)),
                  SizedBox(height: 12),
                  Text('Aucun résultat', style: TextStyle(color: AppColors.ink2)),
                ]))
              : ListView.separated(
                  padding: const EdgeInsets.fromLTRB(16, 4, 16, 100),
                  itemCount: liste.length,
                  separatorBuilder: (context, i) => const SizedBox(height: 10),
                  itemBuilder: (_, i) => _SpecCard(
                    spec: liste[i],
                    accent: accent,
                    onVoir: () => _voirDetails(context, liste[i], accent),
                    onRdv: () => ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                      content: Text('Demande envoyée à ${liste[i].nom}.'),
                      behavior: SnackBarBehavior.floating,
                      backgroundColor: accent,
                    )),
                  ),
                ),
        ),
      ]),
      floatingActionButton: FloatingActionButton(
        onPressed: () => Navigator.pushNamed(context, Routes.chatbot),
        backgroundColor: accent,
        foregroundColor: Colors.white,
        tooltip: 'Assistant IA',
        child: const Icon(Icons.smart_toy_outlined),
      ),
      bottomNavigationBar: BottomNav(currentIndex: 4, estEnceinte: enceinte),
    );
  }
}

// ─── CARTE SPÉCIALISTE ────────────────────────────────────────────────────────
class _SpecCard extends StatelessWidget {
  final _Spec      spec;
  final Color      accent;
  final VoidCallback onVoir;
  final VoidCallback onRdv;
  const _SpecCard({required this.spec, required this.accent,
    required this.onVoir, required this.onRdv});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: .04), blurRadius: 8)],
      ),
      child: Row(children: [
        _CircleInitiales(initiales: spec.nom.split(' ').last[0], accent: accent),
        const SizedBox(width: 12),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Expanded(child: Text(spec.nom,
              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.ink))),
            const Icon(Icons.star_rounded, size: 13, color: Color(0xFFFFB300)),
            const SizedBox(width: 2),
            Text('${spec.note}',
              style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.ink)),
          ]),
          Text(spec.spec, style: const TextStyle(fontSize: 12, color: AppColors.ink2)),
          const SizedBox(height: 3),
          Row(children: [
            const Icon(Icons.place_outlined, size: 12, color: AppColors.ink2),
            Text(' ${spec.ville} · ${spec.distance}',
              style: const TextStyle(fontSize: 11, color: AppColors.ink2)),
          ]),
          const SizedBox(height: 3),
          Text(spec.dispo,
            style: TextStyle(
              fontSize: 11, fontWeight: FontWeight.w700,
              color: spec.dispo.contains('aujourd') ? AppColors.success : AppColors.warning)),
        ])),
        const SizedBox(width: 8),
        GestureDetector(
          onTap: onVoir,
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
            decoration: BoxDecoration(color: accent, borderRadius: BorderRadius.circular(12)),
            child: const Text('Voir', style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w700)),
          ),
        ),
      ]),
    );
  }
}

// ─── INITIALES AVATAR ────────────────────────────────────────────────────────
class _CircleInitiales extends StatelessWidget {
  final String initiales;
  final Color  accent;
  const _CircleInitiales({required this.initiales, required this.accent});
  @override
  Widget build(BuildContext context) => Container(
    width: 48, height: 48,
    decoration: BoxDecoration(color: accent.withValues(alpha: .15), shape: BoxShape.circle),
    alignment: Alignment.center,
    child: Text(initiales.toUpperCase(),
      style: TextStyle(color: accent, fontWeight: FontWeight.w800, fontSize: 18)),
  );
}

// ─── MODÈLE SPÉCIALISTE (local) ───────────────────────────────────────────────
class _Spec {
  final String nom;
  final String spec;
  final String ville;
  final String distance;
  final double note;
  final String dispo;
  final String specialite;
  const _Spec({
    required this.nom, required this.spec, required this.ville,
    required this.distance, required this.note, required this.dispo,
    required this.specialite,
  });
}

