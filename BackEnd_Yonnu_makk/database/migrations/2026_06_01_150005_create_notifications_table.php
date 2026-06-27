<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('gynecologue_id')->nullable()->constrained('gynecologues')->nullOnDelete();
            $table->enum('type', [
                'rappel_rendez_vous',
                'nouveau_contenu',
                'statut_rendez_vous',
                'general',
                'rappel_suivi_grossesse',
                'alerte_grossesse',
                'felicitations_grossesse',
            ]);
            $table->string('titre');
            $table->text('corps');
            $table->json('donnees')->nullable();
            $table->boolean('est_lu')->default(false);
            $table->timestamp('lu_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
