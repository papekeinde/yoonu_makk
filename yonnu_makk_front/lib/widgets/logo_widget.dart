import 'package:flutter/material.dart';
import '../config/theme.dart';

// ─── LOGO YOONU JIGEEN ────────────────────────────────────────────────────────
// Reproduit le logo : cercle rose avec Y + texte YOONU JIGEEN + sous-titre.
class LogoWidget extends StatelessWidget {
  final double size;
  final bool showSubtitle;

  const LogoWidget({super.key, this.size = 120, this.showSubtitle = true});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Cercle avec le "Y"
        Stack(
          clipBehavior: Clip.none,
          children: [
            Container(
              width: size,
              height: size,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(color: AppColors.primary, width: size * 0.055),
                color: AppColors.primarySoft,
              ),
              child: Center(
                child: Text(
                  'Y',
                  style: TextStyle(
                    fontSize: size * 0.52,
                    fontWeight: FontWeight.w800,
                    color: AppColors.primary,
                    height: 1,
                  ),
                ),
              ),
            ),
            // Point décoratif en haut-droite
            Positioned(
              top: size * 0.06,
              right: -size * 0.04,
              child: Container(
                width: size * 0.16,
                height: size * 0.16,
                decoration: const BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.primaryDark,
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 20),
        // Nom de l'application
        Text(
          'YOONU JIGEEN',
          style: TextStyle(
            fontSize: size * 0.27,
            fontWeight: FontWeight.w800,
            color: AppColors.primaryDark,
            letterSpacing: 1.5,
          ),
        ),
        if (showSubtitle) ...[
          const SizedBox(height: 6),
          Text(
            'santé · bien-être · accompagnement',
            style: TextStyle(
              fontSize: size * 0.115,
              color: AppColors.primary,
              fontWeight: FontWeight.w500,
              letterSpacing: 0.5,
            ),
          ),
        ],
      ],
    );
  }
}
