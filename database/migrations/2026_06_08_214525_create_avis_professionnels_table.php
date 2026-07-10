<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Avis/notes laissés par les femmes sur les professionnels après consultation. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avis_professionnels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('femme_id')
                  ->constrained('femmes')->cascadeOnDelete();
            $table->foreignId('professionnel_id')
                  ->constrained('professionnels_sante')->cascadeOnDelete();
            $table->foreignId('demande_id')
                  ->nullable()->constrained('demandes_intermediation')->nullOnDelete();

            $table->unsignedTinyInteger('note');      // 1 à 5 étoiles
            $table->text('commentaire')->nullable();
            $table->boolean('recommande')->default(true);
            $table->boolean('approuve')->default(false); // modération avant affichage

            $table->timestamps();

            // Une femme = un avis par professionnel
            $table->unique(['femme_id', 'professionnel_id']);
        });
    }

    public function down(): void { Schema::dropIfExists('avis_professionnels'); }
};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('avis_professionnels', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avis_professionnels');
    }
};
