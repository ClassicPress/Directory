<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor;

interface TrustAnchorInterface
{
    public function getType(): string;
}
