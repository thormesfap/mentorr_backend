<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoggedUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('api')->user()) {
            return response()->json(['success' => false, 'message' => 'Usuário não logado'], Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
