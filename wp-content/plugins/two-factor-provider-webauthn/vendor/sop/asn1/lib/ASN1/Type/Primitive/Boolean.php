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
 * Implements *BOOLEAN* type.
 */
class Boolean extends Element
{
    use UniversalClass;
    use PrimitiveType;

    /**
     * Value.
     *
     * @var bool
     */
    private $_bool;

    /**
     * Constructor.
     */
    public function __construct(bool $bool)
    {
        $this->_typeTag = self::TYPE_BOOLEAN;
        $this->_bool = $bool;
    }

    /**
     * Get the value.
     */
    public function value(): bool
    {
        return $this->_bool;
    }

    /**
     * {@inheritdoc}
     */
    protected function _encodedContentDER(): string
    {
        return $this->_bool ? chr(0xff) : chr(0);
    }

    /**
     * {@inheritdoc}
     */
    protected static function _decodeFromDER(Identifier $identifier,
        string $data, int &$offset): ElementBase
    {
        $idx = $offset;
        Length::expectFromDER($data, $idx, 1);
        $byte = ord($data[$idx++]);
        if (0 !== $byte) {
            if (0xff !== $byte) {
                throw new DecodeException(
                    'DER encoded boolean true must have all bits set to 1.');
            }
        }
        $offset = $idx;
        return new self(0 !== $byte);
    }
}
