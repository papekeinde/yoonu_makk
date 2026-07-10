<?php

namespace App\Services\Chatbot;

class ChatbotWolofService implements ChatbotServiceInterface
{
    public function repondre(string $message, string $langue = 'wo', array $contexte = []): string
    {
        // TODO: Brancher l'API wolof fournie par le professeur
        return "Chatbot bi dafa ngi ci anam yi. Jëkkërlul ci ginnaaw.";
    }
}
