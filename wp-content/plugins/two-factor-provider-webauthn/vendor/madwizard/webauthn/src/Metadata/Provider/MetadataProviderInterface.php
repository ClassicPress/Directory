<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Provider;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor\MetadataInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationResultInterface;

interface MetadataProviderInterface
{
    /**
     * @throws WebAuthnException
     */
    public function getMetadata(RegistrationResultInterface $registrationResult): ?MetadataInterface;

    public function getDescription(): string;
}
