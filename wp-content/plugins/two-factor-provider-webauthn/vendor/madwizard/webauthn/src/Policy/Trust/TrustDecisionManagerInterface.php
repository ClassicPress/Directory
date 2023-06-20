<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy\Trust;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor\MetadataInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\UntrustedException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationResultInterface;

interface TrustDecisionManagerInterface
{
    /**
     * Returns if the registration is trusted by this decision manager.
     * Exception UntrustedException is thrown when the registration is not trusted.
     *
     * @throws UntrustedException
     */
    public function verifyTrust(RegistrationResultInterface $registrationResult, ?MetadataInterface $metadata): void;
}
