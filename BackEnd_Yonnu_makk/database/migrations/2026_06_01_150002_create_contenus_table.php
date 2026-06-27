<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contenus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categories_contenus')->cascadeOnDelete();
            $table->foreignId('auteur_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['article', 'conseil', 'faq']);
            $table->string('titre');
            $table->string('slug')->unique();
            $table->longText('corps');
            $table->string('image_couverture')->nullable();
            $table->enum('langue', ['fr', 'wo'])->default('fr');
            $table->boolean('est_publie')->default(false);
            $table->timestamp('publie_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contenus');
    }
};
