<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gynecologues', function (Blueprint $table) {
            // Tarif de consultation en FCFA (nullable : non renseigné tant que le gynéco ne l'a pas défini)
            $table->unsignedInteger('tarif_consultation')->nullable()->after('ville');
        });
    }

    public function down(): void
    {
        Schema::table('gynecologues', function (Blueprint $table) {
            $table->dropColumn('tarif_consultation');
        });
    }
};
