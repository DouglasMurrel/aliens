<?php

namespace App\Service;

class SHA256Helper {
    public static function SHA256(?string $s): string
    {
        $s = (string)$s;
        return rtrim(strtr(base64_encode(hash('sha256', $s, true)), '+/', '-_'), '=');
    }
}
