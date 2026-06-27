<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGynecologueActif
{
    public function handle(Request $request, Closure $next): Response
    {
        $gynecologue = $request->user('gynecologue');

        if (! $gynecologue) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        if (! $gynecologue->is_active) {
            return response()->json(['message' => 'Votre compte a été désactivé.'], 403);
        }

        return $next($request);
    }
}
