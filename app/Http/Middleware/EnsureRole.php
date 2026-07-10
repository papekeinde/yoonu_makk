<?php

namespace App\Http\Middleware;

use App\Enums\RoleUtilisateur;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        $rolesEnum = array_map(
            fn(string $r) => RoleUtilisateur::from($r),
            $roles
        );

        if (! in_array($user->role, $rolesEnum)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        return $next($request);
    }
}
