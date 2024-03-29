<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Signature;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Element;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Primitive\NullType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\UnspecifiedType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\SpecificAlgorithmIdentifier;

/*
From RFC 3279 - 2.2.1  RSA Signature Algorithm:

   When any of these three OIDs appears within the ASN.1 type
   AlgorithmIdentifier, the parameters component of that type SHALL be
   the ASN.1 type NULL.
*/

/**
 * Base class for RSA signature algorithms specified in RFC 3279.
 *
 * @see https://tools.ietf.org/html/rfc3279#section-2.2.1
 */
abstract class RFC3279RSASignatureAlgorithmIdentifier extends RSASignatureAlgorithmIdentifier
{
    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public static function fromASN1Params(
        ?UnspecifiedType $params = null): SpecificAlgorithmIdentifier
    {
        if (!isset($params)) {
            throw new \UnexpectedValueException('No parameters.');
        }
        $params->asNull();
        return new static();
    }

    /**
     * {@inheritdoc}
     *
     * @return NullType
     */
    protected function _paramsASN1(): ?Element
    {
        return new NullType();
    }
}
