<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Services\JWTService;
use App\Services\RefreshTokenService;
use Illuminate\Cookie\CookieJar;
use Illuminate\Validation\Factory;
use Illuminate\Contracts\Routing\ResponseFactory;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(Request $request, CookieJar $cookie, Validator $validator, AuthManager $auth, ResponseFactory $response): JsonResponse
    {
        $validator = $validator->make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone_number' => 'required|string|unique:'.User::class,
            'image.*'          => 'nullable|mimes:bmp,jpeg,jpg,png,webp',
        ]);


         if ($validator->fails()) {
            return $response->json([
                'statut' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number
        ]);

        $token = JWTService::generate([
            "id" => $user->id,
        ], 60*60);

        [$secret, $tokenHash] = RefreshTokenService::generateOpaqueToken();

         // créer une ligne de session en BD et récupérer son id
        $refresh_token = RefreshToken::query()->create([
            'user_id' => $user->id,
            'token' => $tokenHash,
            "expired_at" => now()->addDays(30)
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

        $auth->login($user, true);

        event(new Registered($user));

        return $response->json([
            'statut' => 200,
            'user' => $user,
            'token' => $token,
            'message' => 'User created successfully!!'
        ])->withCookie($refreshCookie);
    }
}

?>
