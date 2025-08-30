<?php

namespace App\Libraries\Helpers;

class JwtHelper
{
    public static function getDecodedTokenPayload($jwt)
    {
        try {
            $tokenParts = explode('.', $jwt);
            $tokenPayload = base64_decode($tokenParts[1]);
            return json_decode($tokenPayload, true);
        } catch (\Exception $e) {
            return null;
        }
    }
}
