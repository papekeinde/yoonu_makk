import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../config/theme.dart';
import '../../config/routes.dart';
import '../../controllers/auth_controller.dart';
import '../../widgets/bottom_nav.dart';
import '../../widgets/app_cards.dart';
import '../../widgets/chatbot_fab.dart';

// ─── VUE ACCUEIL ─────────────────────────────────────────────────────────────
// Dispatch selon le profil :
//   • grossesse  → HomeGrossesseContent (vert + suivi bébé)
//   • ménopause  → HomeMenopauseContent (rose + symptômes)
//   • homme      → HomeDiscoveryContent (neutre)
//   • admin      → Vue spécifique Admin
class HomeView extends StatelessWidget {
  const HomeView({super.key});

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthController>().user;

    if (user == null) {
      // Pas connecté → retour landing
      WidgetsBinding.instance.addPostFrameCallback((_) =>
          Navigator.pushReplacementNamed(context, Routes.landing));
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    // ── Dispatching ──────────────────────────────────────────────────────────
    final Widget content;
    bool showFab = true;
    bool estEnceinte = false;

    if (user.role == 'admin') {
      content = _HomeAdmin(prenomUser: user.prenom);
      showFab = false;
    } else if (user.role == 'gynecologue') {
      content = _HomeGynecologue(prenomUser: user.prenom);
      showFab = false;
    } else if (user.genre == 'homme') {
      content = _HomeDiscovery(prenomUser: user.prenom);
    } else if (user.typeProfil == 'grossesse') {
      content = _HomeGrossesse(prenomUser: user.prenom);
      estEnceinte = true;
    } else {
      content = _HomeMenopause(prenomUser: user.prenom);
    }

    return Scaffold(
      body: content,
      floatingActionButton: showFab ? const ChatbotFab() : null,
      bottomNavigationBar: user.role == 'admin' 
          ? null 
          : BottomNav(
              currentIndex: 0,
              estEnceinte:  estEnceinte,
              estHomme: user.genre == 'homme',
            ),
    );
  }
}

// ─── ACCUEIL — profil GYNÉCOLOGUE ───────────────────────────────────────────
class _HomeGynecologue extends StatelessWidget {
  final String prenomUser;
  const _HomeGynecologue({required this.prenomUser});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Espace professionnel'),
        actions: [
          IconButton(
            onPressed: () => context.read<AuthController>().deconnecter().then((_) {
              if (context.mounted) Navigator.pushReplacementNamed(context, Routes.landing);
            }),
            icon: const Icon(Icons.logout_rounded),
          ),
        ],
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.medical_services_outlined, size: 56, color: AppColors.primary),
              const SizedBox(height: 16),
              Text('Bienvenue Dr. $prenomUser', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800)),
              const SizedBox(height: 8),
              const Text('Votre espace professionnel sera bientôt disponible dans l’application mobile.', textAlign: TextAlign.center),
            ],
          ),
        ),
      ),
    );
  }
}

// ─── ACCUEIL — profil GROSSESSE ───────────────────────────────────────────────
class _HomeGrossesse extends StatelessWidget {
  final String prenomUser;
  const _HomeGrossesse({required this.prenomUser});

  static const _accent = AppColors.primary;

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // En-tête
            Row(
              children: [
                CircleAvatar(
                  radius: 24,
                  backgroundColor: _accent.withValues(alpha: 0.2),
                  child: Text(prenomUser[0].toUpperCase(),
                    style: const TextStyle(
                      color: _accent, fontWeight: FontWeight.w800, fontSize: 18)),
                ),
                const SizedBox(width: 12),
                Expanded(child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Bonjour $prenomUser 🌿',
                      style: const TextStyle(
                        fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.ink)),
                    const Text('Votre espace maternité',
                      style: TextStyle(fontSize: 13, color: AppColors.ink2)),
                  ],
                )),
                IconButton(
                  onPressed: () => Navigator.pushNamed(context, Routes.notifications),
                  icon: const Icon(Icons.notifications_rounded, color: AppColors.ink),
                ),
                IconButton(
                  onPressed: () => context.read<AuthController>().deconnecter().then((_) {
                    if (context.mounted) Navigator.pushReplacementNamed(context, Routes.landing);
                  }),
                  icon: const Icon(Icons.logout_rounded, color: AppColors.ink2),
                ),
              ],
            ),

            const SizedBox(height: 20),

            // Bannière tableau de bord grossesse
            GestureDetector(
              onTap: () => Navigator.pushNamed(context, Routes.grossesse),
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: _accent,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Row(
                  children: [
                    const Text('🤰', style: TextStyle(fontSize: 36)),
                    const SizedBox(width: 14),
                    Expanded(child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('MON SUIVI GROSSESSE',
                          style: TextStyle(color: Colors.white70, fontSize: 11,
                            fontWeight: FontWeight.w700, letterSpacing: 1)),
                        const SizedBox(height: 4),
                        const Text('Tableau de bord',
                          style: TextStyle(color: Colors.white, fontSize: 20,
                            fontWeight: FontWeight.w800)),
                        const SizedBox(height: 4),
                        const Text('Suivez votre progression semaine par semaine',
                          style: TextStyle(color: Colors.white70, fontSize: 12)),
                      ],
                    )),
                    const Icon(Icons.arrow_forward_ios_rounded, color: Colors.white, size: 18),
                  ],
                ),
              ),
            ),

            const SizedBox(height: 16),

            // Statistiques rapides
            Row(children: [
              Expanded(child: StatCard(
                value: '12', unit: '/10', label: 'Mouvements\naujourd\'hui',
                icon: Icons.child_care_rounded, accentColor: _accent,
                onTap: () => Navigator.pushNamed(context, Routes.mouvements),
              )),
              const SizedBox(width: 12),
              Expanded(child: StatCard(
                value: '7', unit: 'j', label: 'Prochain\nrendez-vous',
                icon: Icons.calendar_month_rounded, accentColor: AppColors.primary,
                onTap: () => Navigator.pushNamed(context, Routes.rendezVous),
              )),
            ]),

            const SizedBox(height: 24),

            const Text('Accès rapides',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: _accent)),

            const SizedBox(height: 12),

            GridView.count(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              crossAxisCount: 2,
              crossAxisSpacing: 12,
              mainAxisSpacing: 12,
              childAspectRatio: 1.2,
              children: [
                QuickAccessCard(
                  icon: Icons.pregnant_woman, titre: 'Ma grossesse',
                  sousTitre: 'Tableau de bord', iconColor: _accent,
                  onTap: () => Navigator.pushNamed(context, Routes.grossesse),
                ),
                QuickAccessCard(
                  icon: Icons.child_care_rounded, titre: 'Mouvements bébé',
                  sousTitre: 'Enregistrer les kicks', iconColor: _accent,
                  onTap: () => Navigator.pushNamed(context, Routes.mouvements),
                ),
                QuickAccessCard(
                  icon: Icons.assignment_rounded, titre: 'Mes suivis',
                  sousTitre: 'Historique médical', iconColor: AppColors.primary,
                  onTap: () => Navigator.pushNamed(context, Routes.grossesse),
                ),
                QuickAccessCard(
                  icon: Icons.smart_toy_rounded, titre: 'Chatbot IA',
                  sousTitre: 'Questions & conseils', iconColor: AppColors.primary,
                  onTap: () => Navigator.pushNamed(context, Routes.chatbot),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

// ─── ACCUEIL — profil MÉNOPAUSE ───────────────────────────────────────────────
class _HomeMenopause extends StatelessWidget {
  final String prenomUser;
  const _HomeMenopause({required this.prenomUser});

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // En-tête
            Row(
              children: [
                CircleAvatar(
                  radius: 24,
                  backgroundColor: AppColors.primarySoft,
                  child: Text(prenomUser[0].toUpperCase(),
                    style: const TextStyle(
                      color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 18)),

                ),
                const SizedBox(width: 12),
                Expanded(child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Bonjour $prenomUser 👋',
                      style: const TextStyle(
                        fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.ink)),
                    const Text('Comment vous sentez-vous\naujourd\'hui ?',
                      style: TextStyle(fontSize: 13, color: AppColors.ink2)),
                  ],
                )),
                IconButton(
                  onPressed: () => Navigator.pushNamed(context, Routes.notifications),
                  icon: const Icon(Icons.notifications_rounded, color: AppColors.ink),
                ),
                IconButton(
                  onPressed: () => context.read<AuthController>().deconnecter().then((_) {
                    if (context.mounted) Navigator.pushReplacementNamed(context, Routes.landing);
                  }),
                  icon: const Icon(Icons.logout_rounded, color: AppColors.ink2),
                ),
              ],
            ),

            const SizedBox(height: 20),

            // Conseil du jour
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppColors.primarySoft,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.border),
              ),
              child: Row(
                children: [
                  Container(
                    width: 44, height: 44,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: AppColors.primary.withValues(alpha: 0.12),
                    ),
                    child: const Center(child: Text('🌸', style: TextStyle(fontSize: 22))),
                  ),
                  const SizedBox(width: 12),
                  Expanded(child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('CONSEIL DU JOUR',
                        style: TextStyle(
                          fontSize: 10, fontWeight: FontWeight.w800,
                          color: AppColors.primary, letterSpacing: 1)),
                      const SizedBox(height: 4),
                      const Text(
                        'Buvez 1,5 L d\'eau pour atténuer les bouffées de chaleur.',
                        style: TextStyle(fontSize: 13, color: AppColors.ink),
                      ),
                    ],
                  )),
                ],
              ),
            ),

            const SizedBox(height: 16),

            // Statistiques
            Row(children: [
              Expanded(child: StatCard(
                value: '7', unit: '', label: 'Symptômes\ncette semaine',
                icon: Icons.favorite_rounded, accentColor: AppColors.primary,
                onTap: () => Navigator.pushNamed(context, Routes.symptomes),
              )),
              const SizedBox(width: 12),
              Expanded(child: StatCard(
                value: '2', unit: '', label: 'Rendez-vous\nà venir',
                icon: Icons.calendar_month_rounded, accentColor: AppColors.primaryDark,
                onTap: () => Navigator.pushNamed(context, Routes.rendezVous),
              )),
            ]),

            const SizedBox(height: 24),

            const Text('Actions rapides',
              style: TextStyle(
                fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.primaryDark)),

            const SizedBox(height: 12),

            GridView.count(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              crossAxisCount: 2,
              crossAxisSpacing: 12,
              mainAxisSpacing: 12,
              childAspectRatio: 1.2,
              children: [
                QuickAccessCard(
                  icon: Icons.favorite_rounded, titre: 'Symptômes',
                  sousTitre: '', iconColor: AppColors.primary,
                  onTap: () => Navigator.pushNamed(context, Routes.symptomes),
                ),
                QuickAccessCard(
                  icon: Icons.smart_toy_rounded, titre: 'Chatbot IA',
                  sousTitre: '', iconColor: AppColors.primaryDark,
                  onTap: () => Navigator.pushNamed(context, Routes.chatbot),
                ),
                QuickAccessCard(
                  icon: Icons.calendar_month_rounded, titre: 'Rendez-vous',
                  sousTitre: '', iconColor: AppColors.primary,
                  onTap: () => Navigator.pushNamed(context, Routes.rendezVous),
                ),
                QuickAccessCard(
                  icon: Icons.menu_book_rounded, titre: 'Éducation',
                  sousTitre: '', iconColor: AppColors.primaryDark,
                  onTap: () => Navigator.pushNamed(context, Routes.conseils),
                ),
                QuickAccessCard(
                  icon: Icons.people_rounded, titre: 'Spécialistes',
                  sousTitre: '', iconColor: AppColors.primary,
                  onTap: () => Navigator.pushNamed(context, Routes.specialistes),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

// ─── ACCUEIL — profil DÉCOUVERTE (Hommes) ──────────────────────────────────
class _HomeDiscovery extends StatelessWidget {
  final String prenomUser;
  const _HomeDiscovery({required this.prenomUser});

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // En-tête
            Row(
              children: [
                CircleAvatar(
                  radius: 24,
                  backgroundColor: AppColors.primarySoft,
                  child: Text(prenomUser[0].toUpperCase(),
                    style: const TextStyle(
                      color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 18)),
                ),
                const SizedBox(width: 12),
                Expanded(child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Bonjour $prenomUser 👋',
                      style: const TextStyle(
                        fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.ink)),
                    const Text('Espace d\'accompagnement',
                      style: TextStyle(fontSize: 13, color: AppColors.ink2)),
                  ],
                )),
                IconButton(
                  onPressed: () => context.read<AuthController>().deconnecter().then((_) {
                    if (context.mounted) Navigator.pushReplacementNamed(context, Routes.landing);
                  }),
                  icon: const Icon(Icons.logout_rounded, color: AppColors.ink2),
                ),
              ],
            ),

            const SizedBox(height: 24),

            // Message de bienvenue spécifique
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                color: AppColors.primarySoft, borderRadius: BorderRadius.circular(18),
                border: Border.all(color: AppColors.primary.withValues(alpha: .3))),
              child: const Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text('🤝', style: TextStyle(fontSize: 34)),
                SizedBox(height: 10),
                Text('Bienvenue dans votre espace',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: AppColors.primary)),
                SizedBox(height: 8),
                Text(
                  'Informez-vous sur la santé des femmes pour mieux accompagner votre épouse, votre mère ou vos proches.',
                  style: TextStyle(fontSize: 13, color: AppColors.ink, height: 1.6)),
              ]),
            ),

            const SizedBox(height: 24),

            const Text('Explorer',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.primaryDark)),

            const SizedBox(height: 12),

            GridView.count(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              crossAxisCount: 2,
              crossAxisSpacing: 12,
              mainAxisSpacing: 12,
              childAspectRatio: 1.2,
              children: [
                QuickAccessCard(
                  icon: Icons.menu_book_rounded, titre: 'Conseils',
                  sousTitre: 'Contenus éducatifs', iconColor: AppColors.primary,
                  onTap: () => Navigator.pushNamed(context, Routes.conseils),
                ),
                QuickAccessCard(
                  icon: Icons.smart_toy_rounded, titre: 'Chatbot IA',
                  sousTitre: 'Posez vos questions', iconColor: AppColors.primaryDark,
                  onTap: () => Navigator.pushNamed(context, Routes.chatbot),
                ),
                QuickAccessCard(
                  icon: Icons.people_rounded, titre: 'Spécialistes',
                  sousTitre: 'Trouver un médecin', iconColor: AppColors.primary,
                  onTap: () => Navigator.pushNamed(context, Routes.specialistes),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

// ─── ACCUEIL — profil ADMIN ──────────────────────────────────────────────────
class _HomeAdmin extends StatelessWidget {
  final String prenomUser;
  const _HomeAdmin({required this.prenomUser});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Tableau de bord Admin'),
        actions: [
          IconButton(
            onPressed: () => context.read<AuthController>().deconnecter().then((_) {
              if (context.mounted) Navigator.pushReplacementNamed(context, Routes.landing);
            }),
            icon: const Icon(Icons.logout_rounded),
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Bienvenue, $prenomUser',
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
            const SizedBox(height: 20),
            const Card(
              child: ListTile(
                leading: Icon(Icons.people),
                title: Text('Gestion des utilisateurs'),
                subtitle: Text('Valider les comptes gynécologues'),
                trailing: Icon(Icons.chevron_right),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
