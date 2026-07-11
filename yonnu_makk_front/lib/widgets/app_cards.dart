import 'package:flutter/material.dart';
import '../config/theme.dart';

// ─── CARTE STATISTIQUE (ex: "12 mouvements") ─────────────────────────────────
class StatCard extends StatelessWidget {
  final String value;
  final String unit;
  final String label;
  final IconData icon;
  final Color? accentColor;
  final VoidCallback? onTap;

  const StatCard({
    super.key,
    required this.value,
    required this.unit,
    required this.label,
    required this.icon,
    this.accentColor,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final color = accentColor ?? AppColors.primary;
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(12), // Un peu moins de padding pour les petits écrans
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border),
          boxShadow: [
            BoxShadow(
              color: AppColors.primary.withValues(alpha: 0.06),
              blurRadius: 12, offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              width: 40, height: 40,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: color.withValues(alpha: 0.12),
              ),
              child: Icon(icon, color: color, size: 20),
            ),
            const SizedBox(width: 10),
            Expanded( // Permet au texte de prendre la place restante sans déborder
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  FittedBox( // Adapte la taille du texte si trop long
                    fit: BoxFit.scaleDown,
                    alignment: Alignment.centerLeft,
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.baseline,
                      textBaseline: TextBaseline.alphabetic,
                      children: [
                        Text(value, style: TextStyle(
                          fontSize: 22, fontWeight: FontWeight.w800, color: color,
                        )),
                        const SizedBox(width: 3),
                        Text(unit, style: const TextStyle(
                          fontSize: 11, color: AppColors.ink2,
                        )),
                      ],
                    ),
                  ),
                  Text(label, 
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 11, color: AppColors.ink2, height: 1.1)),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ─── CARTE ACCÈS RAPIDE ───────────────────────────────────────────────────────
class QuickAccessCard extends StatelessWidget {
  final IconData   icon;
  final String     titre;
  final String     sousTitre;
  final Color?     iconColor;
  final VoidCallback? onTap;

  const QuickAccessCard({
    super.key,
    required this.icon,
    required this.titre,
    required this.sousTitre,
    this.iconColor,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final color = iconColor ?? AppColors.primary;
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center, // Centré verticalement
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: color, size: 28),
            const SizedBox(height: 8),
            Text(titre, 
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.ink)),
            if (sousTitre.isNotEmpty) ...[
              const SizedBox(height: 2),
              Text(sousTitre, 
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(fontSize: 11, color: AppColors.ink2)),
            ],
          ],
        ),
      ),
    );
  }
}
