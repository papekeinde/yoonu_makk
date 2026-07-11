<?php

namespace Database\Seeders;

use App\Models\Administrateur;
use App\Models\CategorieContenu;
use App\Models\Contenu;
use App\Models\Femme;
use App\Models\Gynecologue;
use App\Models\User;
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

        // 2. GYNÉCOLOGUE
        Gynecologue::updateOrCreate(
            ['email' => 'dr.sow@test.sn'],
            [
                'nom'                 => 'Sow',
                'prenom'              => 'Moussa',
                'password'            => Hash::make('password'),
                'telephone'           => '770001122',
                'numero_ordre'        => 'OM-2024-001',
                'specialite'          => 'Gynécologie-Obstétrique',
                'annees_experience'   => 15,
                'structure_sante'     => 'Hôpital Principal de Dakar',
                'ville'               => 'Dakar',
                'bio'                 => 'Spécialiste en santé de la femme.',
                'email_verified_at'   => now(),
                'is_active'           => true,
            ]
        );

        // 3. PATIENTE TEST
        $u_aissatou = User::updateOrCreate(
            ['email' => 'aissatou@test.sn'],
            [
                'role'              => 'patient',
                'nom'               => 'Bâ',
                'prenom'            => 'Aissatou',
                'password'          => Hash::make('password'),
                'telephone'         => '778889900',
                'date_naissance'    => '1998-11-25',
                'ville'             => 'Saint-Louis',
                'email_verified_at' => now(),
                'genre'             => 'femme',
            ]
        );
        Femme::updateOrCreate(['user_id' => $u_aissatou->id], ['type_profil' => 'grossesse']);

        // 4. CONTENUS
        $cat1 = CategorieContenu::updateOrCreate(['slug' => 'comprendre-menopause'], ['nom' => 'Comprendre la ménopause', 'icone' => 'book']);
        Contenu::updateOrCreate(
            ['slug' => 'quest-ce-que-la-menopause'],
            [
                'categorie_id' => $cat1->id,
                'auteur_id' => $admin->id,
                'type' => 'article',
                'titre' => 'Qu\'est-ce que la ménopause ?',
                'corps' => 'La ménopause est un processus naturel qui marque la fin des cycles menstruels.',
                'langue' => 'fr',
                'est_publie' => true,
                'publie_le' => now()
            ]
        );
    }
}
