<?php

namespace Database\Seeders;

use App\Models\Administrateur;
use App\Models\CategorieContenu;
use App\Models\Contenu;
use App\Models\DemandeAdhesion;
use App\Models\EntreeSymptome;
use App\Models\Femme;
use App\Models\Grossesse;
use App\Models\Gynecologue;
use App\Models\MouvementBebe;
use App\Models\Notification;
use App\Models\Recommandation;
use App\Models\RendezVous;
use App\Models\SuiviGrossesse;
use App\Models\Symptome;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/*
 * ══════════════════════════════════════════════════════════════════════════════
 *  YOONU MAKK — Seeder complet
 * ──────────────────────────────────────────────────────────────────────────────
 *  FEMMES ENCEINTES (4)
 *    - aissatou@test.sn   →  12 SA  (1er trimestre, primigeste)
 *    - rokhaya@test.sn    →  20 SA  (2ème trimestre début)
 *    - khady@test.sn      →  28 SA  (2ème trimestre fin)
 *    - mariama@test.sn    →  36 SA  (3ème trimestre, multi-geste)
 *
 *  FEMMES MÉNOPAUSE (4)
 *    - fatou@test.sn      →  ménopause   (installée, 51 ans, Dakar)
 *    - awa@test.sn        →  péri-ménopause (47 ans, Thiès)
 *    - coumba@test.sn     →  post-ménopause (62 ans, Ziguinchor)
 *    - ndeye@test.sn      →  péri-ménopause sévère (45 ans, Kaolack)
 *
 *  Sources cliniques :
 *    - OMS / FIGO lignes directrices CPN (consultations prénatales)
 *    - Protocoles SYNGOB (Société Sénégalaise de Gynécologie-Obstétrique)
 *    - Âge moyen ménopause Afrique sub-saharienne : 49-51 ans (Harlow 2012)
 *    - Gain de poids grossesse normal (IMC 18,5-25) : 11-16 kg
 *    - TA normale grossesse : systolique 90-140 / diastolique 60-90 mmHg
 *    - Glycémie à jeun : 0,70-1,05 g/L
 * ══════════════════════════════════════════════════════════════════════════════
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════════════════════════════════════
        // 0. NETTOYAGE — rend le seeder ré-exécutable (php artisan db:seed)
        //    sans erreur de doublon (ex. users.email unique).
        // ══════════════════════════════════════════════════════════════════════
        $this->truncateTables([
            'entrees_symptomes', 'symptomes',
            'mouvements_bebe', 'suivis_grossesse', 'grossesses',
            'recommandations', 'rendez_vous', 'notifications',
            'messages_chatbot',
            'avis_professionnels', 'demandes_intermediation', 'professionnels_sante',
            'videos', 'contenus', 'categories_contenus',
            'femmes', 'administrateurs', 'gynecologues', 'demandes_adhesion',
            'users',
        ]);

        // ══════════════════════════════════════════════════════════════════════
        // 1. ADMIN
        // ══════════════════════════════════════════════════════════════════════
        $admin = User::create([
            'role'              => 'admin',
            'nom'               => 'Diallo',
            'prenom'            => 'Aminata',
            'email'             => 'admin@yoonumakk.sn',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        Administrateur::create(['user_id' => $admin->id]);

        // ══════════════════════════════════════════════════════════════════════
        // 2. GYNÉCOLOGUE
        // ══════════════════════════════════════════════════════════════════════
        $demande = DemandeAdhesion::create([
            'nom'                 => 'Sow',
            'prenom'              => 'Moussa',
            'email'               => 'dr.sow@test.sn',
            'telephone'           => '770001122',
            'numero_ordre'        => 'OM-2024-001',
            'specialite'          => 'Gynécologie-Obstétrique',
            'annees_experience'   => 15,
            'structure_sante'     => 'Hôpital Principal de Dakar',
            'ville'               => 'Dakar',
            'chemin_diplome'      => 'demandes/diplomes/test.pdf',
            'chemin_justificatif' => 'demandes/justificatifs/test.pdf',
            'statut'              => 'approuvee',
            'traite_par'          => $admin->id,
            'traite_le'           => now(),
        ]);
        $gyn = Gynecologue::create([
            'demande_adhesion_id' => $demande->id,
            'nom'                 => 'Sow',
            'prenom'              => 'Moussa',
            'email'               => 'dr.sow@test.sn',
            'password'            => Hash::make('password'),
            'telephone'           => '770001122',
            'numero_ordre'        => 'OM-2024-001',
            'specialite'          => 'Gynécologie-Obstétrique',
            'annees_experience'   => 15,
            'structure_sante'     => 'Hôpital Principal de Dakar',
            'ville'               => 'Dakar',
            'bio'                 => 'Spécialiste en santé de la femme, suivi grossesse à risque et ménopause.',
            'email_verified_at'   => now(),
        ]);

        // Ajout de nouveaux gynécologues via la Factory
        Gynecologue::factory()->create([
            'prenom' => 'Fatoumata',
            'nom' => 'Diallo',
            'email' => 'dr.diallo@test.sn',
            'ville' => 'Saint-Louis',
            'structure_sante' => 'Hôpital Régional de Saint-Louis',
        ]);

        Gynecologue::factory()->create([
            'prenom' => 'Khady',
            'nom' => 'Ndiaye',
            'email' => 'dr.ndiaye@test.sn',
            'ville' => 'Thiès',
            'structure_sante' => 'Hôpital Ahmadou Sakhir Ndiéguène',
        ]);

        Gynecologue::factory()->count(3)->create(); // 3 gynécologues aléatoires supplémentaires

        // ══════════════════════════════════════════════════════════════════════
        // 3. FEMMES ENCEINTES
        // ══════════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────────────
        // P1 — Aissatou Bâ · 12 SA · 1er trimestre · primigeste
        // Née 1998, Saint-Louis. Première grossesse, nausées sévères.
        // Source : symptômes T1 classiques (OMS) : nausées/vomissements 80 %,
        //          fatigue profonde, vertiges orthostatiques.
        // ─────────────────────────────────────────────────────
        $u_aissatou = User::create([
            'role'              => 'patient',
            'nom'               => 'Bâ',
            'prenom'            => 'Aissatou',
            'email'             => 'aissatou@test.sn',
            'password'          => Hash::make('password'),
            'telephone'         => '778889900',
            'date_naissance'    => '1998-11-25',
            'ville'             => 'Saint-Louis',
            'email_verified_at' => now(),
        ]);
        $f_aissatou = Femme::create(['user_id' => $u_aissatou->id, 'type_profil' => 'grossesse']);
        $g_aissatou = Grossesse::create([
            'femme_id'                        => $f_aissatou->id,
            'date_debut_grossesse'            => now()->subDays(84)->toDateString(),   // 12 SA
            'date_accouchement_prevue'        => now()->addDays(196)->toDateString(),
            'groupe_sanguin'                  => 'O+',
            'nombre_grossesses_anterieures'   => 0,
            'nombre_accouchements_anterieurs' => 0,
            'antecedents_obstetricaux'        => 'Première grossesse. Pas d\'antécédent médical notable.',
            'grossesse_active'                => true,
        ]);
        // Suivis CPN
        SuiviGrossesse::create([
            'grossesse_id'        => $g_aissatou->id,
            'semaines_amenorrhee' => 8,
            'poids_kg'            => 55.0,
            'tension_systolique'  => 110, 'tension_diastolique' => 65,
            'glycemie'            => 0.82,
            'notes'               => 'Nausées importantes. Conseils alimentaires : fractionner les repas, éviter les odeurs fortes. Acide folique 400µg prescrit.',
            'date_saisie'         => now()->subDays(28)->toDateString(),
        ]);
        SuiviGrossesse::create([
            'grossesse_id'        => $g_aissatou->id,
            'semaines_amenorrhee' => 12,
            'poids_kg'            => 56.3,
            'tension_systolique'  => 112, 'tension_diastolique' => 68,
            'glycemie'            => 0.84,
            'notes'               => 'Nausées en diminution. Résultats dépistage T21 normaux. Échographie T1 : crown-rump length conforme 12 SA. Cou fœtal normal.',
            'date_saisie'         => now()->toDateString(),
        ]);
        // Mouvements (fictifs à 12 SA pour démo — perçus normalement vers 18-20 SA)
        MouvementBebe::create(['grossesse_id' => $g_aissatou->id, 'date_heure' => now()->subHours(2), 'nombre_mouvements' => 2, 'intensite' => 'leger']);
        // Symptômes
        $s = Symptome::create(['femme_id' => $f_aissatou->id, 'date_journal' => now()->subDays(1)->toDateString(), 'note_generale' => 'Nausées toute la journée, grosse fatigue, quelques vertiges en se levant.']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'nausee_vomissement', 'intensite' => 5, 'commentaire' => 'Toute la journée, vomissements 2-3 fois']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'fatigue',            'intensite' => 4, 'commentaire' => 'Dort 10h mais reste épuisée']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'vertiges_grossesse', 'intensite' => 3, 'commentaire' => 'En se levant le matin']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'brulure_estomac',    'intensite' => 2]);
        // Recommandation
        Recommandation::create([
            'femme_id' => $f_aissatou->id, 'type' => 'prenatal',
            'titre'    => 'Acide folique & vitamine D',
            'corps'    => 'Prendre 400 µg d\'acide folique/jour jusqu\'à 14 SA (prévention spina bifida). Vitamine D 800 UI/jour. Fractionner les repas en 5 petites prises pour limiter les nausées.',
            'genere_par' => 'gynecologue', 'gynecologue_id' => $gyn->id,
        ]);
        // Notification
        Notification::create(['user_id' => $u_aissatou->id, 'type' => 'felicitations_grossesse', 'titre' => 'Bienvenue Aissatou 🎉', 'corps' => 'Félicitations ! Votre espace grossesse est prêt. Suivez semaine par semaine votre bébé.']);

        // ─────────────────────────────────────────────────────
        // P2 — Rokhaya Faye · 20 SA · 2ème trimestre début
        // Née 1993, Mbour. Deuxième grossesse. Anémie ferriprive légère.
        // Source : anémie grossesse Sénégal prévalence ~50 % (OMS Afrique),
        //          mouvements fœtaux perçus dès 18-20 SA.
        // ─────────────────────────────────────────────────────
        $u_rokhaya = User::create([
            'role'              => 'patient',
            'nom'               => 'Faye',
            'prenom'            => 'Rokhaya',
            'email'             => 'rokhaya@test.sn',
            'password'          => Hash::make('password'),
            'telephone'         => '776661234',
            'date_naissance'    => '1993-07-18',
            'ville'             => 'Mbour',
            'email_verified_at' => now(),
        ]);
        $f_rokhaya = Femme::create(['user_id' => $u_rokhaya->id, 'type_profil' => 'grossesse']);
        $g_rokhaya = Grossesse::create([
            'femme_id'                        => $f_rokhaya->id,
            'date_debut_grossesse'            => now()->subDays(140)->toDateString(),   // 20 SA
            'date_accouchement_prevue'        => now()->addDays(140)->toDateString(),
            'groupe_sanguin'                  => 'A+',
            'nombre_grossesses_anterieures'   => 1,
            'nombre_accouchements_anterieurs' => 1,
            'antecedents_obstetricaux'        => 'Premier enfant né à terme par voie basse. Anémie légère lors de la première grossesse.',
            'grossesse_active'                => true,
        ]);
        SuiviGrossesse::create([
            'grossesse_id' => $g_rokhaya->id, 'semaines_amenorrhee' => 12,
            'poids_kg' => 59.0, 'tension_systolique' => 108, 'tension_diastolique' => 66, 'glycemie' => 0.83,
            'notes'    => 'CPN1 — Tout normal. NFS : Hb 10,8 g/dL (légère anémie). Sulfate ferreux 200mg prescrit.',
            'date_saisie' => now()->subDays(56)->toDateString(),
        ]);
        SuiviGrossesse::create([
            'grossesse_id' => $g_rokhaya->id, 'semaines_amenorrhee' => 16,
            'poids_kg' => 61.2, 'tension_systolique' => 110, 'tension_diastolique' => 68, 'glycemie' => 0.85,
            'notes'    => 'CPN2 — Nausées disparues. Hb 11,5 g/dL, amélioration sous fer. Mouvements bébé non encore perçus.',
            'date_saisie' => now()->subDays(28)->toDateString(),
        ]);
        SuiviGrossesse::create([
            'grossesse_id' => $g_rokhaya->id, 'semaines_amenorrhee' => 20,
            'poids_kg' => 63.5, 'tension_systolique' => 112, 'tension_diastolique' => 70, 'glycemie' => 0.87,
            'notes'    => 'CPN3 — Premiers mouvements perçus il y a 3 jours. Échographie morphologique T2 : bébé eutrophe, biométries concordantes. Placenta normalement inséré.',
            'date_saisie' => now()->toDateString(),
        ]);
        // Mouvements bébé (premiers mouvements — "butterflies")
        MouvementBebe::create(['grossesse_id' => $g_rokhaya->id, 'date_heure' => now()->subDays(3)->setHour(20), 'nombre_mouvements' => 2, 'intensite' => 'leger']);
        MouvementBebe::create(['grossesse_id' => $g_rokhaya->id, 'date_heure' => now()->subDays(2)->setHour(14), 'nombre_mouvements' => 3, 'intensite' => 'leger']);
        MouvementBebe::create(['grossesse_id' => $g_rokhaya->id, 'date_heure' => now()->subDays(1)->setHour(21), 'nombre_mouvements' => 4, 'intensite' => 'leger']);
        MouvementBebe::create(['grossesse_id' => $g_rokhaya->id, 'date_heure' => now()->subHours(3),             'nombre_mouvements' => 3, 'intensite' => 'modere']);
        // Symptômes
        $s = Symptome::create(['femme_id' => $f_rokhaya->id, 'date_journal' => now()->subDays(2)->toDateString(), 'note_generale' => 'Douleurs lombaires légères. Quelques brûlures d\'estomac après le dîner.']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'douleurs_dorsales', 'intensite' => 3, 'commentaire' => 'Surtout en fin de journée']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'brulure_estomac',   'intensite' => 2, 'commentaire' => 'Après les repas copieux']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'fatigue',           'intensite' => 2]);
        // RDV
        RendezVous::create([
            'femme_id' => $f_rokhaya->id, 'gynecologue_id' => $gyn->id,
            'date_souhaitee' => now()->addDays(14)->toDateString(), 'heure_souhaitee' => '11:00',
            'motif'   => 'CPN4 — Contrôle 24 SA + dépistage diabète gestationnel (HGPO)',
            'contexte' => 'grossesse', 'statut' => 'accepte',
            'date_confirmee' => now()->addDays(14)->toDateString(), 'heure_confirmee' => '11:30',
        ]);
        Recommandation::create([
            'femme_id' => $f_rokhaya->id, 'type' => 'nutrition',
            'titre'    => 'Alimentation riche en fer',
            'corps'    => 'Consommer viande rouge 3×/semaine, légumineuses (niébé, lentilles), feuilles de bissap. Associer avec vitamine C (citron, tomate) pour favoriser l\'absorption. Éviter thé/café dans l\'heure suivant les repas.',
            'genere_par' => 'gynecologue', 'gynecologue_id' => $gyn->id,
        ]);
        Notification::create(['user_id' => $u_rokhaya->id, 'type' => 'rappel_suivi_grossesse', 'titre' => 'CPN4 dans 2 semaines', 'corps' => 'N\'oubliez pas votre consultation du 24ème SA avec le dépistage du diabète gestationnel.']);

        // ─────────────────────────────────────────────────────
        // P3 — Khady Sall · 28 SA · 2ème trimestre fin
        // Née 1995, Dakar. 2ème grossesse. Anémie légère traitée.
        // Source : test de Kleihauer-Betke recommandé si Rh-, HGPO T2.
        // ─────────────────────────────────────────────────────
        $u_khady = User::create([
            'role'              => 'patient',
            'nom'               => 'Sall',
            'prenom'            => 'Khady',
            'email'             => 'khady@test.sn',
            'password'          => Hash::make('password'),
            'telephone'         => '775551122',
            'date_naissance'    => '1995-04-10',
            'ville'             => 'Dakar',
            'email_verified_at' => now(),
        ]);
        $f_khady = Femme::create(['user_id' => $u_khady->id, 'type_profil' => 'grossesse']);
        $g_khady = Grossesse::create([
            'femme_id'                        => $f_khady->id,
            'date_debut_grossesse'            => now()->subDays(196)->toDateString(),   // 28 SA
            'date_accouchement_prevue'        => now()->addDays(84)->toDateString(),
            'groupe_sanguin'                  => 'B+',
            'nombre_grossesses_anterieures'   => 1,
            'nombre_accouchements_anterieurs' => 1,
            'antecedents_obstetricaux'        => 'Premier enfant né à terme par voie basse sans complication. Pas d\'allergie connue.',
            'grossesse_active'                => true,
        ]);
        SuiviGrossesse::create([
            'grossesse_id' => $g_khady->id, 'semaines_amenorrhee' => 20,
            'poids_kg' => 62.5, 'tension_systolique' => 115, 'tension_diastolique' => 70, 'glycemie' => 0.85,
            'notes'    => 'Mouvements bien perçus. Écho morphologique T2 : fœtus en présentation céphalique, biométries normales. Placenta postérieur. Prise de poids conforme (+4 kg T1+T2).',
            'date_saisie' => now()->subDays(56)->toDateString(),
        ]);
        SuiviGrossesse::create([
            'grossesse_id' => $g_khady->id, 'semaines_amenorrhee' => 24,
            'poids_kg' => 64.8, 'tension_systolique' => 118, 'tension_diastolique' => 72, 'glycemie' => 0.88,
            'notes'    => 'Légère anémie ferriprive (Hb 10,5 g/dL). Sulfate ferreux 200mg + acide folique prescrit. HGPO normal (glycémie à jeun 0,88, 1h 1,38, 2h 1,15 — seuils non dépassés).',
            'date_saisie' => now()->subDays(28)->toDateString(),
        ]);
        SuiviGrossesse::create([
            'grossesse_id' => $g_khady->id, 'semaines_amenorrhee' => 28,
            'poids_kg' => 67.2, 'tension_systolique' => 120, 'tension_diastolique' => 75, 'glycemie' => 0.90,
            'notes'    => 'Hb remontée à 11,8 g/dL sous fer. Bébé actif. TA stable. Conseils posture sommeil (décubitus latéral gauche). Prochain RDV 32 SA + vaccin coqueluche.',
            'date_saisie' => now()->toDateString(),
        ]);
        // Mouvements bébé (bébé très actif à 28 SA)
        MouvementBebe::create(['grossesse_id' => $g_khady->id, 'date_heure' => now()->subHours(5),             'nombre_mouvements' => 3,  'intensite' => 'leger']);
        MouvementBebe::create(['grossesse_id' => $g_khady->id, 'date_heure' => now()->subHours(3),             'nombre_mouvements' => 5,  'intensite' => 'modere']);
        MouvementBebe::create(['grossesse_id' => $g_khady->id, 'date_heure' => now()->subHours(1),             'nombre_mouvements' => 4,  'intensite' => 'fort']);
        MouvementBebe::create(['grossesse_id' => $g_khady->id, 'date_heure' => now()->subDays(1)->setHour(10), 'nombre_mouvements' => 6,  'intensite' => 'modere']);
        MouvementBebe::create(['grossesse_id' => $g_khady->id, 'date_heure' => now()->subDays(1)->setHour(20), 'nombre_mouvements' => 8,  'intensite' => 'fort']);
        MouvementBebe::create(['grossesse_id' => $g_khady->id, 'date_heure' => now()->subDays(2)->setHour(15), 'nombre_mouvements' => 7,  'intensite' => 'modere']);
        // Symptômes
        $s = Symptome::create(['femme_id' => $f_khady->id, 'date_journal' => now()->subDays(3)->toDateString(), 'note_generale' => 'Dos très douloureux. Nausées matinales légères. Sommeil difficile.']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'douleurs_dorsales',  'intensite' => 4, 'commentaire' => 'Douleurs lombaires, pire le soir']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'nausee_vomissement', 'intensite' => 2, 'commentaire' => 'À jeun le matin uniquement']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'fatigue',            'intensite' => 3]);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'brulure_estomac',    'intensite' => 3, 'commentaire' => 'Reflux gastro-œsophagien fréquent']);
        $s = Symptome::create(['femme_id' => $f_khady->id, 'date_journal' => now()->toDateString(), 'note_generale' => 'Légères contractions de Braxton Hicks. Jambes lourdes.']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'contractions_benignes', 'intensite' => 2, 'commentaire' => 'Irrégulières, cèdent au repos — Braxton Hicks']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'oedemes_jambes',        'intensite' => 2, 'commentaire' => 'Chevilles gonflées en fin de journée']);
        // RDV
        RendezVous::create([
            'femme_id' => $f_khady->id, 'gynecologue_id' => $gyn->id,
            'date_souhaitee' => now()->addDays(7)->toDateString(), 'heure_souhaitee' => '09:00',
            'motif'   => 'Échographie 3ème trimestre + bilan sanguin complet',
            'contexte' => 'grossesse', 'statut' => 'accepte',
            'date_confirmee' => now()->addDays(7)->toDateString(), 'heure_confirmee' => '09:30',
        ]);
        Recommandation::create([
            'femme_id' => $f_khady->id, 'type' => 'prenatal',
            'titre'    => 'Supplémentation fer & préparation accouchement',
            'corps'    => 'Poursuivre sulfate ferreux 200mg/jour. Dormir en décubitus latéral gauche (améliore la circulation placentaire). Cours de préparation à l\'accouchement recommandés dès 32 SA. Hydratation : 2,5L/jour.',
            'genere_par' => 'gynecologue', 'gynecologue_id' => $gyn->id,
        ]);
        Notification::create(['user_id' => $u_khady->id, 'type' => 'rappel_suivi_grossesse', 'titre' => 'Échographie T3 dans 7 jours', 'corps' => 'Votre prochain RDV prénatal est dans 7 jours. Pensez à apporter votre carnet de santé.']);

        // ─────────────────────────────────────────────────────
        // P4 — Mariama Diop · 36 SA · 3ème trimestre · multigeste
        // Née 1990, Ziguinchor. 3ème grossesse. Pré-éclampsie légère surveillée.
        // Source : pré-éclampsie prévalence 5-8 % grossesses (FIGO), dépistage
        //          TA >140/90 à deux reprises. Aspégic 100mg prophylactique.
        // ─────────────────────────────────────────────────────
        $u_mariama = User::create([
            'role'              => 'patient',
            'nom'               => 'Diop',
            'prenom'            => 'Mariama',
            'email'             => 'mariama@test.sn',
            'password'          => Hash::make('password'),
            'telephone'         => '773334455',
            'date_naissance'    => '1990-02-14',
            'ville'             => 'Ziguinchor',
            'email_verified_at' => now(),
        ]);
        $f_mariama = Femme::create(['user_id' => $u_mariama->id, 'type_profil' => 'grossesse']);
        $g_mariama = Grossesse::create([
            'femme_id'                        => $f_mariama->id,
            'date_debut_grossesse'            => now()->subDays(252)->toDateString(),   // 36 SA
            'date_accouchement_prevue'        => now()->addDays(28)->toDateString(),
            'groupe_sanguin'                  => 'AB+',
            'nombre_grossesses_anterieures'   => 2,
            'nombre_accouchements_anterieurs' => 2,
            'antecedents_obstetricaux'        => '2 accouchements par voie basse. Épisiotomie lors du 2ème accouchement. Antécédent de tension artérielle élevée en fin de grossesse.',
            'grossesse_active'                => true,
        ]);
        SuiviGrossesse::create([
            'grossesse_id' => $g_mariama->id, 'semaines_amenorrhee' => 24,
            'poids_kg' => 70.0, 'tension_systolique' => 122, 'tension_diastolique' => 78, 'glycemie' => 0.91,
            'notes'    => 'Aspégic 100mg/jour prescrit (risque pré-éclampsie). Tout stable. Bébé en céphalique.',
            'date_saisie' => now()->subDays(84)->toDateString(),
        ]);
        SuiviGrossesse::create([
            'grossesse_id' => $g_mariama->id, 'semaines_amenorrhee' => 28,
            'poids_kg' => 73.5, 'tension_systolique' => 128, 'tension_diastolique' => 82, 'glycemie' => 0.93,
            'notes'    => 'Légère hausse TA. Repos relatif conseillé. Protéinurie 0 (bandelette urinaire). Surveillance renforcée. Hb 11,2 g/dL.',
            'date_saisie' => now()->subDays(56)->toDateString(),
        ]);
        SuiviGrossesse::create([
            'grossesse_id' => $g_mariama->id, 'semaines_amenorrhee' => 32,
            'poids_kg' => 76.0, 'tension_systolique' => 132, 'tension_diastolique' => 84, 'glycemie' => 0.95,
            'notes'    => 'TA légèrement élevée mais < 140/90 : poursuite aspégic. Œdèmes membres inférieurs discrets. Bébé estimé 1900g à l\'écho — eutrophe.',
            'date_saisie' => now()->subDays(28)->toDateString(),
        ]);
        SuiviGrossesse::create([
            'grossesse_id' => $g_mariama->id, 'semaines_amenorrhee' => 36,
            'poids_kg' => 78.5, 'tension_systolique' => 135, 'tension_diastolique' => 86, 'glycemie' => 0.97,
            'notes'    => 'TA stable sous surveillance. Bébé en céphalique, estimé 2700g. Score de Manning 8/10. Prévision accouchement voie basse. Hospitalisation si TA > 140/90.',
            'date_saisie' => now()->toDateString(),
        ]);
        // Mouvements très actifs à 36 SA
        MouvementBebe::create(['grossesse_id' => $g_mariama->id, 'date_heure' => now()->subHours(6),             'nombre_mouvements' => 8,  'intensite' => 'fort']);
        MouvementBebe::create(['grossesse_id' => $g_mariama->id, 'date_heure' => now()->subHours(4),             'nombre_mouvements' => 6,  'intensite' => 'modere']);
        MouvementBebe::create(['grossesse_id' => $g_mariama->id, 'date_heure' => now()->subHours(2),             'nombre_mouvements' => 10, 'intensite' => 'fort']);
        MouvementBebe::create(['grossesse_id' => $g_mariama->id, 'date_heure' => now()->subDays(1)->setHour(8),  'nombre_mouvements' => 7,  'intensite' => 'fort']);
        MouvementBebe::create(['grossesse_id' => $g_mariama->id, 'date_heure' => now()->subDays(1)->setHour(14), 'nombre_mouvements' => 9,  'intensite' => 'fort']);
        MouvementBebe::create(['grossesse_id' => $g_mariama->id, 'date_heure' => now()->subDays(1)->setHour(22), 'nombre_mouvements' => 11, 'intensite' => 'fort']);
        MouvementBebe::create(['grossesse_id' => $g_mariama->id, 'date_heure' => now()->subDays(2)->setHour(17), 'nombre_mouvements' => 8,  'intensite' => 'modere']);
        // Symptômes 3ème trimestre
        $s = Symptome::create(['femme_id' => $f_mariama->id, 'date_journal' => now()->subDays(1)->toDateString(), 'note_generale' => 'Jambes très gonflées, douleurs dorsales intenses, contractions irrégulières.']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'oedemes_jambes',        'intensite' => 4, 'commentaire' => 'Prend 3 kg d\'eau la nuit']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'douleurs_dorsales',     'intensite' => 5, 'commentaire' => 'Douleurs pelvi-périnéales']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'contractions_benignes', 'intensite' => 3, 'commentaire' => 'Toutes les 20-30 min — Braxton Hicks fréquents']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'constipation_grossesse','intensite' => 3]);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'fatigue',               'intensite' => 4]);
        $s = Symptome::create(['femme_id' => $f_mariama->id, 'date_journal' => now()->toDateString(), 'note_generale' => 'Contractions plus rapprochées ce matin. Bébé bouge bien.']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'contractions_benignes', 'intensite' => 3, 'commentaire' => 'Espacées 15 min, indolores']);
        EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'brulure_estomac',       'intensite' => 3]);
        // RDV urgent
        RendezVous::create([
            'femme_id' => $f_mariama->id, 'gynecologue_id' => $gyn->id,
            'date_souhaitee' => now()->addDays(3)->toDateString(), 'heure_souhaitee' => '08:00',
            'motif'   => 'Surveillance TA + monitoring fœtal préaccouchement',
            'contexte' => 'grossesse', 'statut' => 'accepte',
            'date_confirmee' => now()->addDays(3)->toDateString(), 'heure_confirmee' => '08:30',
        ]);
        Recommandation::create([
            'femme_id' => $f_mariama->id, 'type' => 'preparation_accouchement',
            'titre'    => 'Préparation accouchement & surveillance TA',
            'corps'    => 'Repos en décubitus latéral gauche. Mesurer TA 2×/jour et noter. Consulter en urgence si TA ≥ 140/90, maux de tête violents, troubles visuels ou douleurs épigastriques. Préparer sac maternité. Accouchement prévu dans 4 semaines.',
            'genere_par' => 'gynecologue', 'gynecologue_id' => $gyn->id,
        ]);
        Recommandation::create([
            'femme_id' => $f_mariama->id, 'type' => 'hygiene_vie',
            'titre'    => 'Signes d\'alarme pré-éclampsie',
            'corps'    => 'URGENCE si : céphalées en casque, phosphènes (mouches volantes), épigastralgies, TA ≥ 160/110. Appelez immédiatement le 15 ou rendez-vous aux urgences de la maternité.',
            'genere_par' => 'gynecologue', 'gynecologue_id' => $gyn->id,
        ]);
        Notification::create(['user_id' => $u_mariama->id, 'type' => 'rappel_suivi_grossesse', 'titre' => '⚠️ Surveillance TA importante', 'corps' => 'Surveillez votre tension artérielle quotidiennement. RDV monitoring dans 3 jours.']);

        // ══════════════════════════════════════════════════════════════════════
        // 4. FEMMES MÉNOPAUSE
        // ══════════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────────────
        // M1 — Fatou Ndiaye · ménopause installée · 51 ans · Dakar
        // Ménopause depuis juin 2023. Bouffées de chaleur fréquentes.
        // Source : prévalence bouffées de chaleur ménopause : 75-85 % des femmes.
        //          Âge moyen ménopause Sénégal : 49,8 ans (étude CHU Dakar 2018).
        // ─────────────────────────────────────────────────────
        $u_fatou = User::create([
            'role'              => 'patient',
            'nom'               => 'Ndiaye',
            'prenom'            => 'Fatou',
            'email'             => 'fatou@test.sn',
            'password'          => Hash::make('password'),
            'telephone'         => '771234567',
            'date_naissance'    => '1975-03-15',
            'ville'             => 'Dakar',
            'email_verified_at' => now(),
        ]);
        $f_fatou = Femme::create([
            'user_id'              => $u_fatou->id,
            'date_debut_menopause' => '2023-06-01',
            'stade_menopause'      => 'menopause',
            'antecedents_medicaux' => 'Hypertension artérielle légère (traitée). Antécédent familial : mère ménopause précoce à 47 ans.',
        ]);
        // Symptômes variés sur plusieurs jours
        foreach ([
            ['date' => '2026-06-01', 'note' => 'Journée difficile : 5 bouffées de chaleur, nuit sans sommeil.',
             'symptoms' => [['bouffees_chaleur',4,'Surtout 10h-12h et 22h-2h'],['sueurs_nocturnes',4,'Réveillée 3 fois'],['fatigue',3,'Épuisée après nuit blanche']]],
            ['date' => '2026-06-02', 'note' => 'Un peu mieux. Irritabilité persistante.',
             'symptoms' => [['bouffees_chaleur',2],['stress',3,'Anxieuse pour sa santé'],['irritabilite',3]]],
            ['date' => '2026-06-04', 'note' => 'Douleurs articulaires aux genoux. Sommeil toujours perturbé.',
             'symptoms' => [['douleurs_articulaires',3,'Genoux et hanches le matin'],['troubles_sommeil',4,'Endormissement difficile + réveils'],['secheresse_vaginale',2,'Inconfort noté']]],
            ['date' => '2026-06-06', 'note' => 'Bouffée de chaleur au travail. Gênant en public.',
             'symptoms' => [['bouffees_chaleur',3,'En réunion, très gênant'],['anxiete',3,'Anxieuse du regard des autres'],['fatigue',2]]],
        ] as $entry) {
            $s = Symptome::create(['femme_id' => $f_fatou->id, 'date_journal' => $entry['date'], 'note_generale' => $entry['note']]);
            foreach ($entry['symptoms'] as $sym) {
                EntreeSymptome::create(array_filter([
                    'symptome_id'  => $s->id,
                    'type_symptome' => $sym[0],
                    'intensite'    => $sym[1],
                    'commentaire'  => $sym[2] ?? null,
                ]));
            }
        }
        // RDV
        RendezVous::create([
            'femme_id' => $f_fatou->id, 'gynecologue_id' => $gyn->id,
            'date_souhaitee' => '2026-06-15', 'heure_souhaitee' => '10:00',
            'motif' => 'Consultation bouffées de chaleur + bilan osseux (ostéodensitométrie)',
            'statut' => 'accepte', 'date_confirmee' => '2026-06-15', 'heure_confirmee' => '10:30',
            'note_gynecologue' => 'Prévoir bilan sanguin : FSH, LH, E2, TSH, NFP.',
        ]);
        RendezVous::create([
            'femme_id' => $f_fatou->id, 'gynecologue_id' => $gyn->id,
            'date_souhaitee' => '2026-06-20', 'heure_souhaitee' => '14:00',
            'motif' => 'Résultats bilan + discussion traitement hormonal substitutif',
            'statut' => 'en_attente',
        ]);
        Recommandation::create([
            'femme_id' => $f_fatou->id, 'type' => 'nutrition', 'titre' => 'Phytoestrogènes & calcium',
            'corps'    => 'Consommer soja (tofu, lait de soja), graines de lin, pois chiche. Ces aliments contiennent des isoflavones qui réduisent les bouffées de chaleur. Calcium : 1200mg/jour (produits laitiers, sardines avec arêtes, feuilles de ceep bu dëkk). Vitamine D 1000 UI/jour.',
            'genere_par' => 'gynecologue', 'gynecologue_id' => $gyn->id,
        ]);
        Notification::create(['user_id' => $u_fatou->id, 'type' => 'statut_rendez_vous', 'titre' => 'RDV confirmé — 15 juin', 'corps' => 'Votre consultation du 15 juin à 10h30 est confirmée. Pensez au bilan sanguin à jeun.']);

        // ─────────────────────────────────────────────────────
        // M2 — Awa Fall · péri-ménopause · 47 ans · Thiès
        // Cycles irréguliers depuis 2 ans. Sautes d'humeur marquées.
        // Source : péri-ménopause dure en moyenne 4-8 ans (NAMS 2022).
        //          Cycles >7j de variation = signes péri-méno (STRAW+10).
        // ─────────────────────────────────────────────────────
        $u_awa = User::create([
            'role'              => 'patient',
            'nom'               => 'Fall',
            'prenom'            => 'Awa',
            'email'             => 'awa@test.sn',
            'password'          => Hash::make('password'),
            'telephone'         => '776543210',
            'date_naissance'    => '1979-08-22',
            'ville'             => 'Thiès',
            'email_verified_at' => now(),
        ]);
        $f_awa = Femme::create([
            'user_id'              => $u_awa->id,
            'stade_menopause'      => 'perimenopause',
            'antecedents_medicaux' => 'Légère hypothyroïdie sous Lévothyrox 50µg. Cycles irréguliers depuis 2 ans (25-45j).',
        ]);
        foreach ([
            ['date' => '2026-06-03', 'note' => 'Premières bouffées de chaleur légères. Irritable toute la journée.',
             'symptoms' => [['bouffees_chaleur',2,'Légères, 2-3/jour'],['irritabilite',4,'Sautes d\'humeur fréquentes'],['troubles_sommeil',3,'Difficultés d\'endormissement']]],
            ['date' => '2026-06-05', 'note' => 'Fatigue importante malgré 8h de sommeil. Anxiété diffuse.',
             'symptoms' => [['fatigue',4,'Fatigue non expliquée'],['anxiete',3,'Inquiétude sur l\'évolution'],['sueurs_nocturnes',2,'Légères cette nuit']]],
        ] as $entry) {
            $s = Symptome::create(['femme_id' => $f_awa->id, 'date_journal' => $entry['date'], 'note_generale' => $entry['note']]);
            foreach ($entry['symptoms'] as $sym) {
                EntreeSymptome::create(array_filter(['symptome_id' => $s->id, 'type_symptome' => $sym[0], 'intensite' => $sym[1], 'commentaire' => $sym[2] ?? null]));
            }
        }
        RendezVous::create([
            'femme_id' => $f_awa->id, 'gynecologue_id' => $gyn->id,
            'date_souhaitee' => now()->addDays(10)->toDateString(), 'heure_souhaitee' => '15:00',
            'motif' => 'Bilan péri-ménopause + ajustement Lévothyrox si besoin',
            'statut' => 'en_attente',
        ]);
        Recommandation::create([
            'femme_id' => $f_awa->id, 'type' => 'activite_physique', 'titre' => 'Activité physique adaptée péri-ménopause',
            'corps'    => 'Marche rapide 30 min/jour (réduit les bouffées de chaleur de 50 % selon étude Sternfeld 2014). Yoga ou pilates 2×/semaine pour réduire le stress. Natation excellente pour les articulations. Éviter sport intense en milieu de journée (chaleur).',
            'genere_par' => 'gynecologue', 'gynecologue_id' => $gyn->id,
        ]);
        Notification::create(['user_id' => $u_awa->id, 'type' => 'rappel_rendez_vous', 'titre' => 'RDV dans 10 jours', 'corps' => 'Votre consultation péri-ménopause est prévue dans 10 jours. Notez vos symptômes d\'ici là.']);

        // ─────────────────────────────────────────────────────
        // M3 — Coumba Sarr · post-ménopause · 62 ans · Ziguinchor
        // Ménopause depuis 2016. Ostéoporose débutante, sécheresse vaginale.
        // Source : 1 femme sur 3 post-méno développe ostéoporose (OMS 2003).
        //          T-score DXA < -2,5 = ostéoporose. Prévalence SV post-méno : 40-54%.
        // ─────────────────────────────────────────────────────
        $u_coumba = User::create([
            'role'              => 'patient',
            'nom'               => 'Sarr',
            'prenom'            => 'Coumba',
            'email'             => 'coumba@test.sn',
            'password'          => Hash::make('password'),
            'telephone'         => '772223344',
            'date_naissance'    => '1964-01-30',
            'ville'             => 'Ziguinchor',
            'email_verified_at' => now(),
        ]);
        $f_coumba = Femme::create([
            'user_id'              => $u_coumba->id,
            'date_debut_menopause' => '2016-03-01',
            'stade_menopause'      => 'postmenopause',
            'antecedents_medicaux' => 'Ostéoporose débutante (T-score -2,1 rachis). Diabète type 2 équilibré sous metformine. Ménopause à 52 ans.',
        ]);
        foreach ([
            ['date' => '2026-06-02', 'note' => 'Douleurs articulaires persistantes. Troubles urinaires légers.',
             'symptoms' => [['douleurs_articulaires',4,'Genoux, hanches, colonne — pire le matin'],['troubles_urinaires',3,'Envie fréquente, légère incontinence d\'effort'],['secheresse_vaginale',3,'Inconfort quotidien, relations douloureuses']]],
            ['date' => '2026-06-05', 'note' => 'Fatigue chronique. Quelques bouffées résiduelles.',
             'symptoms' => [['fatigue',3,'Fatigue chronique post-ménopause'],['bouffees_chaleur',1,'Très légères, résiduelles'],['troubles_sommeil',3,'Sommeil non réparateur']]],
        ] as $entry) {
            $s = Symptome::create(['femme_id' => $f_coumba->id, 'date_journal' => $entry['date'], 'note_generale' => $entry['note']]);
            foreach ($entry['symptoms'] as $sym) {
                EntreeSymptome::create(array_filter(['symptome_id' => $s->id, 'type_symptome' => $sym[0], 'intensite' => $sym[1], 'commentaire' => $sym[2] ?? null]));
            }
        }
        RendezVous::create([
            'femme_id' => $f_coumba->id, 'gynecologue_id' => $gyn->id,
            'date_souhaitee' => now()->addDays(5)->toDateString(), 'heure_souhaitee' => '09:00',
            'motif' => 'Suivi annuel post-ménopause + contrôle densité osseuse',
            'statut' => 'accepte',
            'date_confirmee' => now()->addDays(5)->toDateString(), 'heure_confirmee' => '09:30',
        ]);
        Recommandation::create([
            'femme_id' => $f_coumba->id, 'type' => 'nutrition', 'titre' => 'Prévention ostéoporose',
            'corps'    => 'Calcium 1500mg/jour (indispensable post-ménopause). Vitamine D 2000 UI/jour avec sun exposure 15 min/matin. Alimentation : poissons gras (maquereau, sardine), légumes verts à feuilles (ndiamankou, yeet). Réduire sel et alcool. Éviter tabac (accélère perte osseuse).',
            'genere_par' => 'gynecologue', 'gynecologue_id' => $gyn->id,
        ]);
        Recommandation::create([
            'femme_id' => $f_coumba->id, 'type' => 'activite_physique', 'titre' => 'Exercices anti-ostéoporose',
            'corps'    => 'Exercices en charge (marche, tai-chi) : stimulent la formation osseuse. Musculation légère 2×/semaine. Exercices d\'équilibre pour prévenir les chutes. Éviter flexion/rotation brusque de la colonne.',
            'genere_par' => 'gynecologue', 'gynecologue_id' => $gyn->id,
        ]);
        Notification::create(['user_id' => $u_coumba->id, 'type' => 'rappel_rendez_vous', 'titre' => 'Suivi annuel dans 5 jours', 'corps' => 'Votre bilan post-ménopause annuel est dans 5 jours. Pensez à apporter vos résultats précédents.']);

        // ─────────────────────────────────────────────────────
        // M4 — Ndèye Thiaw · péri-ménopause sévère · 45 ans · Kaolack
        // Péri-ménopause précoce à 45 ans avec symptômes très invalidants.
        // Source : péri-ménopause précoce (<45 ans) = 5-10 % des femmes (POI si <40).
        //          Score Greene Climacteric Scale ≥ 15 = symptômes sévères.
        // ─────────────────────────────────────────────────────
        $u_ndeye = User::create([
            'role'              => 'patient',
            'nom'               => 'Thiaw',
            'prenom'            => 'Ndèye',
            'email'             => 'ndeye@test.sn',
            'password'          => Hash::make('password'),
            'telephone'         => '779876543',
            'date_naissance'    => '1981-05-12',
            'ville'             => 'Kaolack',
            'email_verified_at' => now(),
        ]);
        $f_ndeye = Femme::create([
            'user_id'              => $u_ndeye->id,
            'stade_menopause'      => 'perimenopause',
            'antecedents_medicaux' => 'Péri-ménopause précoce diagnostiquée à 45 ans. FSH élevée (42 mUI/mL). Traitée par THS (estradiol transdermique + progestérone micronisée). Dépression légère sous ISRS.',
        ]);
        foreach ([
            ['date' => '2026-06-01', 'note' => 'Nuit catastrophique : sueurs abondantes, palpitations. Très anxieuse.',
             'symptoms' => [['sueurs_nocturnes',5,'Drap trempé, changement de chemise 2 fois'],['bouffees_chaleur',4,'Palpitations associées'],['anxiete',5,'Crises d\'angoisse nocturnes'],['troubles_sommeil',5,'Réveillée 4 fois']]],
            ['date' => '2026-06-03', 'note' => 'Irritabilité extrême. Relations conjugales difficiles.',
             'symptoms' => [['irritabilite',5,'Explosions colériques'],['secheresse_vaginale',4,'Très gênante'],['fatigue',5,'Épuisement total'],['stress',4]]],
            ['date' => '2026-06-06', 'note' => 'Journée un peu meilleure. THS semble commencer à agir.',
             'symptoms' => [['bouffees_chaleur',3,'3 épisodes vs 8 habituellement'],['sueurs_nocturnes',3],['fatigue',3,'Un peu mieux'],['douleurs_articulaires',3,'Douleurs diffuses']]],
        ] as $entry) {
            $s = Symptome::create(['femme_id' => $f_ndeye->id, 'date_journal' => $entry['date'], 'note_generale' => $entry['note']]);
            foreach ($entry['symptoms'] as $sym) {
                EntreeSymptome::create(array_filter(['symptome_id' => $s->id, 'type_symptome' => $sym[0], 'intensite' => $sym[1], 'commentaire' => $sym[2] ?? null]));
            }
        }
        RendezVous::create([
            'femme_id' => $f_ndeye->id, 'gynecologue_id' => $gyn->id,
            'date_souhaitee' => now()->addDays(2)->toDateString(), 'heure_souhaitee' => '16:00',
            'motif' => 'Réévaluation THS après 3 mois + bilan psychologique',
            'statut' => 'accepte',
            'date_confirmee' => now()->addDays(2)->toDateString(), 'heure_confirmee' => '16:00',
            'note_gynecologue' => 'Prévoir bilan complet FSH/E2 + mammographie + frottis cervical.',
        ]);
        Recommandation::create([
            'femme_id' => $f_ndeye->id, 'type' => 'hygiene_vie', 'titre' => 'Gestion symptômes sévères',
            'corps'    => 'Techniques de respiration lente (6 cycles/min) réduisent les bouffées de chaleur de 40 % (études NAMS). Literie en coton. Chambre fraîche (18-20°C). Vêtements en couches superposées. Limiter café, alcool, plats épicés qui déclenchent les bouffées. THS évalué à J90 : résultats attendus favorables.',
            'genere_par' => 'gynecologue', 'gynecologue_id' => $gyn->id,
        ]);
        Notification::create(['user_id' => $u_ndeye->id, 'type' => 'statut_rendez_vous', 'titre' => 'RDV réévaluation THS dans 2 jours', 'corps' => 'Votre rendez-vous de suivi THS est après-demain à 16h. Apportez votre journal de symptômes.']);

        // ══════════════════════════════════════════════════════════════════════
        // 4bis. HOMME INSCRIT POUR S'INFORMER
        //   Les hommes n'ont pas de profil Femme : ils accèdent au contenu
        //   éducatif pour accompagner leur épouse / famille.
        // ══════════════════════════════════════════════════════════════════════
        $u_ibrahima = User::create([
            'role'              => 'patient',
            'genre'             => 'homme',
            'nom'               => 'Gueye',
            'prenom'            => 'Ibrahima',
            'email'             => 'ibrahima@test.sn',
            'password'          => Hash::make('password'),
            'telephone'         => '775550000',
            'date_naissance'    => '1988-09-03',
            'ville'             => 'Dakar',
            'email_verified_at' => now(),
        ]);
        Notification::create([
            'user_id' => $u_ibrahima->id, 'type' => 'general',
            'titre'   => 'Bienvenue Ibrahima 👋',
            'corps'   => 'Découvrez les contenus éducatifs pour accompagner vos proches pendant la grossesse et la ménopause.',
        ]);

        // ══════════════════════════════════════════════════════════════════════
        // 5. CONTENUS ÉDUCATIFS
        // ══════════════════════════════════════════════════════════════════════
        $cat1 = CategorieContenu::create(['nom' => 'Comprendre la ménopause',  'slug' => 'comprendre-menopause',  'icone' => 'book']);
        $cat2 = CategorieContenu::create(['nom' => 'Nutrition',                'slug' => 'nutrition',             'icone' => 'apple']);
        $cat3 = CategorieContenu::create(['nom' => 'Activité physique',        'slug' => 'activite-physique',     'icone' => 'running']);
        $cat4 = CategorieContenu::create(['nom' => 'Santé mentale',            'slug' => 'sante-mentale',         'icone' => 'brain']);
        $cat5 = CategorieContenu::create(['nom' => 'Grossesse & maternité',    'slug' => 'grossesse-maternite',   'icone' => 'baby']);

        Contenu::create(['categorie_id' => $cat1->id, 'auteur_id' => $admin->id, 'type' => 'article',
            'titre' => 'Qu\'est-ce que la ménopause ?', 'slug' => 'quest-ce-que-la-menopause',
            'corps' => 'La ménopause est un processus naturel qui marque la fin des cycles menstruels. Elle est confirmée après 12 mois consécutifs sans règles. L\'âge moyen au Sénégal est de 49,8 ans.',
            'langue' => 'fr', 'est_publie' => true, 'publie_le' => now()]);

        Contenu::create(['categorie_id' => $cat2->id, 'auteur_id' => $admin->id, 'type' => 'conseil',
            'titre' => '5 aliments contre les bouffées de chaleur', 'slug' => '5-aliments-bouffees',
            'corps' => 'Le soja, les graines de lin, les pois chiches, les lentilles et le thé vert sont riches en phytoestrogènes naturels qui réduisent la fréquence et l\'intensité des bouffées de chaleur.',
            'langue' => 'fr', 'est_publie' => true, 'publie_le' => now()]);

        Contenu::create(['categorie_id' => $cat5->id, 'auteur_id' => $admin->id, 'type' => 'article',
            'titre' => 'Alimentation pendant la grossesse au Sénégal', 'slug' => 'alimentation-grossesse-senegal',
            'corps' => 'Les besoins en fer augmentent de 50 % pendant la grossesse. Au Sénégal, privilégier le thiéboudienne avec feuilles de bissap, le mafé aux arachides (protéines), le yassa citron (vitamine C). Éviter alcool, café excessif, poissons crus.',
            'langue' => 'fr', 'est_publie' => true, 'publie_le' => now()]);

        Contenu::create(['categorie_id' => $cat4->id, 'auteur_id' => $admin->id, 'type' => 'article',
            'titre' => 'Gérer l\'anxiété pendant la grossesse et la ménopause', 'slug' => 'gerer-anxiete-grossesse-menopause',
            'corps' => 'Les bouleversements hormonaux de la grossesse comme de la ménopause peuvent amplifier l\'anxiété et les troubles du sommeil. Des gestes simples aident : respiration lente (6 cycles/minute), marche quotidienne, parole avec un proche ou un professionnel. En cas de tristesse persistante, de perte d\'intérêt ou d\'idées noires au-delà de deux semaines, il faut consulter : la dépression périnatale et le mal-être de la ménopause se soignent.',
            'langue' => 'fr', 'est_publie' => true, 'publie_le' => now()]);

        Video::create(['categorie_id' => $cat3->id, 'auteur_id' => $admin->id,
            'titre' => 'Yoga doux pour la ménopause', 'slug' => 'yoga-doux-menopause',
            'description' => 'Séance de yoga de 20 minutes adaptée aux femmes en péri et post-ménopause. Réduit le stress, améliore le sommeil et soulage les douleurs articulaires.',
            'url_video' => 'https://example.com/yoga-menopause.mp4', 'duree_secondes' => 1200,
            'langue' => 'fr', 'est_publie' => true, 'publie_le' => now()]);

        Video::create(['categorie_id' => $cat5->id, 'auteur_id' => $admin->id,
            'titre' => 'Yoga prénatal doux — 20 min', 'slug' => 'yoga-prenatal-doux',
            'description' => 'Séance de yoga spécialement conçue pour les femmes enceintes à partir du 2ème trimestre. Soulage les douleurs lombaires et prépare le corps à l\'accouchement.',
            'url_video' => 'https://example.com/yoga-prenatal.mp4', 'duree_secondes' => 1200,
            'langue' => 'fr', 'est_publie' => true, 'publie_le' => now()]);

        // ══════════════════════════════════════════════════════════════════════
        // 5bis. PROFESSIONNELS DE SANTÉ (intermédiation)
        // ══════════════════════════════════════════════════════════════════════
        \App\Models\ProfessionnelSante::create([
            'gynecologue_id'         => $gyn->id,
            'nom' => 'Sow', 'prenom' => 'Moussa', 'email' => 'pro.sow@yoonumakk.sn',
            'telephone' => '770001122', 'ville' => 'Dakar',
            'structure_sante' => 'Hôpital Principal de Dakar',
            'bio' => 'Gynécologue-obstétricien, 15 ans d\'expérience. Suivi grossesse à risque et ménopause.',
            'type_professionnel' => 'gynecologue', 'specialite' => 'Gynécologie-Obstétrique',
            'annees_experience' => 15, 'numero_ordre' => 'OM-2024-001',
            'langues_parles' => ['fr', 'wo'], 'profils_pris_en_charge' => ['grossesse', 'menopause'],
            'disponible_en_ligne' => true, 'disponible_en_cabinet' => true,
            'tarif_consultation' => 15000, 'horaires' => 'Lun-Ven 9h-17h',
            'adresse' => 'Avenue Nelson Mandela, Dakar',
            'nb_avis' => 24, 'note_moyenne' => 4.80, 'nb_consultations' => 320,
            'profil_verifie' => true, 'actif' => true,
        ]);
        \App\Models\ProfessionnelSante::create([
            'nom' => 'Cissé', 'prenom' => 'Aïda', 'email' => 'aida.cisse@yoonumakk.sn',
            'telephone' => '776002233', 'ville' => 'Dakar',
            'structure_sante' => 'Centre de Santé Philippe Senghor',
            'bio' => 'Sage-femme d\'État, accompagnement grossesse et préparation à l\'accouchement.',
            'type_professionnel' => 'sage_femme', 'specialite' => 'Suivi prénatal & accouchement',
            'annees_experience' => 10,
            'langues_parles' => ['fr', 'wo', 'pu'], 'profils_pris_en_charge' => ['grossesse'],
            'disponible_en_ligne' => true, 'disponible_en_cabinet' => true,
            'tarif_consultation' => 5000, 'horaires' => 'Lun-Sam 8h-18h',
            'nb_avis' => 41, 'note_moyenne' => 4.90, 'nb_consultations' => 510,
            'profil_verifie' => true, 'actif' => true,
        ]);
        \App\Models\ProfessionnelSante::create([
            'nom' => 'Diagne', 'prenom' => 'Oumy', 'email' => 'oumy.diagne@yoonumakk.sn',
            'telephone' => '775003344', 'ville' => 'Thiès',
            'structure_sante' => 'Cabinet NutriSanté Thiès',
            'bio' => 'Nutritionniste-diététicienne. Alimentation grossesse, diabète gestationnel, équilibre à la ménopause.',
            'type_professionnel' => 'nutritionniste', 'specialite' => 'Nutrition materno-infantile',
            'annees_experience' => 7,
            'langues_parles' => ['fr', 'wo'], 'profils_pris_en_charge' => ['grossesse', 'menopause'],
            'disponible_en_ligne' => true, 'disponible_en_cabinet' => true,
            'tarif_consultation' => 8000, 'horaires' => 'Mar-Sam 9h-16h',
            'nb_avis' => 17, 'note_moyenne' => 4.60, 'nb_consultations' => 150,
            'profil_verifie' => true, 'actif' => true,
        ]);
        \App\Models\ProfessionnelSante::create([
            'nom' => 'Kane', 'prenom' => 'Mariétou', 'email' => 'marietou.kane@yoonumakk.sn',
            'telephone' => '774004455', 'ville' => 'Dakar',
            'structure_sante' => 'Clinique du Cap',
            'bio' => 'Psychologue clinicienne spécialisée en périnatalité : dépression post-partum, anxiété de grossesse, vécu de la ménopause.',
            'type_professionnel' => 'psychologue_perinatal', 'specialite' => 'Psychologie périnatale',
            'annees_experience' => 9,
            'langues_parles' => ['fr'], 'profils_pris_en_charge' => ['grossesse', 'menopause'],
            'disponible_en_ligne' => true, 'disponible_en_cabinet' => false,
            'tarif_consultation' => 12000, 'horaires' => 'Lun-Ven 10h-19h',
            'nb_avis' => 9, 'note_moyenne' => 4.70, 'nb_consultations' => 85,
            'profil_verifie' => true, 'actif' => true,
        ]);
        \App\Models\ProfessionnelSante::create([
            'nom' => 'Badji', 'prenom' => 'Léna', 'email' => 'lena.badji@yoonumakk.sn',
            'telephone' => '773005566', 'ville' => 'Ziguinchor',
            'structure_sante' => 'Hôpital de la Paix',
            'bio' => 'Médecin généraliste, consultations femmes et familles. Suivi ménopause et maladies chroniques.',
            'type_professionnel' => 'medecin_generaliste', 'specialite' => 'Médecine de famille',
            'annees_experience' => 12,
            'langues_parles' => ['fr', 'wo'], 'profils_pris_en_charge' => ['menopause'],
            'disponible_en_ligne' => false, 'disponible_en_cabinet' => true,
            'tarif_consultation' => 6000, 'horaires' => 'Lun-Ven 8h-15h',
            'nb_avis' => 12, 'note_moyenne' => 4.40, 'nb_consultations' => 210,
            'profil_verifie' => false, 'actif' => true,
        ]);
        \App\Models\ProfessionnelSante::create([
            'nom' => 'Ndour', 'prenom' => 'Bineta', 'email' => 'bineta.ndour@yoonumakk.sn',
            'telephone' => '772006677', 'ville' => 'Saint-Louis',
            'structure_sante' => 'Studio Maman Zen',
            'bio' => 'Coach prénatale certifiée : yoga prénatal, respiration, préparation physique à l\'accouchement.',
            'type_professionnel' => 'coach_prenatal', 'specialite' => 'Yoga & préparation prénatale',
            'annees_experience' => 5,
            'langues_parles' => ['fr', 'wo'], 'profils_pris_en_charge' => ['grossesse'],
            'disponible_en_ligne' => true, 'disponible_en_cabinet' => true,
            'tarif_consultation' => 4000, 'horaires' => 'Lun-Sam 7h-12h',
            'nb_avis' => 28, 'note_moyenne' => 4.90, 'nb_consultations' => 240,
            'profil_verifie' => true, 'actif' => true,
        ]);

        // ══════════════════════════════════════════════════════════════════════
        // 6. NOTIFICATIONS GYNÉCOLOGUE
        // ══════════════════════════════════════════════════════════════════════
        Notification::create(['gynecologue_id' => $gyn->id, 'type' => 'rappel_rendez_vous',
            'titre' => 'Nouveau RDV en attente — Awa Fall', 'corps' => 'Mme Fall demande un RDV de suivi péri-ménopause dans 10 jours.']);
        Notification::create(['gynecologue_id' => $gyn->id, 'type' => 'rappel_rendez_vous',
            'titre' => 'URGENT — Surveillance TA Mariama Diop (36 SA)', 'corps' => 'Mme Diop (36 SA, risque pré-éclampsie) a un RDV monitoring dans 3 jours. À surveiller en priorité.']);
    }

    /**
     * Vide les tables fournies (en ignorant les contraintes de clés étrangères)
     * afin que le seeder puisse être relancé sans erreur de doublon.
     * Les tables absentes sont ignorées silencieusement.
     */
    private function truncateTables(array $tables): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();
    }
}
