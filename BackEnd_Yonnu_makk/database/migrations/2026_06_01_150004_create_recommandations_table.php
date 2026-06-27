<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommandations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('femme_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['nutrition', 'activite_physique', 'hygiene_vie', 'consultation', 'prenatal', 'allaitement', 'preparation_accouchement']);
            $table->string('titre');
            $table->text('corps');
            $table->enum('genere_par', ['systeme', 'gynecologue'])->default('systeme');
            $table->foreignId('gynecologue_id')->nullable()->constrained('gynecologues')->nullOnDelete();
            $table->boolean('est_lu')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommandations');
    }
};
