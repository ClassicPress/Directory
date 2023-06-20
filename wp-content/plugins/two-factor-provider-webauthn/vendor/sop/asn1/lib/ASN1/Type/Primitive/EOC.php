<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Primitive;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Component\Identifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Component\Length;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Element;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Exception\DecodeException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Feature\ElementBase;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\PrimitiveType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\UniversalClass;

/**
 * Implements *End-of-contents* type.
 */
class EOC extends Element
{
    use UniversalClass;
    use PrimitiveType;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_typeTag = self::TYPE_EOC;
    }

    /**
     * {@inheritdoc}
     */
    protected function _encodedContentDER(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected static function _decodeFromDER(Identifier $identifier,
        string $data, int &$offset): ElementBase
    {
        $idx = $offset;
        if (!$identifier->isPrimitive()) {
            throw new DecodeException('EOC value must be primitive.');
        }
        // EOC type has always zero length
        Length::expectFromDER($data, $idx, 0);
        $offset = $idx;
        return new self();
    }
}
