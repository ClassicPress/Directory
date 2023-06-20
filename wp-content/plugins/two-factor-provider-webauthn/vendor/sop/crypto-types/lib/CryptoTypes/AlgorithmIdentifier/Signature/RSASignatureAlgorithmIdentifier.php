<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Signature;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\AlgorithmIdentifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Feature\SignatureAlgorithmIdentifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\SpecificAlgorithmIdentifier;

/**
 * Base class for signature algorithms employing RSASSA.
 */
abstract class RSASignatureAlgorithmIdentifier extends SpecificAlgorithmIdentifier implements SignatureAlgorithmIdentifier
{
    /**
     * {@inheritdoc}
     */
    public function supportsKeyAlgorithm(AlgorithmIdentifier $algo): bool
    {
        return self::OID_RSA_ENCRYPTION === $algo->oid();
    }
}
