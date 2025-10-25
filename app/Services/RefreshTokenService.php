<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RefreshTokenService {
    static function generateOpaqueToken()
    {
        // secret aléatoire que l'on retourne au client
        $secret = Str::random(60);

        // stocker un hash du secret (comme mot de passe)
        $tokenHash = Hash::make($secret);

        return [$secret, $tokenHash];
    }
}

?>
