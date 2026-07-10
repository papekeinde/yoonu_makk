<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\MailConfiguration;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationEmailController extends Controller
{
    /**
     * Vérification email Laravel:
     * - le hash du lien généré par le framework est basé sur SHA-1.
     * - hash_equals protège contre les attaques timing.
     * - la route est protégée par le middleware `signed`.
     */
    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json(['message' => 'Lien de vérification invalide.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.']);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email vérifié avec succès.']);
    }

    public function resend(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.']);
        }

        if (! MailConfiguration::isConfigured()) {
            return response()->json([
                'message' => 'Configuration email incomplète. Renseignez MAIL_USERNAME et MAIL_PASSWORD Mailtrap.',
            ], 503);
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Service email indisponible. Vérifiez la configuration Mailtrap SMTP.',
            ], 503);
        }

        return response()->json(['message' => 'Lien de vérification envoyé.']);
    }
}
