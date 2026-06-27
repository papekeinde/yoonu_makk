<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suivis_grossesse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grossesse_id')->constrained('grossesses')->cascadeOnDelete();
            $table->unsignedTinyInteger('semaines_amenorrhee');
            $table->decimal('poids_kg', 5, 2)->nullable();
            $table->unsignedSmallInteger('tension_systolique')->nullable();
            $table->unsignedSmallInteger('tension_diastolique')->nullable();
            $table->decimal('glycemie', 4, 2)->nullable();
            $table->text('notes')->nullable();
            $table->date('date_saisie');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suivis_grossesse');
    }
};
