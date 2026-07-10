<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages_chatbot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('session_id')->index();
            $table->enum('role', ['utilisateur', 'assistant']);
            $table->text('message');
            $table->enum('langue', ['fr', 'wo'])->default('fr');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages_chatbot');
    }
};
