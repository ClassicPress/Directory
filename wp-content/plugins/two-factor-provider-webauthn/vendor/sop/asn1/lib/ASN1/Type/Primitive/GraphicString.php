<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Primitive;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\PrimitiveString;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\UniversalClass;

/**
 * Implements *GraphicString* type.
 */
class GraphicString extends PrimitiveString
{
    use UniversalClass;

    /**
     * Constructor.
     */
    public function __construct(string $string)
    {
        $this->_typeTag = self::TYPE_GRAPHIC_STRING;
        parent::__construct($string);
    }

    /**
     * {@inheritdoc}
     */
    protected function _validateString(string $string): bool
    {
        // allow everything
        return true;
    }
}
