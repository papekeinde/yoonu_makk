import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../config/routes.dart';
import '../../widgets/bottom_nav.dart';

// ─── VUE SYMPTÔMES ───────────────────────────────────────────────────────────
class SymptomsView extends StatefulWidget {
  const SymptomsView({super.key});
  @override
  State<SymptomsView> createState() => _SymptomsViewState();
}

class _SymptomsViewState extends State<SymptomsView> {
  String? _filtre; // catégorie sélectionnée

  // Données mock — seront remplacées par l'API
  final List<_SymptomeEntry> _symptomes = [
    _SymptomeEntry(id: 1, nom: 'Bouffée de chaleur', categorie: 'Ménopause',
        intensite: 4, date: DateTime.now().subtract(const Duration(hours: 2)),
        notes: 'Après le déjeuner, durée ~5 min'),
    _SymptomeEntry(id: 2, nom: 'Fatigue intense', categorie: 'Général',
        intensite: 3, date: DateTime.now().subtract(const Duration(hours: 5))),
    _SymptomeEntry(id: 3, nom: 'Insomnie', categorie: 'Sommeil',
        intensite: 2, date: DateTime.now().subtract(const Duration(days: 1)),
        notes: 'Réveil à 3h du matin'),
    _SymptomeEntry(id: 4, nom: 'Douleurs articulaires', categorie: 'Douleurs',
        intensite: 3, date: DateTime.now().subtract(const Duration(days: 1))),
    _SymptomeEntry(id: 5, nom: 'Sautes d\'humeur', categorie: 'Humeur',
        intensite: 2, date: DateTime.now().subtract(const Duration(days: 2))),
    _SymptomeEntry(id: 6, nom: 'Vertiges', categorie: 'Général',
        intensite: 1, date: DateTime.now().subtract(const Duration(days: 2)),
        notes: 'Au lever du lit'),
    _SymptomeEntry(id: 7, nom: 'Bouffée de chaleur', categorie: 'Ménopause',
        intensite: 5, date: DateTime.now().subtract(const Duration(days: 3))),
  ];

  static const _categories = ['Tous', 'Ménopause', 'Douleurs', 'Sommeil', 'Humeur', 'Général'];

  List<_SymptomeEntry> get _filtered => _filtre == null || _filtre == 'Tous'
      ? _symptomes
      : _symptomes.where((s) => s.categorie == _filtre).toList();

  // Grouper par date (aujourd'hui / hier / date)
  Map<String, List<_SymptomeEntry>> get _grouped {
    final map = <String, List<_SymptomeEntry>>{};
    for (final s in _filtered) {
      final label = _dateLabel(s.date);
      map.putIfAbsent(label, () => []).add(s);
    }
    return map;
  }

  String _dateLabel(DateTime d) {
    final now = DateTime.now();
    final diff = DateTime(now.year, now.month, now.day)
        .difference(DateTime(d.year, d.month, d.day)).inDays;
    if (diff == 0) return 'Aujourd\'hui';
    if (diff == 1) return 'Hier';
    return '${d.day.toString().padLeft(2,'0')}/${d.month.toString().padLeft(2,'0')}/${d.year}';
  }

  void _ajouterSymptome() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _AddSymptomeSheet(
        onAjouter: (entry) => setState(() => _symptomes.insert(0, entry)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final grouped = _grouped;
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: const Text('Mes symptômes',
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
        actions: [
          TextButton.icon(
            onPressed: () => Navigator.pushNamed(context, Routes.chatbot),
            icon: const Icon(Icons.smart_toy_rounded, size: 18),
            label: const Text('IA'),
            style: TextButton.styleFrom(foregroundColor: AppColors.primary),
          ),
        ],
      ),
      body: Column(
        children: [
          // Résumé hebdomadaire
          _WeeklySummary(count: _symptomes.length),
          // Filtres par catégorie
          SizedBox(
            height: 46,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 7),
              itemCount: _categories.length,
              separatorBuilder: (context, i) => const SizedBox(width: 8),
              itemBuilder: (_, i) {
                final cat   = _categories[i];
                final actif = (_filtre ?? 'Tous') == cat;
                return GestureDetector(
                  onTap: () => setState(() => _filtre = cat == 'Tous' ? null : cat),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    decoration: BoxDecoration(
                      color: actif ? AppColors.primary : AppColors.surface,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(
                        color: actif ? AppColors.primary : AppColors.border),
                    ),
                    alignment: Alignment.center,
                    child: Text(cat,
                      style: TextStyle(
                        fontSize: 12,
                        color: actif ? Colors.white : AppColors.ink2,
                        fontWeight: actif ? FontWeight.w600 : FontWeight.normal,
                      )),
                  ),
                );
              },
            ),
          ),
          const SizedBox(height: 4),
          // Liste
          Expanded(
            child: grouped.isEmpty
                ? const _EmptyState()
                : ListView(
                    padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
                    children: grouped.entries.map((e) => Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: const EdgeInsets.symmetric(vertical: 10),
                          child: Text(e.key,
                            style: const TextStyle(
                              fontSize: 13, fontWeight: FontWeight.w700,
                              color: AppColors.ink2)),
                        ),
                        ...e.value.map((s) => _SymptomeCard(
                          entry: s,
                          onDelete: () => setState(() => _symptomes.remove(s)),
                        )),
                      ],
                    )).toList(),
                  ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _ajouterSymptome,
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add_rounded),
        label: const Text('Ajouter', style: TextStyle(fontWeight: FontWeight.w600)),
      ),
      bottomNavigationBar: const BottomNav(currentIndex: 1),
    );
  }
}

// ─── RÉSUMÉ SEMAINE ──────────────────────────────────────────────────────────
class _WeeklySummary extends StatelessWidget {
  final int count;
  const _WeeklySummary({required this.count});
  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 12, 16, 4),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [AppColors.primary, AppColors.primaryDark],
          begin: Alignment.topLeft, end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Row(children: [
        const Text('📊', style: TextStyle(fontSize: 32)),
        const SizedBox(width: 14),
        Expanded(child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('CETTE SEMAINE', style: TextStyle(
              color: Colors.white70, fontSize: 10, fontWeight: FontWeight.w800, letterSpacing: 1)),
            const SizedBox(height: 2),
            Text('$count symptômes enregistrés', style: const TextStyle(
              color: Colors.white, fontSize: 18, fontWeight: FontWeight.w800)),
          ],
        )),
        Column(
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            const Text('Intensité moy.', style: TextStyle(color: Colors.white70, fontSize: 10)),
            const SizedBox(height: 2),
            Row(children: List.generate(5, (i) => Icon(
              i < 3 ? Icons.circle : Icons.circle_outlined,
              size: 10, color: Colors.white,
            ))),
          ],
        ),
      ]),
    );
  }
}

// ─── CARTE SYMPTÔME ──────────────────────────────────────────────────────────
class _SymptomeCard extends StatelessWidget {
  final _SymptomeEntry entry;
  final VoidCallback   onDelete;
  const _SymptomeCard({required this.entry, required this.onDelete});

  Color get _catColor {
    switch (entry.categorie) {
      case 'Ménopause': return AppColors.primary;
      case 'Douleurs':  return AppColors.danger;
      case 'Sommeil':   return const Color(0xFF5C6BC0);
      case 'Humeur':    return const Color(0xFFFF9800);
      default:          return AppColors.ink2;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Dismissible(
      key: Key('sym_${entry.id}'),
      direction: DismissDirection.endToStart,
      background: Container(
        alignment: Alignment.centerRight,
        padding: const EdgeInsets.only(right: 20),
        decoration: BoxDecoration(
          color: AppColors.danger.withValues(alpha: 0.12),
          borderRadius: BorderRadius.circular(14),
        ),
        child: const Icon(Icons.delete_outline_rounded, color: AppColors.danger),
      ),
      onDismissed: (_) => onDelete(),
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.border),
          boxShadow: [BoxShadow(
            color: Colors.black.withValues(alpha: 0.04), blurRadius: 8)],
        ),
        child: Row(children: [
          // Intensité visuelle
          Container(
            width: 44, height: 44,
            decoration: BoxDecoration(
              color: _catColor.withValues(alpha: 0.12),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Center(child: Text(_emoji(entry.categorie),
              style: const TextStyle(fontSize: 22))),
          ),
          const SizedBox(width: 12),
          Expanded(child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(entry.nom,
                style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.ink)),
              const SizedBox(height: 3),
              Row(children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  decoration: BoxDecoration(
                    color: _catColor.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(entry.categorie,
                    style: TextStyle(fontSize: 10, color: _catColor, fontWeight: FontWeight.w600)),
                ),
                if (entry.notes != null) ...[
                  const SizedBox(width: 6),
                  const Icon(Icons.notes_rounded, size: 12, color: AppColors.ink2),
                ],
              ]),
              if (entry.notes != null) ...[
                const SizedBox(height: 3),
                Text(entry.notes!,
                  style: const TextStyle(fontSize: 11, color: AppColors.ink2),
                  maxLines: 1, overflow: TextOverflow.ellipsis),
              ],
            ],
          )),
          const SizedBox(width: 10),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              // Dots d'intensité
              Row(children: List.generate(5, (i) => Container(
                width: 6, height: 6,
                margin: const EdgeInsets.only(left: 2),
                decoration: BoxDecoration(
                  color: i < entry.intensite ? _catColor : AppColors.border,
                  shape: BoxShape.circle,
                ),
              ))),
              const SizedBox(height: 4),
              Text(
                '${entry.date.hour.toString().padLeft(2,'0')}:${entry.date.minute.toString().padLeft(2,'0')}',
                style: const TextStyle(fontSize: 11, color: AppColors.ink2),
              ),
            ],
          ),
        ]),
      ),
    );
  }

  String _emoji(String cat) {
    switch (cat) {
      case 'Ménopause': return '🔥';
      case 'Douleurs':  return '💢';
      case 'Sommeil':   return '😴';
      case 'Humeur':    return '😔';
      default:          return '🫀';
    }
  }
}

// ─── ÉTAT VIDE ────────────────────────────────────────────────────────────────
class _EmptyState extends StatelessWidget {
  const _EmptyState();
  @override
  Widget build(BuildContext context) => const Center(
    child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
      Text('🌸', style: TextStyle(fontSize: 48)),
      SizedBox(height: 12),
      Text('Aucun symptôme dans cette catégorie',
        style: TextStyle(color: AppColors.ink2, fontSize: 14)),
    ]),
  );
}

// ─── MODÈLE ENTRY (local) ────────────────────────────────────────────────────
class _SymptomeEntry {
  final int      id;
  final String   nom;
  final String   categorie;
  final int      intensite;
  final DateTime date;
  final String?  notes;
  _SymptomeEntry({
    required this.id, required this.nom, required this.categorie,
    required this.intensite, required this.date, this.notes,
  });
}

// ─── BOTTOM SHEET — AJOUT SYMPTÔME ──────────────────────────────────────────
class _AddSymptomeSheet extends StatefulWidget {
  final ValueChanged<_SymptomeEntry> onAjouter;
  const _AddSymptomeSheet({required this.onAjouter});
  @override State<_AddSymptomeSheet> createState() => _AddSymptomeSheetState();
}

class _AddSymptomeSheetState extends State<_AddSymptomeSheet> {
  final _nomCtrl   = TextEditingController();
  final _notesCtrl = TextEditingController();
  String _categorie = 'Général';
  int    _intensite = 3;

  static const _cats = ['Ménopause', 'Douleurs', 'Sommeil', 'Humeur', 'Général'];

  @override
  void dispose() { _nomCtrl.dispose(); _notesCtrl.dispose(); super.dispose(); }

  void _valider() {
    if (_nomCtrl.text.trim().isEmpty) return;
    widget.onAjouter(_SymptomeEntry(
      id: DateTime.now().millisecondsSinceEpoch,
      nom: _nomCtrl.text.trim(),
      categorie: _categorie,
      intensite: _intensite,
      date: DateTime.now(),
      notes: _notesCtrl.text.trim().isEmpty ? null : _notesCtrl.text.trim(),
    ));
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      padding: EdgeInsets.fromLTRB(20, 16, 20,
          MediaQuery.of(context).viewInsets.bottom + 20),
      child: Column(mainAxisSize: MainAxisSize.min, children: [
        Container(width: 36, height: 4,
          decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(2))),
        const SizedBox(height: 16),
        const Text('Ajouter un symptôme',
          style: TextStyle(fontSize: 17, fontWeight: FontWeight.w700, color: AppColors.ink)),
        const SizedBox(height: 16),
        TextField(
          controller: _nomCtrl,
          decoration: const InputDecoration(hintText: 'Nom du symptôme'),
        ),
        const SizedBox(height: 12),
        // Catégories
        Wrap(spacing: 8, children: _cats.map((c) => ChoiceChip(
          label: Text(c, style: const TextStyle(fontSize: 12)),
          selected: _categorie == c,
          selectedColor: AppColors.primarySoft,
          onSelected: (_) => setState(() => _categorie = c),
        )).toList()),
        const SizedBox(height: 12),
        // Intensité
        Row(children: [
          const Text('Intensité :', style: TextStyle(fontSize: 13, color: AppColors.ink2)),
          const SizedBox(width: 10),
          Expanded(child: Slider(
            value: _intensite.toDouble(),
            min: 1, max: 5, divisions: 4,
            activeColor: AppColors.primary,
            label: '$_intensite / 5',
            onChanged: (v) => setState(() => _intensite = v.round()),
          )),
          Text('$_intensite/5',
            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.primary)),
        ]),
        const SizedBox(height: 8),
        TextField(
          controller: _notesCtrl,
          maxLines: 2,
          decoration: const InputDecoration(hintText: 'Notes (optionnel)'),
        ),
        const SizedBox(height: 16),
        ElevatedButton(onPressed: _valider,
          child: const Text('Enregistrer')),
      ]),
    );
  }
}

