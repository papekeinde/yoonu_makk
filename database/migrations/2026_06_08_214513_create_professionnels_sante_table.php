<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Professionnels de santé participant à la plateforme d'intermédiation.
 * Inclut gynécologues déjà inscrits, sages-femmes, nutritionnistes,
 * psychologues périnataux, kinésithérapeutes, etc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professionnels_sante', function (Blueprint $table) {
            $table->id();

            // Lien optionnel vers un gynécologue déjà existant
            $table->foreignId('gynecologue_id')
                  ->nullable()->constrained('gynecologues')->nullOnDelete();

            // Identité
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone', 20)->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('structure_sante')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();

            // Professionnel
            $table->enum('type_professionnel', [
                'gynecologue','sage_femme','nutritionniste',
                'psychologue_perinatal','kinesitherapeute',
                'infirmiere','medecin_generaliste','coach_prenatal','autre',
            ])->default('gynecologue');

            $table->string('specialite')->nullable();
            $table->unsignedTinyInteger('annees_experience')->default(0);
            $table->string('numero_ordre', 50)->nullable();
            $table->json('langues_parles')->nullable();          // ['fr','wo','pu']
            $table->json('profils_pris_en_charge')->nullable();  // ['grossesse','menopause']

            // Disponibilité & tarifs
            $table->boolean('disponible_en_ligne')->default(false);
            $table->boolean('disponible_en_cabinet')->default(true);
            $table->decimal('tarif_consultation', 8, 0)->nullable(); // FCFA
            $table->json('jours_disponibles')->nullable();
            $table->string('horaires', 100)->nullable();

            // Géolocalisation
            $table->string('adresse')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Stats dénormalisées
            $table->unsignedSmallInteger('nb_avis')->default(0);
            $table->decimal('note_moyenne', 3, 2)->default(0.00);
            $table->unsignedInteger('nb_consultations')->default(0);

            $table->boolean('profil_verifie')->default(false);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('professionnels_sante'); }
};
