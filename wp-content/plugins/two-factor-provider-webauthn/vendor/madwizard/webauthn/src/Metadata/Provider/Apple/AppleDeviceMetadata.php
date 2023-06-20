<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Provider\Apple;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor\CertificateTrustAnchor;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor\MetadataInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\X509Certificate;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Util\BundledData;

final class AppleDeviceMetadata implements MetadataInterface
{
    public function getTrustAnchors(): array
    {
        return [
            new CertificateTrustAnchor(X509Certificate::fromPem(BundledData::getContents('apple/apple-webauthn-root.crt'))),
        ];
    }

    public function supportsAttestationType(string $type): bool
    {
        return $type === AttestationType::ANON_CA;
    }

    public function getStatusReports(): array
    {
        return [];
    }

    public function getDescription(): string
    {
        return 'Apple device (Touch ID / Face ID)';
    }
}
