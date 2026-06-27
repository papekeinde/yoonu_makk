<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MotDePasseEmailRequest;
use App\Http\Requests\Auth\ResetMotDePasseRequest;
use App\Support\MailConfiguration;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class MotDePasseController extends Controller
{
    public function sendLink(MotDePasseEmailRequest $request): JsonResponse
    {
        if (! MailConfiguration::isConfigured()) {
            return response()->json([
                'message' => 'Configuration email incomplète. Renseignez MAIL_USERNAME et MAIL_PASSWORD Mailtrap.',
            ], 503);
        }

        try {
            $status = Password::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Service email indisponible. Vérifiez la configuration Mailtrap SMTP.',
            ], 503);
        }

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Lien de réinitialisation envoyé.']);
        }

        return response()->json(['message' => __($status)], 400);
    }

    public function reset(ResetMotDePasseRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Mot de passe réinitialisé avec succès.']);
        }

        return response()->json(['message' => __($status)], 400);
    }
}
