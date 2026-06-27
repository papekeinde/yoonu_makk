<?php

namespace App\Services\Chatbot;

use Illuminate\Http\UploadedFile;

class ChatbotManager
{
    public function __construct(
        private RemoteChatbotClient $remote,
        private KnowledgeBase $kb,
    ) {}

    /** Réponse texte simple (compatibilité). */
    public function repondre(string $message, string $langue = 'fr', array $contexte = []): string
    {
        return $this->respond($message, $langue)['response'];
    }

    /**
     * Réponse complète à une question texte.
     * @return array{response:string, audio_url:?string, source:string}
     */
    public function respond(string $message, string $langue = 'fr', bool $audio = false): array
    {
        $remote = $this->remote->queryText($message, $langue, $audio);
        if ($remote && ! empty($remote['response'])) {
            return [
                'response'  => $remote['response'],
                'audio_url' => $remote['audio_url'] ?? null,
                'source'    => 'remote',
            ];
        }

        $local = $this->kb->search($message, $langue);
        if ($local) {
            return ['response' => $local, 'audio_url' => null, 'source' => 'kb'];
        }

        return [
            'response'  => $this->messageDefaut($langue),
            'audio_url' => null,
            'source'    => 'none',
        ];
    }

    /**
     * Traite un message vocal : transcription + réponse (+ audio).
     * @return array{response:string, transcription:?string, audio_url:?string, source:string}|null
     */
    public function respondAudio(UploadedFile $audio, string $langue = 'fr'): ?array
    {
        $remote = $this->remote->queryAudio($audio, $langue);
        if ($remote && ! empty($remote['response'])) {
            return [
                'response'      => $remote['response'],
                'transcription' => $remote['transcription'] ?? null,
                'audio_url'     => $remote['audio_url'] ?? null,
                'source'        => 'remote',
            ];
        }

        // Pas d'ASR en local : on ne peut pas traiter la voix sans l'API externe.
        return null;
    }

    private function messageDefaut(string $langue): string
    {
        return $langue === 'wo'
            ? "Baal ma, xamuma tontu ci sa laaj bi for léegi. Laajal ko ci sa wisit bu topp."
            : "Désolée, je n'ai pas encore la réponse à cette question. N'hésitez pas à la poser lors de votre prochaine consultation prénatale.";
    }
}
