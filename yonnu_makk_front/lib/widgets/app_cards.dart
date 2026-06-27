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
        padding: const EdgeInsets.all(16),
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
              width: 44, height: 44,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: color.withValues(alpha: 0.12),
              ),
              child: Icon(icon, color: color, size: 22),
            ),
            const SizedBox(width: 12),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.baseline,
                  textBaseline: TextBaseline.alphabetic,
                  children: [
                    Text(value, style: TextStyle(
                      fontSize: 26, fontWeight: FontWeight.w800, color: color,
                    )),
                    const SizedBox(width: 3),
                    Text(unit, style: const TextStyle(
                      fontSize: 12, color: AppColors.ink2,
                    )),
                  ],
                ),
                Text(label, style: const TextStyle(
                  fontSize: 12, color: AppColors.ink2,
                )),
              ],
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
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: color, size: 32),
            const SizedBox(height: 10),
            Text(titre, style: const TextStyle(
              fontSize: 14, fontWeight: FontWeight.w700, color: AppColors.ink,
            )),
            const SizedBox(height: 2),
            Text(sousTitre, style: const TextStyle(
              fontSize: 12, color: AppColors.ink2,
            )),
          ],
        ),
      ),
    );
  }
}
