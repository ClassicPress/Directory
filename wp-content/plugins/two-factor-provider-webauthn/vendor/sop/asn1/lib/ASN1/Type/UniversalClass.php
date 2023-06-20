<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Component\Identifier;

/**
 * Trait for types of universal class.
 */
trait UniversalClass
{
    /**
     * @see \Sop\ASN1\Feature\ElementBase::typeClass()
     */
    public function typeClass(): int
    {
        return Identifier::CLASS_UNIVERSAL;
    }
}
