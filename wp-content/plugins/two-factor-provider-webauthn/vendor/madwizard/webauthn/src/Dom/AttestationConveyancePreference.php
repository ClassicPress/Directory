<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom;

final class AttestationConveyancePreference
{
    /**
     *  Indicates that the Relying Party is not interested in authenticator attestation. F.
     */
    public const NONE = 'none';

    /**
     *  Indicates that the Relying Party prefers an attestation conveyance yielding verifiable attestation statements,
     *  but allows the client to decide how to obtain such attestation statements.
     */
    public const INDRECT = 'indirect';

    /**
     * Indicates that the Relying Party wants to receive the attestation statement as generated by the authenticator.
     */
    public const DIRECT = 'direct';

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function isValidValue(string $value): bool
    {
        return $value === self::NONE || $value === self::INDRECT || $value === self::DIRECT;
    }
}
