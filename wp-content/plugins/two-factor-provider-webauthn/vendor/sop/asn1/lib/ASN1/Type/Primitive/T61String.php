<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Primitive;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\PrimitiveString;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\UniversalClass;

/**
 * Implements *T61String* type.
 */
class T61String extends PrimitiveString
{
    use UniversalClass;

    /**
     * Constructor.
     */
    public function __construct(string $string)
    {
        $this->_typeTag = self::TYPE_T61_STRING;
        parent::__construct($string);
    }

    /**
     * {@inheritdoc}
     */
    protected function _validateString(string $string): bool
    {
        // allow everything since there's literally
        // thousands of allowed characters (16 bit composed characters)
        return true;
    }
}
