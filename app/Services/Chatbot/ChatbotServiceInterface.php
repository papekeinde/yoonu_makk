<?php

namespace App\Services\Chatbot;

interface ChatbotServiceInterface
{
    public function repondre(string $message, string $langue = 'fr', array $contexte = []): string;
}
