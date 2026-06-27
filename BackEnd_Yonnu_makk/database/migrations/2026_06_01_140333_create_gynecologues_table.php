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
        Schema::create('gynecologues', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('telephone', 20);
            $table->string('numero_ordre')->unique();
            $table->string('specialite');
            $table->unsignedSmallInteger('annees_experience')->default(0);
            $table->string('structure_sante');
            $table->string('ville', 100);
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->foreignId('demande_adhesion_id')->nullable()->constrained('demandes_adhesion')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gynecologues');
    }
};
