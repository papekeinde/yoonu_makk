import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../config/routes.dart';
import '../controllers/auth_controller.dart';

// ─── BARRE DE NAVIGATION BASSE ────────────────────────────────────────────────
// Adaptée selon le profil : grossesse, ménopause ou homme.
// L'onglet actif est déduit de la route courante (plus de conflit d'index).
//
//   Ménopause : Accueil · Symptômes · RDV · Conseils · Profil
//   Grossesse : Accueil · Grossesse · RDV · Conseils · Profil
//   Homme     : Accueil · IA · Conseils · Médecins · Profil

class BottomNav extends StatelessWidget {
  final int     currentIndex; // repli si la route n'est pas dans la liste
  final bool?   estEnceinte;  // conservés pour compatibilité (ignorés)
  final bool?   estHomme;

  const BottomNav({
    super.key,
    required this.currentIndex,
    this.estEnceinte,
    this.estHomme,
  });

  // Routes de chaque onglet, dans l'ordre d'affichage.
  static List<String> _routesPour({required bool isHomme, required bool isEnceinte}) {
    if (isHomme) {
      return [Routes.home, Routes.chatbot, Routes.conseils, Routes.specialistes, Routes.profil];
    } else if (isEnceinte) {
      return [Routes.home, Routes.grossesse, Routes.rendezVous, Routes.conseils, Routes.profil];
    }
    return [Routes.home, Routes.symptomes, Routes.rendezVous, Routes.conseils, Routes.profil];
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthController>().user;

    final isHomme    = user?.genre == 'homme';
    final isEnceinte = user?.typeProfil == 'grossesse';

    final List<BottomNavigationBarItem> items;
    if (isHomme) {
      items = _itemsHomme;
    } else if (isEnceinte) {
      items = _itemsGrossesse;
    } else {
      items = _itemsMenopause;
    }

    // Index actif déduit de la route courante (avec repli sur currentIndex).
    final routes    = _routesPour(isHomme: isHomme, isEnceinte: isEnceinte);
    var   routeName = ModalRoute.of(context)?.settings.name;
    // Sous-pages rattachées à un onglet parent
    if (routeName == Routes.mouvements) routeName = Routes.grossesse;
    var index = routeName == null ? -1 : routes.indexOf(routeName);
    if (index < 0) index = currentIndex.clamp(0, routes.length - 1);

    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withValues(alpha: 0.08),
            blurRadius: 20, offset: const Offset(0, -4),
          ),
        ],
      ),
      child: BottomNavigationBar(
        currentIndex:            index,
        type:                    BottomNavigationBarType.fixed,
        selectedItemColor:       AppColors.primary,
        unselectedItemColor:     AppColors.ink2,
        backgroundColor:         AppColors.surface,
        selectedLabelStyle:      const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
        unselectedLabelStyle:    const TextStyle(fontSize: 11),
        elevation:               0,
        onTap: (i) => _navigue(context, i, routes),
        items: items,
      ),
    );
  }

  static void _navigue(BuildContext context, int index, List<String> routes) {
    if (index < 0 || index >= routes.length) return;
    final cible = routes[index];
    if (ModalRoute.of(context)?.settings.name == cible) return;
    Navigator.pushReplacementNamed(context, cible);
  }
}

const _itemsGrossesse = [
  BottomNavigationBarItem(icon: Icon(Icons.home_rounded),           label: 'Accueil'),
  BottomNavigationBarItem(icon: Icon(Icons.pregnant_woman),         label: 'Grossesse'),
  BottomNavigationBarItem(icon: Icon(Icons.calendar_month_rounded), label: 'RDV'),
  BottomNavigationBarItem(icon: Icon(Icons.menu_book_rounded),      label: 'Conseils'),
  BottomNavigationBarItem(icon: Icon(Icons.person_rounded),         label: 'Profil'),
];

const _itemsMenopause = [
  BottomNavigationBarItem(icon: Icon(Icons.home_rounded),           label: 'Accueil'),
  BottomNavigationBarItem(icon: Icon(Icons.favorite_rounded),       label: 'Symptômes'),
  BottomNavigationBarItem(icon: Icon(Icons.calendar_month_rounded), label: 'RDV'),
  BottomNavigationBarItem(icon: Icon(Icons.menu_book_rounded),      label: 'Conseils'),
  BottomNavigationBarItem(icon: Icon(Icons.person_rounded),         label: 'Profil'),
];

const _itemsHomme = [
  BottomNavigationBarItem(icon: Icon(Icons.home_rounded),           label: 'Accueil'),
  BottomNavigationBarItem(icon: Icon(Icons.smart_toy_rounded),      label: 'IA'),
  BottomNavigationBarItem(icon: Icon(Icons.menu_book_rounded),      label: 'Conseils'),
  BottomNavigationBarItem(icon: Icon(Icons.people_rounded),         label: 'Médecins'),
  BottomNavigationBarItem(icon: Icon(Icons.person_rounded),         label: 'Profil'),
];
