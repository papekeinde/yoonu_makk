<?php

namespace Database\Seeders;

use App\Models\Administrateur;
use App\Models\CategorieContenu;
use App\Models\Contenu;
use App\Models\Femme;
use App\Models\Gynecologue;
use App\Models\User;
use App\Models\Video;
use App\Models\ProfessionnelSante;
use App\Models\Notification;
use App\Models\RendezVous;
use App\Models\Symptome;
use App\Models\EntreeSymptome;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMIN
        $admin = User::updateOrCreate(
            ['email' => 'admin@yoonumakk.sn'],
            [
                'role'              => 'admin',
                'nom'               => 'Diallo',
                'prenom'            => 'Aminata',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'genre'             => 'femme',
                'telephone'         => '770000000',
            ]
        );
        Administrateur::updateOrCreate(['user_id' => $admin->id]);

        // 2. GYNÉCOLOGUES
        $gyns = [
            [
                'nom' => 'Sow', 'prenom' => 'Moussa', 'email' => 'dr.sow@test.sn',
                'tel' => '770001122', 'ordre' => 'OM-2024-001', 'ville' => 'Dakar',
                'structure' => 'Hôpital Principal de Dakar'
            ],
            [
                'nom' => 'Fall', 'prenom' => 'Rokhaya', 'email' => 'dr.fall@test.sn',
                'tel' => '770001133', 'ordre' => 'OM-2024-002', 'ville' => 'Saint-Louis',
                'structure' => 'Hôpital Régional de Saint-Louis'
            ],
            [
                'nom' => 'Ndiaye', 'prenom' => 'Fatou', 'email' => 'dr.ndiaye@test.sn',
                'tel' => '770001144', 'ordre' => 'OM-2024-003', 'ville' => 'Thiès',
                'structure' => 'Centre de Santé Ahmadou Malick Ndiaye'
            ],
        ];

        foreach ($gyns as $g) {
            $gynecologue = Gynecologue::updateOrCreate(
                ['email' => $g['email']],
                [
                    'nom'                 => $g['nom'],
                    'prenom'              => $g['prenom'],
                    'password'            => Hash::make('password'),
                    'telephone'           => $g['tel'],
                    'numero_ordre'        => $g['ordre'],
                    'specialite'          => 'Gynécologie-Obstétrique',
                    'annees_experience'   => rand(5, 25),
                    'structure_sante'     => $g['structure'],
                    'ville'               => $g['ville'],
                    'bio'                 => "Spécialiste passionné(e) par la santé de la femme au Sénégal.",
                    'email_verified_at'   => now(),
                    'is_active'           => true,
                ]
            );

            ProfessionnelSante::updateOrCreate(
                ['email' => $g['email']],
                [
                    'gynecologue_id' => $gynecologue->id,
                    'nom' => $g['nom'], 'prenom' => $g['prenom'],
                    'telephone' => $g['tel'], 'ville' => $g['ville'],
                    'structure_sante' => $g['structure'],
                    'type_professionnel' => 'gynecologue',
                    'specialite' => 'Gynécologie-Obstétrique',
                    'actif' => true,
                    'profil_verifie' => true,
                    'note_moyenne' => 4.5 + (rand(0, 5) / 10),
                ]
            );
        }

        // 3. PATIENTES
        $patients = [
            ['nom' => 'Bâ', 'prenom' => 'Aissatou', 'email' => 'aissatou@test.sn', 'profil' => 'grossesse', 'ville' => 'Dakar'],
            ['nom' => 'Diop', 'prenom' => 'Khady', 'email' => 'khady@test.sn', 'profil' => 'menopause', 'ville' => 'Thiès'],
            ['nom' => 'Faye', 'prenom' => 'Mariama', 'email' => 'mariama@test.sn', 'profil' => 'grossesse', 'ville' => 'Mbour'],
        ];

        foreach ($patients as $p) {
            $user = User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'role'              => 'patient',
                    'nom'               => $p['nom'],
                    'prenom'            => $p['prenom'],
                    'password'          => Hash::make('password'),
                    'telephone'         => '77888' . rand(1000, 9999),
                    'date_naissance'    => now()->subYears(rand(20, 50))->toDateString(),
                    'ville'             => $p['ville'],
                    'email_verified_at' => now(),
                    'genre'             => 'femme',
                ]
            );
            $femme = Femme::updateOrCreate(['user_id' => $user->id], ['type_profil' => $p['profil']]);

            // Notifications
            Notification::create([
                'user_id' => $user->id,
                'type' => 'general',
                'titre' => 'Bienvenue sur Yoonu Jigeen',
                'corps' => "Bonjour {$p['prenom']}, votre compte est prêt.",
            ]);

            // Fake Symptoms
            if ($p['profil'] == 'menopause') {
                $s = Symptome::create(['femme_id' => $femme->id, 'date_journal' => now()->toDateString(), 'note_generale' => 'Bouffées de chaleur ce matin.']);
                EntreeSymptome::create(['symptome_id' => $s->id, 'type_symptome' => 'bouffees_chaleur', 'intensite' => 4]);
            }
        }

        // 4. CONTENUS
        $cat1 = CategorieContenu::updateOrCreate(['slug' => 'comprendre-menopause'], ['nom' => 'Ménopause', 'icone' => 'book']);
        $cat2 = CategorieContenu::updateOrCreate(['slug' => 'suivi-grossesse'], ['nom' => 'Grossesse', 'icone' => 'baby']);

        Contenu::updateOrCreate(['slug' => 'quest-ce-que-la-menopause'], [
            'categorie_id' => $cat1->id, 'auteur_id' => $admin->id, 'type' => 'article',
            'titre' => 'Comprendre la ménopause', 'corps' => 'La ménopause est une étape naturelle de la vie...',
            'langue' => 'fr', 'est_publie' => true, 'publie_le' => now()
        ]);

        Contenu::updateOrCreate(['slug' => 'alimentation-grossesse'], [
            'categorie_id' => $cat2->id, 'auteur_id' => $admin->id, 'type' => 'article',
            'titre' => 'Bien manger pendant sa grossesse', 'corps' => 'Une alimentation équilibrée est essentielle...',
            'langue' => 'fr', 'est_publie' => true, 'publie_le' => now()
        ]);

        Video::updateOrCreate(['slug' => 'exercices-prenataux'], [
            'categorie_id' => $cat2->id, 'auteur_id' => $admin->id,
            'titre' => 'Exercices doux', 'description' => 'Quelques mouvements pour rester en forme.',
            'url_video' => 'https://example.com/video.mp4', 'langue' => 'fr', 'est_publie' => true
        ]);
    }
}
