<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\InscriptionRequest;
use App\Http\Resources\UserResource;
use App\Models\Femme;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;

class InscriptionController extends Controller
{
    public function store(InscriptionRequest $request): JsonResponse
    {
        $user = User::create([
            ...$request->safe()->except(['password', 'type_profil']),
            'password' => $request->password,
            'role'     => 'patient',
        ]);

        // Seules les femmes ont un profil santé (grossesse / ménopause).
        // Les hommes s'inscrivent pour s'informer : pas de profil Femme.
        if ($request->input('genre') === 'femme') {
            Femme::create([
                'user_id'     => $user->id,
                'type_profil' => $request->input('type_profil', 'menopause'),
            ]);
        }

        // L'envoi du mail de vérification ne doit pas faire échouer l'inscription
        // (ex. SMTP indisponible / mal configuré → on log et on continue).
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            report($e);
        }

        // En environnement local, on auto-vérifie l'email pour ne pas bloquer
        // le développement / la démo. La production conserve la vraie
        // vérification par lien (Mailtrap / SMTP).
        if (app()->environment('local')) {
            $user->markEmailAsVerified();
        }

        $token = $user->createToken('api')->plainTextToken;

        $user->load('femme');

        return response()->json([
            'message' => 'Inscription réussie. Veuillez vérifier votre email.',
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 201);
    }
}
