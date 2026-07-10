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
        Schema::create('gynecologue_patientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gynecologue_id')->constrained('gynecologues')->cascadeOnDelete();
            $table->foreignId('femme_id')->constrained('femmes')->cascadeOnDelete();
            $table->date('date_prise_en_charge')->default(now());
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['gynecologue_id', 'femme_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gynecologue_patientes');
    }
};
