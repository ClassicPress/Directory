<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Primitive;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\PrimitiveString;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\UniversalClass;

/**
 * Implements *ObjectDescriptor* type.
 */
class ObjectDescriptor extends PrimitiveString
{
    use UniversalClass;

    /**
     * Constructor.
     */
    public function __construct(string $descriptor)
    {
        $this->_string = $descriptor;
        $this->_typeTag = self::TYPE_OBJECT_DESCRIPTOR;
    }

    /**
     * Get the object descriptor.
     */
    public function descriptor(): string
    {
        return $this->_string;
    }
}
