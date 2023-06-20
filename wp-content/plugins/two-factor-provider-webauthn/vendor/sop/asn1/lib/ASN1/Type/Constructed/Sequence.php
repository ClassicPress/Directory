<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Constructed;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Element;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Structure;

/**
 * Implements *SEQUENCE* and *SEQUENCE OF* types.
 */
class Sequence extends Structure
{
    /**
     * Constructor.
     *
     * @param Element ...$elements Any number of elements
     */
    public function __construct(Element ...$elements)
    {
        $this->_typeTag = self::TYPE_SEQUENCE;
        parent::__construct(...$elements);
    }
}
