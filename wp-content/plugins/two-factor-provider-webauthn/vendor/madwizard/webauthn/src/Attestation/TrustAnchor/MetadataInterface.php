<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Statement\StatusReport;

interface MetadataInterface
{
    /**
     * @return TrustAnchorInterface[]
     */
    public function getTrustAnchors(): array;

    /**
     * @see AttestationType
     */
    public function supportsAttestationType(string $type): bool;

    /**
     * @return StatusReport[]
     */
    public function getStatusReports(): array;

    public function getDescription(): string;
}
