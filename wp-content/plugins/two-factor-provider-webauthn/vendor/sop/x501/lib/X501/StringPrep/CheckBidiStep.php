<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X501\StringPrep;

/**
 * Implements 'Check bidi' step of the Internationalized String Preparation
 * as specified by RFC 4518.
 *
 * @see https://tools.ietf.org/html/rfc4518#section-2.5
 */
class CheckBidiStep implements PrepareStep
{
    /**
     * @param string $string UTF-8 encoded string
     *
     * @return string
     */
    public function apply(string $string): string
    {
        // @todo Implement
        return $string;
    }
}
