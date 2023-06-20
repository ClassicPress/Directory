<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Registry;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationObject;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement\AttestationStatementInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Verifier\AttestationVerifierInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\DataValidationException;

interface AttestationFormatInterface
{
    /**
     * Returns format ID for this attestation format. For example 'fido-u2f'.
     */
    public function getFormatId(): string;

    /**
     * Creates an attestation statement object from an attestation object. Should be called only for attestation
     * objects with format ID supported by this class (@see getFormatId).
     *
     * @throws DataValidationException
     */
    public function createStatement(AttestationObject $attestationObject): AttestationStatementInterface;

    /**
     * Gets a reference to a verifier that verifies attestation statements of the format supported by this class.
     */
    public function getVerifier(): AttestationVerifierInterface;
}
