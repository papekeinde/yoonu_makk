<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerifie
{
    public function handle(Request $request, Closure $next, string $guard = 'sanctum'): Response
    {
        $user = match($guard) {
            'gynecologue' => $request->user('gynecologue'),
            default       => $request->user(),
        };

        if (! $user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Veuillez vérifier votre adresse email avant de continuer.',
            ], 403);
        }

        return $next($request);
    }
}
