<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\Signature;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Primitive\BitString;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Feature\AlgorithmIdentifierType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Signature\ECSignatureAlgorithmIdentifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Signature\RSASignatureAlgorithmIdentifier;

/**
 * Base class for signature values.
 */
abstract class Signature
{
    /**
     * Get the signature as a BitString.
     *
     * @return BitString
     */
    abstract public function bitString(): BitString;

    /**
     * Get signature object by signature data and used algorithm.
     *
     * @param string                  $data Signature value
     * @param AlgorithmIdentifierType $algo Algorithm identifier
     *
     * @return self
     */
    public static function fromSignatureData(string $data,
        AlgorithmIdentifierType $algo): Signature
    {
        if ($algo instanceof RSASignatureAlgorithmIdentifier) {
            return RSASignature::fromSignatureString($data);
        }
        if ($algo instanceof ECSignatureAlgorithmIdentifier) {
            return ECSignature::fromDER($data);
        }
        return new GenericSignature(new BitString($data), $algo);
    }
}
