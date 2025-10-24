<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JWTService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RefreshTokenController extends Controller
{
    function __invoke (Request $request) {
        $user = Auth::user();

        $RToken =  JWTService::refresh();

        if (!$RToken) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        $token = JWTService::generate([
            "id" => $user->id,
        ], 3600 / 2);

         return response()->json([
            'statut' => 200,
            'user' => $user,
            'token' => $token,
            'opaque_token' => $RToken,
            'message' => 'User created successfully!!'
        ]);
    }
}
