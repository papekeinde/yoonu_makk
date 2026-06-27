<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grossesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('femme_id')->constrained()->cascadeOnDelete();
            $table->date('date_debut_grossesse');
            $table->date('date_accouchement_prevue');
            $table->enum('groupe_sanguin', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->unsignedTinyInteger('nombre_grossesses_anterieures')->default(0);
            $table->unsignedTinyInteger('nombre_accouchements_anterieurs')->default(0);
            $table->text('antecedents_obstetricaux')->nullable();
            $table->boolean('grossesse_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grossesses');
    }
};
