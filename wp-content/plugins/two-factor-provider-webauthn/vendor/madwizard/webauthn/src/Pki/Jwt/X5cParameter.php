<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\Jwt;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\CoseKeyInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\X509Certificate;

class X5cParameter
{
    /**
     * @var array|X509Certificate[]
     */
    private $certificates;

    /**
     * @var CoseKeyInterface
     */
    private $key;

    /**
     * X5cParameter constructor.
     *
     * @param X509Certificate[] $certificates
     */
    public function __construct(array $certificates, CoseKeyInterface $key)
    {
        $this->certificates = $certificates;
        $this->key = $key;
    }

    /**
     * Certificates in X5C in the order from the JWT (leaf first).
     *
     * @return array|X509Certificate[]
     */
    public function getCertificates(): array
    {
        return $this->certificates;
    }

    public function getCoseKey(): CoseKeyInterface
    {
        return $this->key;
    }
}
