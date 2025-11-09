<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Cookie\CookieJar;
use Illuminate\Validation\Factory;
use Illuminate\Contracts\Routing\ResponseFactory;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request, Validator $validator, CookieJar $cookie, ResponseFactory $response): JsonResponse
    {

        $validator = $validator->make($request->all(), [
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $response->json([
                'statut' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $user = User::query()->where("email", $request->input("email"))->first();

        // 2. Générer le JWT
        $token = JWTService::generate([
            "id" => $user->id,
        ], 60*60);

        // 3. Générer le refresh token
        [$secret, $tokenHash] = RefreshToken::generateOpaqueToken();

        $refreshToken = RefreshToken::query()->create([
            'user_id' => $user->id,
            'token' => $tokenHash,
            'expires_at' => now()->addDays(30)
        ]);

        $refreshCookie = $cookie->make(
            'refresh_token',
            $refresh_token->id . '.' . $secret,
            60 * 24 * 30, // Durée de 30 jours
            '/',
            null,
            true, // Secure (nécessite HTTPS)
            true  // HttpOnly (empêche JS d'y accéder)
        );

        // 4. Retourner JSON (pas de redirection)
        return $response->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token
        ])->withCookie($refreshCookie);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request, CookieJar $cookie, ResponseFactory $response): JsonResponse
    {
        try {
            // 1. Récupérer le refresh token du cookie
            $cookieValue = $request->cookie('refresh_token');

            if ($cookieValue) {
                [$id, $secret] = explode('.', $cookieValue, 2);

                // 2. Supprimer le refresh token de la BD
                RefreshToken::query()->where('id', $id)->delete();

                // Optionnel : Supprimer TOUS les refresh tokens de l'utilisateur
                // RefreshToken::where('user_id', $request->user_id)->delete();
            }

            // 3. Optionnel : Blacklister le JWT actuel
            // (nécessite une table de blacklist si JWT non expiré)

            // 4. Supprimer le cookie refresh_token
            $cookie = $cookie->forget('refresh_token');

            // 5. Déconnecter de la session (si vous utilisez les sessions)
            if ($request->hasSession()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return $response->json([
                'status' => 'success',
                'message' => 'Déconnexion réussie'
            ])->withCookie($cookie);

        } catch (\Exception $e) {
            return $response->json([
                'status' => 'error',
                'message' => 'Erreur lors de la déconnexion'
            ], 500);
        }
    }
}
