<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JWTService;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountCreated;

class EmailVerificationLinkController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        if ($user->verified_at) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $token = JWTService::generate([
            "id" => $user->id,
        ], 60);

        $user->link = url('/verify/email?token='.$token);
        Mail::to($user->email)->send(new AccountCreated($user));

        return response()->json(['message' => 'Verification email resent.']);
    }
}
