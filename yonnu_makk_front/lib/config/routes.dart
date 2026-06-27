// ─── ROUTES NOMMÉES ──────────────────────────────────────────────────────────
// Toutes les routes de l'application sont déclarées ici en un seul endroit.
// Pour naviguer : Navigator.pushNamed(context, Routes.login)

import 'package:flutter/material.dart';
import '../views/auth/landing_view.dart';
import '../views/auth/login_view.dart';
import '../views/auth/register_view.dart';
import '../views/home/home_view.dart';
import '../views/symptomes/symptomes_view.dart';
import '../views/chatbot/chatbot_view.dart';
import '../views/rendez_vous/rdv_list_view.dart';
import '../views/grossesse/grossesse_view.dart';
import '../views/conseils/conseils_view.dart';
import '../views/specialistes/specialistes_view.dart';
import '../views/profil/profil_view.dart';
import '../views/notifications/notifications_view.dart';

class Routes {
  Routes._();

  // ── Auth ──────────────────────────────────────────────────────────────────
  static const landing  = '/';
  static const login    = '/connexion';
  static const register = '/inscription';

  // ── Patient ───────────────────────────────────────────────────────────────
  static const home           = '/accueil';
  static const symptomes      = '/symptomes';
  static const chatbot        = '/chatbot';
  static const rendezVous     = '/rendez-vous';
  static const grossesse      = '/grossesse';
  static const mouvements     = '/grossesse/mouvements';
  static const conseils       = '/conseils';
  static const specialistes   = '/specialistes';
  static const profil         = '/profil';
  static const notifications  = '/notifications';

  // ── Table de correspondance route → widget ────────────────────────────────
  static final Map<String, WidgetBuilder> all = {
    landing:        (_) => const LandingView(),
    login:          (_) => const LoginView(),
    register:       (_) => const RegisterView(),
    home:           (_) => const HomeView(),
    symptomes:      (_) => const SymptomsView(),
    chatbot:        (_) => const ChatbotView(),
    rendezVous:     (_) => const RdvListView(),
    grossesse:      (_) => const GrossesseView(),
    mouvements:     (_) => const MouvementsView(), // défini dans grossesse_view.dart
    conseils:       (_) => const ConseilsView(),
    specialistes:   (_) => const SpecialistesView(),
    profil:         (_) => const ProfilView(),
    notifications:  (_) => const NotificationsView(),
  };
}
