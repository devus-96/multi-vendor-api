<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JWTService;
use Illuminate\Support\Facades\Auth;

class VerifySessionToken
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTService::verify($request);

            if (!$user) {
                return response()->json(['error' => 'Invalid token'], 401);
            }

            // Si tout est bon, on connecte l'utilisateur
            Auth::login($user);
        } catch (ExpiredException $e) {
              return response()->json(['error' => 'Invalid token'], 401);
        }
        return $next($request);
    }
}
