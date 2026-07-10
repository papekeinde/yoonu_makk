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

        $local = $this->kb->searchFull($message, $langue);
        if ($local) {
            return [
                'response'  => $local['response'],
                // L'audio pré-enregistré est uniquement renvoyé en mode wolof.
                // En français le client utilise le TTS local (meilleure qualité).
                'audio_url' => $langue === 'wo' ? $this->resolvePrerecordedAudio($local['id']) : null,
                'source'    => 'kb',
            ];
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

    /**
     * Retourne l'URL publique du fichier audio pré-enregistré pour une question
     * donnée (ex : cpn_001.mp3), ou null si le fichier n'existe pas encore.
     */
    private function resolvePrerecordedAudio(string $questionId): ?string
    {
        if ($questionId === '') {
            return null;
        }

        // Seuls les caractères alphanumériques et tirets-bas sont acceptés
        // pour éviter toute traversée de répertoire.
        if (! preg_match('/^[a-z0-9_]+$/i', $questionId)) {
            return null;
        }

        // Cherche le fichier dans les formats courants (ordre de préférence).
        foreach (['mp3', 'm4a', 'aac', 'ogg'] as $ext) {
            $path = storage_path('app/public/chatbot_audio/' . $questionId . '.' . $ext);
            if (file_exists($path)) {
                // Sert via la route API (/api/public/audio/chatbot/{id})
                // pour que les headers CORS soient appliqués (fix web Flutter).
                return url('/api/public/audio/chatbot/' . $questionId);
            }
        }

        return null;
    }

    private function messageDefaut(string $langue): string
    {
        return $langue === 'wo'
            ? "Baal ma, xamuma tontu ci sa laaj bi for léegi. Laajal ko ci sa wisit bu topp."
            : "Désolée, je n'ai pas encore la réponse à cette question. N'hésitez pas à la poser lors de votre prochaine consultation prénatale.";
    }
}
