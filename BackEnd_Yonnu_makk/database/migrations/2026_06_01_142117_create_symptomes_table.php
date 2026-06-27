<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('symptomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('femme_id')->constrained()->cascadeOnDelete();
            $table->date('date_journal');
            $table->text('note_generale')->nullable();
            $table->timestamps();

            $table->unique(['femme_id', 'date_journal']);
        });

        Schema::create('entrees_symptomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('symptome_id')->constrained('symptomes')->cascadeOnDelete();
            $table->enum('type_symptome', [
                'bouffees_chaleur',
                'sueurs_nocturnes',
                'troubles_sommeil',
                'fatigue',
                'stress',
                'anxiete',
                'irritabilite',
                'douleurs_articulaires',
                'secheresse_vaginale',
                'troubles_urinaires',
                'nausee_vomissement',
                'brulure_estomac',
                'douleurs_dorsales',
                'oedemes_jambes',
                'constipation_grossesse',
                'vertiges_grossesse',
                'contractions_benignes',
                'mouvements_reduits',
                'saignement_legers',
                'perte_eaux',
            ]);
            $table->unsignedTinyInteger('intensite')->comment('1 = faible, 5 = très sévère');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrees_symptomes');
        Schema::dropIfExists('symptomes');
    }
};
