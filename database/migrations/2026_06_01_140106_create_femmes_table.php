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
        Schema::create('femmes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type_profil', ['menopause', 'grossesse'])->default('menopause');
            $table->date('date_debut_menopause')->nullable();
            $table->enum('stade_menopause', ['perimenopause', 'menopause', 'postmenopause'])->nullable();
            $table->text('antecedents_medicaux')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('femmes');
    }
};
