<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdatePassword extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The old password is invalid'], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json($user, 200);
    }

}
