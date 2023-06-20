<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Verifier;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AuthenticatorData;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Registry\AttestationFormatInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Registry\BuiltInAttestationFormat;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement\AttestationStatementInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement\NoneAttestationStatement;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustPath\EmptyTrustPath;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\VerificationException;

final class NoneAttestationVerifier implements AttestationVerifierInterface
{
    public function verify(AttestationStatementInterface $attStmt, AuthenticatorData $authenticatorData, string $clientDataHash): VerificationResult
    {
        if (!($attStmt instanceof NoneAttestationStatement)) {
            throw new VerificationException('Expecting NoneAttestationStatement.');
        }
        return new VerificationResult(AttestationType::NONE, new EmptyTrustPath());
    }

    public function getSupportedFormat(): AttestationFormatInterface
    {
        return new BuiltInAttestationFormat(
            NoneAttestationStatement::FORMAT_ID,
            NoneAttestationStatement::class,
            $this
        );
    }
}
