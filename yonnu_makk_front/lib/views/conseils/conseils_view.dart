import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../config/theme.dart';
import '../../config/routes.dart';
import '../../controllers/auth_controller.dart';
import '../../widgets/bottom_nav.dart';

// ─── VUE CONSEILS & ÉDUCATION ────────────────────────────────────────────────
// Inspirée du EducationScreen du projet de référence.
// Adapte les onglets et le contenu selon grossesse / ménopause.
class ConseilsView extends StatefulWidget {
  const ConseilsView({super.key});
  @override
  State<ConseilsView> createState() => _ConseilsViewState();
}

class _ConseilsViewState extends State<ConseilsView> {
  int _tab = 0;

  @override
  Widget build(BuildContext context) {
    final user      = context.watch<AuthController>().user;
    final enceinte  = user?.estEnceinte ?? false;
    final accent    = AppColors.primary;
    final accentSoft = AppColors.primarySoft;
    final tabs      = enceinte
        ? ['Prénatal', 'Bien-être', 'Alimentation']
        : ['Ménopause', 'Bien-être', 'Nutrition'];

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        backgroundColor: accent,
        foregroundColor: Colors.white,
        elevation: 0,
        title: Text(enceinte ? 'Espace Maternité' : 'Conseils Santé',
          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 16)),
        actions: [
          Container(
            margin: const EdgeInsets.only(right: 14, top: 10, bottom: 10),
            padding: const EdgeInsets.symmetric(horizontal: 10),
            decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(20)),
            alignment: Alignment.center,
            child: const Text('Validé gynécologues',
              style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w600)),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(18),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          // Bannière
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: const [AppColors.primary, AppColors.primaryDark],
                begin: Alignment.topLeft, end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Row(children: [
              Text(enceinte ? '👩‍⚕️' : '🌸', style: const TextStyle(fontSize: 40)),
              const SizedBox(width: 14),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(enceinte
                    ? 'Conseils adaptés à votre grossesse'
                    : 'Comprendre et gérer la ménopause',
                  style: const TextStyle(
                    color: Colors.white, fontSize: 14, fontWeight: FontWeight.w800, height: 1.3)),
                const SizedBox(height: 4),
                const Text('Sources : OMS · Wikipedia · SYNGOB Sénégal',
                  style: TextStyle(color: Colors.white70, fontSize: 10)),
              ])),
            ]),
          ),
          const SizedBox(height: 14),

          // Onglets
          Container(
            padding: const EdgeInsets.all(4),
            decoration: BoxDecoration(
              color: accentSoft, borderRadius: BorderRadius.circular(14)),
            child: Row(children: List.generate(tabs.length, (i) => Expanded(
              child: GestureDetector(
                onTap: () => setState(() => _tab = i),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  padding: const EdgeInsets.symmetric(vertical: 9),
                  decoration: BoxDecoration(
                    color: _tab == i ? accent : Colors.transparent,
                    borderRadius: BorderRadius.circular(11),
                  ),
                  alignment: Alignment.center,
                  child: Text(tabs[i], style: TextStyle(
                    fontSize: 12, fontWeight: FontWeight.w700,
                    color: _tab == i ? Colors.white : AppColors.ink2)),
                ),
              ),
            ))),
          ),
          const SizedBox(height: 16),

          // Carte à retenir
          _FeaturedCard(tab: _tab, enceinte: enceinte, accent: accent),
          const SizedBox(height: 14),

          // Contenu par onglet
          ...(enceinte
              ? _contenuGrossesse(_tab, accent)
              : _contenuMenopause(_tab, accent)),
        ]),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => Navigator.pushNamed(context, Routes.chatbot),
        backgroundColor: accent,
        foregroundColor: Colors.white,
        tooltip: 'Ouvrir l\'assistant IA',
        child: const Icon(Icons.smart_toy_outlined),
      ),
      bottomNavigationBar: BottomNav(
        currentIndex: enceinte ? 3 : 3,
        estEnceinte: enceinte,
      ),
    );
  }

  // ── Contenu GROSSESSE ──────────────────────────────────────────────────────
  List<Widget> _contenuGrossesse(int tab, Color accent) {
    if (tab == 0) { return [
      _sectionTitle('📅 Calendrier des consultations'),
      _factCard(Icons.calendar_month, accent, 'Avant 8 SA — CPN1',
          'Déclaration grossesse. Groupe sanguin, NFS, glycémie, sérologies. Prescription acide folique 400 µg + vitamine D.', '8 SA'),
      _factCard(Icons.calendar_month, accent, '11–14 SA — Écho T1',
          'Dépistage trisomie 21 (clarté nucale). Mesure LCC. Vérification activité cardiaque.', '12 SA'),
      _factCard(Icons.calendar_month, accent, '20–24 SA — Écho T2',
          'Examen complet des organes fœtaux. Localisation placenta. Les mouvements commencent ~20 SA.', '22 SA'),
      _factCard(Icons.calendar_month, accent, '32–34 SA — Écho T3',
          'Vérification croissance fœtale, présentation (céphalique/siège), liquide amniotique.', '32 SA'),
      const SizedBox(height: 16),
      _sectionTitle('⚠️ Signes d\'alarme — consultez immédiatement'),
      _alertCard('Tension ≥ 140/90 mmHg avec maux de tête',
          'Peut indiquer une pré-éclampsie. Urgence absolue. Consultez immédiatement.'),
      _alertCard('Moins de 10 mouvements bébé en 2h (après 28 SA)',
          'Test des 10 coups : si insuffisant, consultez votre gynécologue sans délai.'),
      _alertCard('Saignements ou douleur intense',
          'À tout stade de la grossesse, consultez en urgence.'),
    ]; }

    if (tab == 1) { return [
      _sectionTitle('🧘 Activité physique recommandée'),
      _articleCard('🤸', accent, 'Yoga prénatal doux — 20 min',
          '7 postures sécurisées par trimestre. Réduit les douleurs lombaires de 50 %. À partir de 12 SA.', '20 min'),
      _articleCard('🚶', accent, 'Marche quotidienne — 30 min',
          'Réduit les œdèmes et les bouffées de chaleur, prépare le périnée. Idéale le matin.', '30 min'),
      _sectionTitle('😴 Sommeil & relaxation'),
      _articleCard('🌙', accent, 'Position de sommeil idéale',
          'Dormez sur le côté gauche après 20 SA. Diminue la pression sur la veine cave inférieure.', '5 min'),
      _articleCard('🧘', accent, 'Respiration 4-7-8',
          'Inspirez 4s, retenez 7s, expirez 8s. Réduit le stress et aide l\'endormissement.', '3 min'),
    ]; }

    return [
      _sectionTitle('🥗 Besoins nutritionnels grossesse'),
      _infoBox(Icons.local_hospital, AppColors.success, 'Apports recommandés OMS',
          'Fer : 27 mg/j · Calcium : 1 000 mg/j · Acide folique : 400–800 µg/j · Protéines : +25 g/j · Eau : 2,5 L/j'),
      const SizedBox(height: 12),
      _articleCard('🥩', accent, 'Sources de fer', 'Viande rouge, légumineuses (lentilles, haricots), épinards, dattes. Associez vitamine C pour améliorer l\'absorption.', '4 min'),
      _articleCard('🥛', accent, 'Calcium & os', 'Lait, yaourt, fromage blanc, sardines avec arêtes, brocoli, amandes.', '3 min'),
      _articleCard('🚫', accent, 'À éviter', 'Alcool, tabac, café > 200 mg/j, fromages non pasteurisés, viandes crues (toxoplasmose, listériose).', '3 min'),
    ];
  }

  // ── Contenu MÉNOPAUSE ──────────────────────────────────────────────────────
  List<Widget> _contenuMenopause(int tab, Color accent) {
    if (tab == 0) { return [
      _sectionTitle('🌡️ Comprendre la ménopause'),
      _infoBox(Icons.info_outline, accent, 'Définition OMS',
          'Arrêt définitif des règles depuis ≥ 12 mois. En France, âge moyen : 51 ans. Causée par la baisse des estrogènes.'),
      const SizedBox(height: 12),
      _articleCard('🔥', accent, 'Bouffées de chaleur', 'Touchent 75 % des femmes. Durée : 1–5 min. Causes : dérégulation thermorégulatrice. Facteurs aggravants : café, alcool, stress, tabac.', '5 min'),
      _articleCard('😴', accent, 'Troubles du sommeil', 'Les sueurs nocturnes interrompent le sommeil. Chambre fraîche (18–20 °C), coton, évitez les écrans 1h avant le coucher.', '4 min'),
      _articleCard('🦴', accent, 'Ostéoporose & prévention', 'La chute des estrogènes accélère la perte osseuse. Calcium 1 200 mg/j + vitamine D 800 UI/j + exercice en charge.', '6 min'),
      _articleCard('❤️', accent, 'Santé cardiovasculaire', 'Le risque cardiovasculaire augmente après la ménopause. Contrôlez tension, cholestérol et glycémie. Marche 30 min/j.', '5 min'),
      const SizedBox(height: 14),
      _sectionTitle('💊 Traitement hormonal (THS)'),
      _articleCard('⚕️', accent, 'THS — ce qu\'il faut savoir',
          'Efficace contre les bouffées de chaleur et l\'ostéoporose. Discutez risques/bénéfices avec votre gynécologue. Débuté tôt, le THS améliore la qualité de vie.', '7 min'),
    ]; }

    if (tab == 1) { return [
      _sectionTitle('🧘 Activité physique & bien-être'),
      _articleCard('🚶', accent, 'Marche nordique — 45 min', 'Renforce os et muscles, améliore l\'humeur. 3 séances/semaine minimum recommandées.', '3 min'),
      _articleCard('🏊', accent, 'Natation & aquagym', 'Impact minimal sur les articulations. Améliore circulation et bouffées de chaleur. Idéale en cas de douleurs.', '3 min'),
      _sectionTitle('🌿 Phytothérapie'),
      _articleCard('🌱', accent, 'Actée à grappes noires', 'Réduit les bouffées de chaleur légères à modérées. Dosage : 40 mg/j. Avis médical avant usage.', '4 min'),
      _articleCard('🫖', accent, 'Tisanes & relaxation', 'Valériane (sommeil), passiflore (anxiété), mélisse (humeur). Infusion 10 min, 2 tasses/j.', '3 min'),
    ]; }

    return [
      _sectionTitle('🥗 Nutrition & ménopause'),
      _infoBox(Icons.restaurant, accent, 'Besoins nutritionnels',
          'Calcium : 1 200 mg/j · Vitamine D : 800–1 000 UI/j · Protéines : 60–70 g/j · Oméga-3 : 2 g/j'),
      const SizedBox(height: 12),
      _articleCard('🐟', accent, 'Oméga-3 & cœur', 'Saumon, sardines, noix, graines de lin. Protègent le cœur et réduisent l\'inflammation.', '4 min'),
      _articleCard('🥦', accent, 'Phytoestrogènes', 'Soja, pois chiches, graines de lin. Effets modestes sur les bouffées de chaleur.', '4 min'),
      _articleCard('🚫', accent, 'À limiter', 'Alcool, café, aliments épicés (déclencheurs de bouffées). Sel < 5 g/j pour la tension artérielle.', '3 min'),
    ];
  }

  // ── Helpers UI ─────────────────────────────────────────────────────────────

  Widget _sectionTitle(String t) => Padding(
    padding: const EdgeInsets.only(top: 4, bottom: 10),
    child: Text(t, style: const TextStyle(
      fontSize: 15, fontWeight: FontWeight.w800, color: AppColors.ink)),
  );

  Widget _factCard(IconData icon, Color color, String title, String body, String badge) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Container(
          width: 42, height: 42,
          decoration: BoxDecoration(color: color.withValues(alpha: .12), shape: BoxShape.circle),
          child: Icon(icon, color: color, size: 20),
        ),
        const SizedBox(width: 12),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Expanded(child: Text(title,
              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.ink))),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
              decoration: BoxDecoration(
                color: color.withValues(alpha: .12), borderRadius: BorderRadius.circular(10)),
              child: Text(badge, style: TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: color)),
            ),
          ]),
          const SizedBox(height: 4),
          Text(body, style: const TextStyle(fontSize: 12, color: AppColors.ink2, height: 1.5)),
        ])),
      ]),
    );
  }

  Widget _articleCard(String emoji, Color color, String title, String body, String duree) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(emoji, style: const TextStyle(fontSize: 28)),
        const SizedBox(width: 12),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(title,
            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.ink)),
          const SizedBox(height: 4),
          Text(body, style: const TextStyle(fontSize: 12, color: AppColors.ink2, height: 1.5)),
          const SizedBox(height: 6),
          Row(children: [
            const Icon(Icons.access_time_rounded, size: 12, color: AppColors.ink2),
            const SizedBox(width: 4),
            Text('Lecture $duree', style: const TextStyle(fontSize: 11, color: AppColors.ink2)),
          ]),
        ])),
      ]),
    );
  }

  Widget _alertCard(String titre, String corps) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.danger.withValues(alpha: .06),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.danger.withValues(alpha: .25)),
      ),
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        const Icon(Icons.warning_amber_rounded, color: AppColors.danger, size: 18),
        const SizedBox(width: 10),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(titre, style: const TextStyle(
            fontSize: 12, fontWeight: FontWeight.w700, color: AppColors.danger)),
          const SizedBox(height: 3),
          Text(corps, style: const TextStyle(fontSize: 12, color: AppColors.ink2, height: 1.45)),
        ])),
      ]),
    );
  }

  Widget _infoBox(IconData icon, Color color, String label, String text) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: color.withValues(alpha: .08),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withValues(alpha: .25)),
      ),
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Icon(icon, color: color, size: 20),
        const SizedBox(width: 10),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(label.toUpperCase(),
            style: TextStyle(fontSize: 9, fontWeight: FontWeight.w700, color: color, letterSpacing: .7)),
          const SizedBox(height: 3),
          Text(text, style: const TextStyle(fontSize: 12, color: AppColors.ink2, height: 1.5)),
        ])),
      ]),
    );
  }
}

// ─── CARTE "À RETENIR" ────────────────────────────────────────────────────────
class _FeaturedCard extends StatelessWidget {
  final int  tab;
  final bool enceinte;
  final Color accent;
  const _FeaturedCard({required this.tab, required this.enceinte, required this.accent});

  @override
  Widget build(BuildContext context) {
    final items = enceinte ? _grossesse : _menopause;
    final item  = items[tab.clamp(0, items.length - 1)];
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: AppColors.border, width: 1.5),
      ),
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(item.$1, style: const TextStyle(fontSize: 32)),
        const SizedBox(width: 12),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text('À RETENIR', style: TextStyle(
            fontSize: 9, fontWeight: FontWeight.w800, color: accent, letterSpacing: 1)),
          const SizedBox(height: 3),
          Text(item.$2, style: const TextStyle(
            fontSize: 14, fontWeight: FontWeight.w800, color: AppColors.ink)),
          const SizedBox(height: 4),
          Text(item.$3, style: const TextStyle(
            fontSize: 12, color: AppColors.ink2, height: 1.55)),
        ])),
      ]),
    );
  }

  static const _grossesse = [
    ('💊', 'Acide folique — incontournable',
     '400 µg/jour dès la planification jusqu\'à 14 SA. Réduit de 70 % le risque de malformations du tube neural. Associez vitamine D 800 UI/j.'),
    ('🧘', 'Marche & yoga prénatal',
     '30 min de marche/jour réduit les œdèmes et les douleurs. Étude Sternfeld : -50 % de douleurs lombaires avec yoga prénatal 2×/semaine.'),
    ('🥗', 'Besoins nutritionnels +50 %',
     'Fer : 27 mg/j · Calcium : 1 000 mg/j · Acide folique : 400–800 µg · Protéines : +25 g/j · Eau : 2,5 L/j. Prise de poids recommandée : 11–16 kg.'),
  ];

  static const _menopause = [
    ('🔥', 'Gérer les bouffées de chaleur',
     'Chambre fraîche (18–20 °C), vêtements en coton, hydratation 1,5 L/j, moins de caféine. Respiration 4-7-8 lors des épisodes.'),
    ('🚶', 'Activité physique quotidienne',
     '30 min de marche ou natation 3×/semaine : réduit les bouffées, renforce les os, améliore l\'humeur. Association calcium + vitamine D indispensable.'),
    ('🥦', 'Assiette anti-ménopause',
     'Calcium 1 200 mg/j + vitamine D 800 UI/j + oméga-3 (saumon, sardines, noix). Limitez sel, alcool et aliments épicés.'),
  ];
}

