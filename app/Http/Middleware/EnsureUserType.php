<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserType
{
    /**
     * Ensure the authenticated user has one of the expected roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return new JsonResponse(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        if (!in_array($user->user_type, $roles, true)) {
            return new JsonResponse(['message' => 'Forbidden for role '.$user->user_type], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
