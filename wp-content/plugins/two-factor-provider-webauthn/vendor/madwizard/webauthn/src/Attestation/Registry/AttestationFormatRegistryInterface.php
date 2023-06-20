<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Registry;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationObject;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement\AttestationStatementInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Verifier\AttestationVerifierInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\FormatNotSupportedException;

interface AttestationFormatRegistryInterface
{
    public function createStatement(AttestationObject $attestationObject): AttestationStatementInterface;

    /**
     * @throws FormatNotSupportedException
     */
    public function getVerifier(string $formatId): AttestationVerifierInterface;
}
