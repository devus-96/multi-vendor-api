<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JWTService;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\ExpiredException;

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
                Auth::logout();
                return response()->json(['error' => 'something went wrong'], 500);
            }
        } catch (ExpiredException $e) {
              return response()->json(['error' => 'Expired token'], 401);
        }catch (\Firebase\JWT\SignatureInvalidException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Signature du token invalide'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token invalide'
            ], 401);
        }
        return $next($request);
    }
}
