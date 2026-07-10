<?php

namespace Tests\Feature;

use App\Enums\StatutDemandeAdhesion;
use App\Models\DemandeAdhesion;
use App\Models\DemandeIntermediation;
use App\Models\Gynecologue;
use App\Models\ProfessionnelSante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FluxCompletTest extends TestCase
{
    use RefreshDatabase;

    public function test_inscription_et_connexion_patient(): void
    {
        $response = $this->postJson('/api/auth/inscription', [
            'nom'                   => 'Diop',
            'prenom'                => 'Awa',
            'email'                 => 'awa@test.sn',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'genre'                 => 'femme',
            'date_naissance'        => '1995-01-01',
            'type_profil'           => 'menopause',
            'telephone'             => '770000000',
            'ville'                 => 'Dakar',
        ]);

        $response->assertCreated()->assertJsonStructure(['user', 'token']);

        $login = $this->postJson('/api/auth/connexion', [
            'email'    => 'awa@test.sn',
            'password' => 'password123',
        ]);

        $login->assertOk()->assertJsonStructure(['token']);
    }

    public function test_connexion_patient_par_telephone_avec_ou_sans_indicatif(): void
    {
        User::factory()->create([
            'email'    => 'phone@test.sn',
            'telephone'=> '770000000',
            'password' => 'password123',
            'role'     => 'patient',
        ]);

        foreach (['770000000', '+221770000000', '221770000000', '+1770000000'] as $identifiant) {
            $this->postJson('/api/auth/connexion', [
                'identifiant' => $identifiant,
                'password'    => 'password123',
            ])->assertOk()->assertJsonStructure(['token']);
        }
    }

    public function test_connexion_gynecologue_par_telephone_avec_ou_sans_indicatif(): void
    {
        Gynecologue::create([
            'demande_adhesion_id' => null,
            'nom'                 => 'Seck',
            'prenom'              => 'Amy',
            'email'               => 'amy@test.sn',
            'password'            => 'password123',
            'telephone'           => '771234567',
            'numero_ordre'        => 'GYN-100',
            'specialite'          => 'Gynécologie',
            'annees_experience'   => 7,
            'structure_sante'     => 'Clinique B',
            'ville'               => 'Dakar',
            'is_active'           => true,
        ]);

        foreach (['771234567', '+221771234567', '221771234567', '+1771234567'] as $identifiant) {
            $this->postJson('/api/gynecologue/auth/connexion', [
                'identifiant' => $identifiant,
                'password'    => 'password123',
            ])->assertOk()->assertJsonStructure(['token']);
        }
    }

    public function test_inscription_normalise_le_telephone_et_evite_les_doublons(): void
    {
        $first = $this->postJson('/api/auth/inscription', [
            'nom'                   => 'Fall',
            'prenom'                => 'Kadiatou',
            'email'                 => 'kadiatou1@test.sn',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'genre'                 => 'femme',
            'date_naissance'        => '1994-02-02',
            'type_profil'           => 'menopause',
            'telephone'             => '77 999 88 77',
        ]);

        $first->assertCreated();
        $this->assertDatabaseHas('users', [
            'email'     => 'kadiatou1@test.sn',
            'telephone' => '+221779998877',
        ]);

        $second = $this->postJson('/api/auth/inscription', [
            'nom'                   => 'Fall',
            'prenom'                => 'Kadiatou2',
            'email'                 => 'kadiatou2@test.sn',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'genre'                 => 'femme',
            'date_naissance'        => '1994-02-02',
            'type_profil'           => 'menopause',
            'telephone'             => '+221779998877',
        ]);

        $second->assertStatus(422);
    }

    public function test_update_profil_normalise_le_telephone_et_bloque_les_doublons(): void
    {
        $first = User::factory()->create([
            'role'              => 'patient',
            'telephone'         => '+221781112233',
            'email_verified_at' => now(),
        ]);

        $second = User::factory()->create([
            'role'              => 'patient',
            'telephone'         => null,
            'email_verified_at' => now(),
        ]);

        $updateOk = $this->actingAs($second, 'sanctum')->putJson('/api/patient/profil', [
            'telephone' => '77 123 45 67',
        ]);

        $updateOk->assertOk();
        $this->assertDatabaseHas('users', [
            'id'        => $second->id,
            'telephone' => '+221771234567',
        ]);

        $updateDup = $this->actingAs($second->fresh(), 'sanctum')->putJson('/api/patient/profil', [
            'telephone' => '78 111 22 33',
        ]);

        $updateDup->assertStatus(422);
        $this->assertDatabaseHas('users', [
            'id'        => $second->id,
            'telephone' => '+221771234567',
        ]);
        $this->assertDatabaseHas('users', [
            'id'        => $first->id,
            'telephone' => '+221781112233',
        ]);
    }

    public function test_connexion_avec_mauvais_mot_de_passe_echoue(): void
    {
        User::factory()->create([
            'email'    => 'x@test.sn',
            'password' => 'password123',
            'role'     => 'patient',
        ]);

        $this->postJson('/api/auth/connexion', [
            'email'    => 'x@test.sn',
            'password' => 'mauvais',
        ])->assertStatus(401);
    }

    public function test_demande_adhesion_publique_avec_fichiers(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/demandes-adhesion', [
            'nom'                => 'Sow',
            'prenom'             => 'Fatou',
            'email'              => 'fatou.sow@test.sn',
            'telephone'          => '771111111',
            'numero_ordre'       => 'GYN-001',
            'specialite'         => 'Gynécologie',
            'annees_experience'  => 10,
            'structure_sante'    => 'Hôpital Principal',
            'ville'              => 'Dakar',
            'diplome'            => UploadedFile::fake()->create('diplome.pdf', 100, 'application/pdf'),
            'justificatif'       => UploadedFile::fake()->create('ordre.pdf', 100, 'application/pdf'),
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('demandes_adhesion', [
            'email'  => 'fatou.sow@test.sn',
            'statut' => StatutDemandeAdhesion::EnAttente->value,
        ]);
    }

    public function test_admin_approuve_demande_et_email_envoye(): void
    {
        Mail::fake();
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);

        $demande = DemandeAdhesion::create([
            'nom'                  => 'Ba',
            'prenom'               => 'Aminata',
            'email'                => 'aminata@test.sn',
            'telephone'            => '772222222',
            'numero_ordre'         => 'GYN-002',
            'specialite'           => 'Gynécologie',
            'annees_experience'    => 8,
            'structure_sante'      => 'Clinique Pasteur',
            'ville'                => 'Thiès',
            'chemin_diplome'       => 'demandes/diplomes/a.pdf',
            'chemin_justificatif'  => 'demandes/justificatifs/a.pdf',
            'statut'               => StatutDemandeAdhesion::EnAttente,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/demandes-adhesion/{$demande->id}/approuver");

        $response->assertOk();
        $this->assertDatabaseHas('gynecologues', ['email' => 'aminata@test.sn']);
        Mail::assertSent(\App\Mail\CompteGynecologueCreeMail::class);
    }

    public function test_gynecologue_change_son_mot_de_passe(): void
    {
        $gyneco = Gynecologue::create([
            'demande_adhesion_id' => null,
            'nom'                 => 'Ndiaye',
            'prenom'              => 'Mariama',
            'email'               => 'mariama@test.sn',
            'password'            => 'ancien123',
            'telephone'           => '773333333',
            'numero_ordre'        => 'GYN-003',
            'specialite'          => 'Gynécologie',
            'annees_experience'   => 5,
            'structure_sante'     => 'Hôpital A',
            'ville'               => 'Dakar',
            'is_active'           => true,
        ]);

        $response = $this->actingAs($gyneco, 'gynecologue')->putJson('/api/gynecologue/mot-de-passe', [
            'mot_de_passe_actuel'             => 'ancien123',
            'nouveau_mot_de_passe'            => 'nouveau1234',
            'nouveau_mot_de_passe_confirmation' => 'nouveau1234',
        ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('nouveau1234', $gyneco->fresh()->password));
    }

    public function test_patient_acces_protege_sans_token(): void
    {
        $this->getJson('/api/patient/profil')->assertStatus(401);
    }

    public function test_admin_seul_peut_lister_demandes(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $this->actingAs($patient, 'sanctum')
            ->getJson('/api/admin/demandes-adhesion')
            ->assertStatus(403);
    }

    /**
     * Les routes pro d'intermédiation utilisent le guard gynecologue.
     * Un token patient (guard sanctum) doit recevoir 401.
     */
    public function test_patient_ne_peut_pas_acceder_aux_routes_pro_intermediation(): void
    {
        $patient = User::factory()->create([
            'role'              => 'patient',
            'email_verified_at' => now(),
        ]);

        // GET /api/intermediation/pro/demandes → 401 avec un token patient
        $this->actingAs($patient, 'sanctum')
            ->getJson('/api/intermediation/pro/demandes')
            ->assertStatus(401);

        // PUT /api/intermediation/pro/demandes/1 → 401 (guard check avant model lookup)
        $this->actingAs($patient, 'sanctum')
            ->putJson('/api/intermediation/pro/demandes/1', [
                'statut' => 'accepte',
            ])
            ->assertStatus(401);
    }
}
