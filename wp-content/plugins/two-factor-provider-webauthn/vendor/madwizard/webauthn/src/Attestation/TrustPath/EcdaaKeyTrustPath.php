<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustPath;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

final class EcdaaKeyTrustPath implements TrustPathInterface
{
    /**
     * @var ByteBuffer
     */
    private $ecdaaKeyId;

    public function __construct(ByteBuffer $ecdaaKeyId)
    {
        $this->ecdaaKeyId = $ecdaaKeyId;
    }

    public function getEcdaaKeyId(): ByteBuffer
    {
        return $this->ecdaaKeyId;
    }
}
