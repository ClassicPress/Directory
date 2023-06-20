<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationObject;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AuthenticatorData;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Identifier\IdentifierInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor\MetadataInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Verifier\VerificationResult;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\CredentialId;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\UserHandle;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\CoseKeyInterface;

interface RegistrationResultInterface
{
    public function getCredentialId(): CredentialId;

    public function getPublicKey(): CoseKeyInterface;

    public function getVerificationResult(): VerificationResult;

    public function getAttestationObject(): AttestationObject;

    public function getSignatureCounter(): int;

    public function getAuthenticatorData(): AuthenticatorData;

    public function getMetadata(): ?MetadataInterface;

    public function getIdentifier(): ?IdentifierInterface;

    public function getUserHandle(): UserHandle;

    public function isUserVerified(): bool;
}
