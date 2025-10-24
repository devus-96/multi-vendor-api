<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Session;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use App\Services\JWTService;
use App\Services\RefreshToken;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone_number' => 'required|string|unique:'.User::class
        ]);


         if ($validator->fails()) {
            return response()->json([
                'statut' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number
        ]);

        $token = JWTService::generate([
            "id" => $user->id,
        ], 3600 / 2);

        $opaque_token = RefreshToken::generateOpaqueToken();

        // créer une ligne de session en BD et récupérer son id
        $session = Session::create([
            'user_id' => $user->id,
            'token' => $opaque_token,
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json([
            'statut' => 200,
            'user' => $user,
            'token' => $token,
            'opaque_token' => $opaque_token,
            'message' => 'User created successfully!!'
        ]);
    }
}

?>
