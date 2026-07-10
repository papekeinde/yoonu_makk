<?php

namespace App\Services\Chatbot;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client de l'API chatbot prénatal externe (wolof/français).
 * Endpoints : POST /api/query/text, POST /api/query/audio, GET /api/audio/{id}.
 */
class RemoteChatbotClient
{
    private string $baseUrl;
    private int    $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.chatbot.base_url'), '/');
        $this->timeout = (int) config('services.chatbot.timeout', 20);
    }

    /** Question texte → réponse texte (+ audio optionnel). Null si échec. */
    public function queryText(string $message, string $langue = 'fr', bool $audio = false): ?array
    {
        if ($this->baseUrl === '') {
            return null;
        }

        try {
            $res = Http::timeout($this->timeout)
                ->acceptJson()
                ->post("{$this->baseUrl}/api/query/text", [
                    'query'          => $message,
                    'language'       => $this->mapLangue($langue),
                    'generate_audio' => $audio,
                ]);

            if (! $res->successful()) {
                return null;
            }

            return $this->normaliser($res->json());
        } catch (\Throwable $e) {
            Log::warning('Chatbot API (text) injoignable', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /** Fichier audio → transcription + réponse (+ audio). Null si échec. */
    public function queryAudio(UploadedFile $audio, string $langue = 'fr'): ?array
    {
        if ($this->baseUrl === '') {
            return null;
        }

        try {
            $res = Http::timeout($this->timeout + 20)
                ->acceptJson()
                ->attach('audio', file_get_contents($audio->getRealPath()), $audio->getClientOriginalName() ?: 'audio.m4a')
                ->post("{$this->baseUrl}/api/query/audio", [
                    'language'       => $this->mapLangue($langue),
                    'generate_audio' => 'true',
                ]);

            if (! $res->successful()) {
                return null;
            }

            return $this->normaliser($res->json());
        } catch (\Throwable $e) {
            Log::warning('Chatbot API (audio) injoignable', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /** Uniformise la réponse et rend l'URL audio absolue. */
    private function normaliser(?array $data): ?array
    {
        if (! is_array($data)) {
            return null;
        }

        $audioUrl = $data['audio_url'] ?? null;
        if (is_string($audioUrl) && $audioUrl !== '' && ! str_starts_with($audioUrl, 'http')) {
            $audioUrl = $this->baseUrl . '/' . ltrim($audioUrl, '/');
        }

        return [
            'response'      => $data['response'] ?? null,
            'transcription' => $data['transcription'] ?? null,
            'language'      => $data['language'] ?? $data['asr_language'] ?? null,
            'audio_url'     => $audioUrl,
            'confidence'    => $data['confidence'] ?? null,
        ];
    }

    private function mapLangue(string $langue): string
    {
        return match ($langue) {
            'fr' => 'fr',
            'wo' => 'wo',
            default => 'auto',
        };
    }
}
