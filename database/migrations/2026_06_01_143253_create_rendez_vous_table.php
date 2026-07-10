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
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('femme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gynecologue_id')->constrained('gynecologues')->cascadeOnDelete();
            $table->date('date_souhaitee');
            $table->time('heure_souhaitee')->nullable();
            $table->text('motif')->nullable();
            $table->enum('contexte', ['menopause', 'grossesse'])->default('menopause');
            $table->enum('statut', [
                'en_attente',
                'accepte',
                'refuse',
                'termine',
                'annule',
            ])->default('en_attente');
            $table->date('date_confirmee')->nullable();
            $table->time('heure_confirmee')->nullable();
            $table->text('note_gynecologue')->nullable();
            $table->enum('annule_par', ['patiente', 'gynecologue'])->nullable();
            $table->text('raison_annulation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};
