<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Registry;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationObject;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement\AttestationStatementInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Verifier\AttestationVerifierInterface;

class BuiltInAttestationFormat implements AttestationFormatInterface
{
    /**
     * @var string
     */
    private $formatId;

    /**
     * @var string
     */
    private $statementClass;

    /**
     * @var AttestationVerifierInterface
     */
    private $verifier;

    /**
     * @phpstan-param class-string<AttestationStatementInterface> $statementClass
     */
    public function __construct(string $formatId, string $statementClass, AttestationVerifierInterface $verifier)
    {
        $this->formatId = $formatId;
        $this->statementClass = $statementClass;
        $this->verifier = $verifier;
    }

    public function getFormatId(): string
    {
        return $this->formatId;
    }

    public function createStatement(AttestationObject $attestationObject): AttestationStatementInterface
    {
        $class = $this->statementClass;
        return new $class($attestationObject);
    }

    public function getVerifier(): AttestationVerifierInterface
    {
        return $this->verifier;
    }
}
