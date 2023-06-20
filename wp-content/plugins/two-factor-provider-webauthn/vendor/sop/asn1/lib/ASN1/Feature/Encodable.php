<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Feature;

/**
 * Interface for classes that may be encoded to DER.
 */
interface Encodable
{
    /**
     * Encode object to DER.
     */
    public function toDER(): string;
}
