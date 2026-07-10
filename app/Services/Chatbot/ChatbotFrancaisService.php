<?php

namespace App\Services\Chatbot;

class ChatbotFrancaisService implements ChatbotServiceInterface
{
    public function repondre(string $message, string $langue = 'fr', array $contexte = []): string
    {
        // TODO: Intégrer le LLM pour le français (OpenAI, Claude, etc.)
        return "Je suis le chatbot YOONU MAKK. Cette fonctionnalité est en cours de développement.";
    }
}
