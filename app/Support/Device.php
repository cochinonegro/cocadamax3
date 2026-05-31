<?php

namespace App\Support;

class Device
{
    public static function isMobile(?string $userAgent = null): bool
    {
        $ua = strtolower($userAgent ?? request()->userAgent() ?? '');

        if ($ua === '') {
            return false;
        }

        return (bool) preg_match(
            '/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini|mobile/i',
            $ua,
        );
    }
}
