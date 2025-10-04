<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMentor
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuário não identificado'], Response::HTTP_UNAUTHORIZED);
        }
        if (!$user->mentor) {
            return response()->json(['success' => false, 'message' => 'Usuário não é mentor'], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
