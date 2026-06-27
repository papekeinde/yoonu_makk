<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StoreChatbotMessageRequest;
use App\Http\Resources\MessageChatbotResource;
use App\Models\MessageChatbot;
use App\Services\Chatbot\ChatbotManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function __construct(
        private ChatbotManager $chatbot,
    ) {}

    public function historique(Request $request): JsonResponse
    {
        $messages = $request->user()
            ->messagesChatbot()
            ->orderByDesc('created_at')
            ->paginate(30);

        return response()->json(MessageChatbotResource::collection($messages)->response()->getData(true));
    }

    public function envoyer(StoreChatbotMessageRequest $request): JsonResponse
    {
        $user      = $request->user();
        $sessionId = $request->session_id ?? Str::uuid()->toString();
        $langue    = $request->langue ?? 'fr';

        MessageChatbot::create([
            'user_id'    => $user->id,
            'session_id' => $sessionId,
            'role'       => 'utilisateur',
            'message'    => $request->message,
            'langue'     => $langue,
        ]);

        // generate_audio si le client demande une réponse vocalisée (?audio=1)
        $avecAudio = $request->boolean('audio');
        $resultat  = $this->chatbot->respond($request->message, $langue, $avecAudio);

        $messageBot = MessageChatbot::create([
            'user_id'    => $user->id,
            'session_id' => $sessionId,
            'role'       => 'assistant',
            'message'    => $resultat['response'],
            'langue'     => $langue,
        ]);

        return response()->json([
            'message'    => new MessageChatbotResource($messageBot),
            'session_id' => $sessionId,
            'audio_url'  => $resultat['audio_url'] ?? null,
        ]);
    }

    public function envoyerAudio(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'audio'      => ['required', 'file', 'mimes:mp3,wav,m4a,aac,ogg,webm,3gp', 'max:10240'],
            'session_id' => ['nullable', 'uuid'],
            'langue'     => ['nullable', 'in:fr,wo'],
        ]);

        $user      = $request->user();
        $sessionId = $validated['session_id'] ?? Str::uuid()->toString();
        $langue    = $validated['langue'] ?? 'fr';

        $resultat = $this->chatbot->respondAudio($request->file('audio'), $langue);

        if ($resultat === null) {
            return response()->json([
                'message' => 'Le service vocal est momentanément indisponible. Réessayez ou écrivez votre question.',
            ], 503);
        }

        // Message de l'utilisateur = transcription de l'audio
        if (! empty($resultat['transcription'])) {
            MessageChatbot::create([
                'user_id'    => $user->id,
                'session_id' => $sessionId,
                'role'       => 'utilisateur',
                'message'    => $resultat['transcription'],
                'langue'     => $langue,
            ]);
        }

        $messageBot = MessageChatbot::create([
            'user_id'    => $user->id,
            'session_id' => $sessionId,
            'role'       => 'assistant',
            'message'    => $resultat['response'],
            'langue'     => $langue,
        ]);

        return response()->json([
            'message'       => new MessageChatbotResource($messageBot),
            'transcription' => $resultat['transcription'] ?? null,
            'audio_url'     => $resultat['audio_url'] ?? null,
            'session_id'    => $sessionId,
        ]);
    }
}
