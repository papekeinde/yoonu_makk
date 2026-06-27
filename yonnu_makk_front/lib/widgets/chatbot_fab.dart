import 'package:flutter/material.dart';
import '../config/theme.dart';
import '../config/routes.dart';

// ─── BOUTON FLOTTANT CHATBOT ──────────────────────────────────────────────────
// Inspiré du ChatbotFab du projet de référence.
// Se masque automatiquement quand on est déjà sur la page chatbot.
class ChatbotFab extends StatelessWidget {
  const ChatbotFab({super.key});

  @override
  Widget build(BuildContext context) {
    // Ne pas afficher si on est déjà sur le chatbot
    final route = ModalRoute.of(context)?.settings.name;
    if (route == Routes.chatbot) return const SizedBox.shrink();

    const accent  = AppColors.primary;

    return FloatingActionButton.extended(
      onPressed: () => Navigator.pushNamed(context, Routes.chatbot),
      backgroundColor: accent,
      foregroundColor: Colors.white,
      elevation: 4,
      icon: const Icon(Icons.smart_toy_outlined),
      label: const Text('Assistant IA',
        style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
      tooltip: 'Ouvrir l\'assistant IA',
    );
  }
}
