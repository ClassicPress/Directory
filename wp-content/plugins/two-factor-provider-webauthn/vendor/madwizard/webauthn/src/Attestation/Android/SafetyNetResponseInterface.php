<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Android;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\X509Certificate;

interface SafetyNetResponseInterface
{
    public function getNonce(): string;

    /**
     * @return int|float
     */
    public function getTimestampMs();

    /**
     * @return X509Certificate[]
     */
    public function getCertificateChain(): array;

    public function isCtsProfileMatch(): bool;
}
