<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Feature;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Constructed\Sequence;

/**
 * Base interface for algorithm identifiers.
 */
interface AlgorithmIdentifierType
{
    /**
     * Get the object identifier of the algorithm.
     *
     * @return string Object identifier in dotted format
     */
    public function oid(): string;

    /**
     * Get a human readable name of the algorithm.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Generate ASN.1 structure.
     *
     * @return Sequence
     */
    public function toASN1(): Sequence;
}
