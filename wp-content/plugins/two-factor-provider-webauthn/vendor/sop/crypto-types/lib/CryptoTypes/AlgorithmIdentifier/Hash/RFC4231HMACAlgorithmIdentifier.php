<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Hash;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Element;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Primitive\NullType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\UnspecifiedType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Feature\HashAlgorithmIdentifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Feature\PRFAlgorithmIdentifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\SpecificAlgorithmIdentifier;

/**
 * Base class for HMAC algorithm identifiers specified in RFC 4231.
 *
 * @see https://tools.ietf.org/html/rfc4231#section-3.1
 */
abstract class RFC4231HMACAlgorithmIdentifier extends SpecificAlgorithmIdentifier implements HashAlgorithmIdentifier, PRFAlgorithmIdentifier
{
    /**
     * Parameters stored for re-encoding.
     *
     * @var null|NullType
     */
    protected $_params;

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public static function fromASN1Params(
        ?UnspecifiedType $params = null): SpecificAlgorithmIdentifier
    {
        /*
         * RFC 4231 states that the "parameter" component SHOULD be present
         * but have type NULL.
         */
        $obj = new static();
        if (isset($params)) {
            $obj->_params = $params->asNull();
        }
        return $obj;
    }

    /**
     * {@inheritdoc}
     *
     * @return null|NullType
     */
    protected function _paramsASN1(): ?Element
    {
        return $this->_params;
    }
}
