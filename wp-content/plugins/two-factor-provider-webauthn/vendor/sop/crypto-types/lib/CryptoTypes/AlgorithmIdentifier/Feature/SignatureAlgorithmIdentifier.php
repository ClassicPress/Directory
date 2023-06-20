<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Feature;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\AlgorithmIdentifier;

/**
 * Algorithm identifier for signature algorithms.
 */
interface SignatureAlgorithmIdentifier extends AlgorithmIdentifierType
{
    /**
     * Check whether signature algorithm supports given key algorithm.
     *
     * @param AlgorithmIdentifier $algo
     *
     * @return bool
     */
    public function supportsKeyAlgorithm(AlgorithmIdentifier $algo): bool;
}
