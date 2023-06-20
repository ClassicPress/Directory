<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustPath\TrustPathInterface;

interface TrustPathValidatorInterface
{
    public function validate(TrustPathInterface $trustPath, TrustAnchorInterface $trustAnchor): bool;
}
