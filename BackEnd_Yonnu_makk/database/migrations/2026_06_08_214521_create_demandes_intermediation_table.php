<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Demandes d'intermédiation : une femme contacte un professionnel de santé.
 * Suit le cycle complet : en_attente → accepte | refuse → termine | annule
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_intermediation', function (Blueprint $table) {
            $table->id();

            $table->foreignId('femme_id')
                  ->constrained('femmes')->cascadeOnDelete();
            $table->foreignId('professionnel_id')
                  ->constrained('professionnels_sante')->cascadeOnDelete();

            // Nature de la demande
            $table->enum('type_demande', [
                'consultation_en_ligne',
                'consultation_cabinet',
                'suivi_grossesse',
                'suivi_menopause',
                'urgence',
                'deuxieme_avis',
                'bilan_nutritionnel',
                'soutien_psychologique',
            ])->default('consultation_cabinet');

            $table->text('message')->nullable();   // message initial de la femme
            $table->string('motif', 255)->nullable();

            // Planification
            $table->date('date_souhaitee')->nullable();
            $table->time('heure_souhaitee')->nullable();
            $table->date('date_confirmee')->nullable();
            $table->time('heure_confirmee')->nullable();

            // Statut
            $table->enum('statut', [
                'en_attente','accepte','refuse','termine','annule',
            ])->default('en_attente');

            $table->text('reponse_professionnel')->nullable();
            $table->text('note_professionnel')->nullable();
            $table->text('compte_rendu')->nullable();   // CR après consultation

            // Suivi
            $table->boolean('urgente')->default(false);
            $table->timestamp('vue_par_professionnel_le')->nullable();
            $table->timestamp('traitee_le')->nullable();

            $table->timestamps();

            $table->index(['femme_id', 'statut']);
            $table->index(['professionnel_id', 'statut']);
        });
    }

    public function down(): void { Schema::dropIfExists('demandes_intermediation'); }
};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('demandes_intermediation', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_intermediation');
    }
};
