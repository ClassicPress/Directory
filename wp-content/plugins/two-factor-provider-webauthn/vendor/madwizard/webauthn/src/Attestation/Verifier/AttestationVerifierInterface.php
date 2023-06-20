<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Verifier;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AuthenticatorData;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement\AttestationStatementInterface;

interface AttestationVerifierInterface
{
    public function verify(AttestationStatementInterface $attStmt, AuthenticatorData $authenticatorData, string $clientDataHash): VerificationResult;
}
