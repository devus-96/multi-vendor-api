<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyEmail extends Controller
{
    public function __invoke(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->to(env('WEB_CLIENT_URL') . "/auth/login?t=e&m=Verification link has expired");
        }

        $data = TokenService::decode($token);

        if (!$data) {
            return redirect()->to(env('WEB_CLIENT_URL') . "/auth/login?t=e&m=Verification link has expired");
        }

        $user = User::find($data->id); // Fixed typo: it was "tokenabled_id"

        if (!$user) {
            return redirect()->to(env('WEB_CLIENT_URL') . "/auth/login?t=e&m=User not found");
        }

        $user->verified_at = now();
        $user->email_verified_at = now();
        $user->save();

        return redirect()->to(env('WEB_CLIENT_URL') . "/auth/login?t=s&m=Account verified successfully");
    }
}
