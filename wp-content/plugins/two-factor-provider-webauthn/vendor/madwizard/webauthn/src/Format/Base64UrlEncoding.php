<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use function base64_decode;
use function base64_encode;

final class Base64UrlEncoding
{
    public static function encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function decode(string $data): string
    {
        $res = base64_decode(strtr($data, '-_', '+/'), true);
        if ($res === false) {
            throw new ParseException('Failed to decode base64url encoded data');
        }
        return $res;
    }
}
