<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension;

final class ExtensionHelper
{
    public const MAX_IDENTIFIER_LENGHT = 32;

    public static function validExtensionIdentifier(string $identifier): bool
    {
        // SPEC 9.1
        // All extension identifiers MUST be a maximum of 32 octets in length and MUST consist only of printable
        // USASCII characters, excluding backslash and doublequote, i.e., VCHAR as defined in [RFC5234] but without
        // %x22 and %x5c. Implementations MUST match WebAuthn extension identifiers in a case-sensitive fashion.
        if (strlen($identifier) > self::MAX_IDENTIFIER_LENGHT) {
            return false;
        }
        return (bool) preg_match('~^[\x21\x23-\x5B\x5D-\x7E]+$~', $identifier);
    }
}
