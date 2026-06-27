<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_bebe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grossesse_id')->constrained('grossesses')->cascadeOnDelete();
            $table->dateTime('date_heure');
            $table->unsignedTinyInteger('nombre_mouvements')->default(1);
            $table->enum('intensite', ['leger', 'modere', 'fort'])->default('modere');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_bebe');
    }
};
