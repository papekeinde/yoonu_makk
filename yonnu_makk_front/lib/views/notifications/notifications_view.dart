import 'package:flutter/material.dart';
import '../../config/theme.dart';

// ─── VUE NOTIFICATIONS ───────────────────────────────────────────────────────
class NotificationsView extends StatefulWidget {
  const NotificationsView({super.key});
  @override
  State<NotificationsView> createState() => _NotificationsViewState();
}

class _NotificationsViewState extends State<NotificationsView> {
  final List<_Notif> _notifs = [
    _Notif(
      id: 1, titre: 'Rappel rendez-vous',
      message: 'Votre rendez-vous avec Dr. Fatou Sow est dans 3 jours (Lundi à 09:30).',
      type: 'rdv', lu: false,
      date: DateTime.now().subtract(const Duration(hours: 1)),
    ),
    _Notif(
      id: 2, titre: 'Nouveau conseil santé',
      message: 'Article : "Les 5 bonnes habitudes pour mieux dormir pendant la ménopause". Découvrez nos conseils validés par des gynécologues.',
      type: 'conseil', lu: false,
      date: DateTime.now().subtract(const Duration(hours: 4)),
    ),
    _Notif(
      id: 3, titre: 'Résultat suivi grossesse',
      message: 'Votre gynécologue a commenté votre dernier suivi. Consultez votre dossier pour plus de détails.',
      type: 'suivi', lu: false,
      date: DateTime.now().subtract(const Duration(hours: 8)),
    ),
    _Notif(
      id: 4, titre: 'Rappel : enregistrez vos symptômes',
      message: 'Vous n\'avez pas enregistré de symptômes depuis 2 jours. Tenez votre journal à jour pour un meilleur suivi.',
      type: 'rappel', lu: true,
      date: DateTime.now().subtract(const Duration(days: 1)),
    ),
    _Notif(
      id: 5, titre: 'Votre demande de RDV confirmée',
      message: 'Dr. Awa Diop a confirmé votre demande de rendez-vous pour le Vendredi 14 Juin à 15:00.',
      type: 'rdv', lu: true,
      date: DateTime.now().subtract(const Duration(days: 2)),
    ),
    _Notif(
      id: 6, titre: 'Conseil du jour',
      message: 'Pensez à boire 1,5 L d\'eau pour atténuer les bouffées de chaleur. L\'hydratation est votre meilleure alliée.',
      type: 'conseil', lu: true,
      date: DateTime.now().subtract(const Duration(days: 3)),
    ),
  ];

  int get _nonLus => _notifs.where((n) => !n.lu).length;

  void _marquerToutLu() => setState(() {
    for (final n in _notifs) { n.lu = true; }
  });

  void _supprimer(int id) => setState(() => _notifs.removeWhere((n) => n.id == id));

  @override
  Widget build(BuildContext context) {
    final nonLus = _nonLus;
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: Row(children: [
          const Text('Notifications',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
          if (nonLus > 0) ...[
            const SizedBox(width: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
              decoration: BoxDecoration(
                color: AppColors.primary, borderRadius: BorderRadius.circular(12)),
              child: Text('$nonLus',
                style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w700)),
            ),
          ],
        ]),
        actions: [
          if (nonLus > 0)
            TextButton(
              onPressed: _marquerToutLu,
              child: const Text('Tout lire',
                style: TextStyle(color: AppColors.primary, fontSize: 12, fontWeight: FontWeight.w600)),
            ),
        ],
      ),
      body: _notifs.isEmpty
          ? const _EmptyNotifs()
          : ListView.builder(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 32),
              itemCount: _notifs.length,
              itemBuilder: (_, i) => Dismissible(
                key: Key('notif_${_notifs[i].id}'),
                direction: DismissDirection.endToStart,
                background: Container(
                  alignment: Alignment.centerRight,
                  padding: const EdgeInsets.only(right: 20),
                  margin: const EdgeInsets.only(bottom: 10),
                  decoration: BoxDecoration(
                    color: AppColors.danger.withValues(alpha: .12),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: const Icon(Icons.delete_outline_rounded, color: AppColors.danger),
                ),
                onDismissed: (_) => _supprimer(_notifs[i].id),
                child: _NotifCard(
                  notif: _notifs[i],
                  onTap: () => setState(() => _notifs[i].lu = true),
                ),
              ),
            ),
    );
  }
}

// ─── CARTE NOTIFICATION ──────────────────────────────────────────────────────
class _NotifCard extends StatelessWidget {
  final _Notif    notif;
  final VoidCallback onTap;
  const _NotifCard({required this.notif, required this.onTap});

  IconData get _icon {
    switch (notif.type) {
      case 'rdv':     return Icons.calendar_today_rounded;
      case 'conseil': return Icons.menu_book_rounded;
      case 'suivi':   return Icons.monitor_heart_rounded;
      case 'rappel':  return Icons.notifications_active_rounded;
      default:        return Icons.notifications_rounded;
    }
  }

  Color get _color {
    switch (notif.type) {
      case 'rdv':     return AppColors.primary;
      case 'conseil': return const Color(0xFF1565C0);
      case 'suivi':   return AppColors.green;
      case 'rappel':  return AppColors.warning;
      default:        return AppColors.ink2;
    }
  }

  String _dateLabel(DateTime d) {
    final diff = DateTime.now().difference(d);
    if (diff.inMinutes < 60) return 'Il y a ${diff.inMinutes} min';
    if (diff.inHours < 24) return 'Il y a ${diff.inHours}h';
    if (diff.inDays == 1) return 'Hier';
    return 'Il y a ${diff.inDays} jours';
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: notif.lu ? AppColors.surface : AppColors.primarySoft.withValues(alpha: .6),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: notif.lu ? AppColors.border : AppColors.primary.withValues(alpha: .25)),
          boxShadow: [BoxShadow(
            color: Colors.black.withValues(alpha: .04), blurRadius: 8)],
        ),
        child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Container(
            width: 44, height: 44,
            decoration: BoxDecoration(
              color: _color.withValues(alpha: .12),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(_icon, color: _color, size: 22),
          ),
          const SizedBox(width: 12),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Row(children: [
              Expanded(child: Text(notif.titre, style: TextStyle(
                fontSize: 13, fontWeight: notif.lu ? FontWeight.w600 : FontWeight.w800,
                color: AppColors.ink))),
              if (!notif.lu)
                Container(width: 8, height: 8,
                  decoration: const BoxDecoration(
                    color: AppColors.primary, shape: BoxShape.circle)),
            ]),
            const SizedBox(height: 4),
            Text(notif.message, style: const TextStyle(
              fontSize: 12, color: AppColors.ink2, height: 1.45),
              maxLines: 2, overflow: TextOverflow.ellipsis),
            const SizedBox(height: 5),
            Text(_dateLabel(notif.date),
              style: const TextStyle(fontSize: 11, color: AppColors.ink2)),
          ])),
        ]),
      ),
    );
  }
}

// ─── ÉTAT VIDE ────────────────────────────────────────────────────────────────
class _EmptyNotifs extends StatelessWidget {
  const _EmptyNotifs();
  @override
  Widget build(BuildContext context) => const Center(
    child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
      Text('🔔', style: TextStyle(fontSize: 52)),
      SizedBox(height: 12),
      Text('Aucune notification pour le moment',
        style: TextStyle(color: AppColors.ink2, fontSize: 14)),
    ]),
  );
}

// ─── MODÈLE NOTIFICATION (local) ─────────────────────────────────────────────
class _Notif {
  final int    id;
  final String titre;
  final String message;
  final String type;
  bool         lu;
  final DateTime date;
  _Notif({required this.id, required this.titre, required this.message,
    required this.type, required this.lu, required this.date});
}

