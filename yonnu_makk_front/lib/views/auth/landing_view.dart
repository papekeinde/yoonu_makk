import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../config/routes.dart';
import '../../widgets/logo_widget.dart';

// ─── VUE LANDING (PAGE D'ACCUEIL / SPLASH) ───────────────────────────────────
// Première page visible. Présente l'application et propose de se connecter
// ou de créer un compte. Correspond exactement aux screenshots de design.
class LandingView extends StatefulWidget {
  const LandingView({super.key});

  @override
  State<LandingView> createState() => _LandingViewState();
}

class _LandingViewState extends State<LandingView> {
  String _langue = 'Français';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end:   Alignment.bottomCenter,
            colors: [Color(0xFFFFFBFC), Color(0xFFFDECF3)],
          ),
        ),
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24),
            child: Column(
              children: [
                const Spacer(flex: 2),

                // Logo
                const LogoWidget(size: 130),

                const SizedBox(height: 28),

                // Badges "Ménopause" et "Grossesse"
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    _BadgeProfil(
                      icon: Icons.favorite,
                      label: 'Ménopause',
                      bgColor: AppColors.primarySoft,
                      textColor: AppColors.primary,
                    ),
                    const SizedBox(width: 12),
                    _BadgeProfil(
                      icon: Icons.visibility,
                      label: 'Grossesse',
                      bgColor: const Color(0xFFE8F5E9),
                      textColor: AppColors.green,
                    ),
                  ],
                ),

                const SizedBox(height: 24),

                // Accroche
                const Text(
                  'Suivez votre santé à chaque étape\nde votre vie de femme',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 16,
                    color: AppColors.ink,
                    fontWeight: FontWeight.w500,
                    height: 1.5,
                  ),
                ),

                const Spacer(flex: 3),

                // Bouton Connexion
                ElevatedButton(
                  onPressed: () => Navigator.pushNamed(context, Routes.login),
                  child: const Text('Connexion'),
                ),

                const SizedBox(height: 12),

                // Bouton Créer un compte
                OutlinedButton(
                  onPressed: () => Navigator.pushNamed(context, Routes.register),
                  child: const Text('Créer un compte'),
                ),

                const SizedBox(height: 24),

                // Sélecteur de langue
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: ['Français', 'Wolof', 'Pulaar'].map((l) {
                    final selected = l == _langue;
                    return GestureDetector(
                      onTap: () => setState(() => _langue = l),
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 10),
                        child: Text(
                          l,
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: selected ? FontWeight.w700 : FontWeight.w400,
                            color: selected ? AppColors.primary : AppColors.ink2,
                          ),
                        ),
                      ),
                    );
                  }).toList(),
                ),

                const SizedBox(height: 20),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

// ── Widget interne : badge de profil ─────────────────────────────────────────
class _BadgeProfil extends StatelessWidget {
  final IconData icon;
  final String   label;
  final Color    bgColor;
  final Color    textColor;

  const _BadgeProfil({
    required this.icon,
    required this.label,
    required this.bgColor,
    required this.textColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(50),
        border: Border.all(color: textColor.withValues(alpha: 0.25)),
      ),
      child: Row(
        children: [
          Icon(icon, color: textColor, size: 16),
          const SizedBox(width: 6),
          Text(label, style: TextStyle(
            color: textColor, fontWeight: FontWeight.w600, fontSize: 14,
          )),
        ],
      ),
    );
  }
}
