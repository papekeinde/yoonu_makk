<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rend les champs chemin_diplome et chemin_justificatif nullables
 * pour permettre la soumission d'une demande d'adhésion sans document
 * (les documents peuvent être fournis ultérieurement).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes_adhesion', function (Blueprint $table) {
            $table->string('chemin_diplome')->nullable()->change();
            $table->string('chemin_justificatif')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('demandes_adhesion', function (Blueprint $table) {
            $table->string('chemin_diplome')->nullable(false)->change();
            $table->string('chemin_justificatif')->nullable(false)->change();
        });
    }
};
