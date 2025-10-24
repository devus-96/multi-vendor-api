<?php

namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class JWTService
{
    /**
     *  Generate a token
     *
     *  @param mixed $data
     *  @param integer $expirationTime
     *  @return
     */
    static function generate($data, $expirationTime = null)
    {
        $key = config('session.secret');

        $payload = [
            "iss" => "localhost",
            "iat" => time(),
            "data" => $data,
        ];

        if($expirationTime != null){
            $payload["exp"] = time() + $expirationTime;
        }

        return JWT::encode($payload, $key, "HS256");
    }

    /**
     *  decode token
     *
     *  @param string token
     *  @return mixed data
     */

    static function decode($token)
    {
        $key = config('session.secret');
        try {
            $decoded = JWT::decode($token, new Key($key,"HS256"));
            return $decoded->data;
        }catch(ExpiredException $e){
            return $e;
        }
    }

    static function refresh ($request) {
        $token = $request->bearerToken();

        if (!$token || !str_contains($token, '.')) {
            return false;
        }

        [$id, $secret] = explode('.', $token, 2);

        $session = Session::find($id);

        if (!$session) {
            return false;
        }

         // vérifier l'expiration
        if ($session->expires_at->isPast()) {
            $session->delete();
            return false;
        }

        // comparer le secret fourni et le hash stocké
        if (!Hash::check($secret, $session->token_hash)) {
           return false;
        }

       return $token;
    }

    /**
     *  Email is use as identifier.
     */
    static function verify($request)
    {
        $key = config('session.secret');
        $bearerHeader = $request->header("Authorization");
        if ($bearerHeader == null) {
            return false;
        }

        $bearer = explode(" ", $bearerHeader);

        if (empty($bearer)) {
            return false;
        }

        if ($bearer[0] != "Bearer") {
            return false;
        }

        $bearerToken = $bearer[1];

        try {
            $decoded = JWT::decode($bearerToken, new Key($key, "HS256"));

            $data = $decoded->data;

            $user = User::select("id")
                ->where("id", "=", $data->id)
                ->first();

            if ($user == null) {
                return false;
            } else {
                $request->query->set("user_id", $user->id);
                return $user;
            }
        }catch(Exception $e) {
            return false;
        }catch(ExpiredException $e){
            return $e;
        }

    }

    static function user($request){

        $bearerHeader = $request->header("Authorization");
        if ($bearerHeader == null) {
            return null;
        }

        $bearer = explode(" ", $bearerHeader);

        if (empty($bearer)) {
            return null;
        }

        if ($bearer[0] != "Bearer") {
            return null;
        }

        $bearerToken = $bearer[1];

        $decoded = JWT::decode($bearerToken, new Key(self::$secret, "HS256"));

        $data = $decoded->data;

        return User::where("id", "=", $data->id)
            ->first()->makeHidden("password");
    }

}
