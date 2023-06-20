<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy\Trust\Voter;

use InvalidArgumentException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor\MetadataInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustPath\TrustPathInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy\Trust\TrustVote;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationResultInterface;

final class TrustAttestationTypeVoter implements TrustVoterInterface
{
    /**
     * @var string
     */
    private $trustedType;

    public function __construct(string $attestationType)
    {
        if (!AttestationType::isValidType($attestationType)) {
            throw new InvalidArgumentException(sprintf('Type "%s" is not a valid attestation type.', $attestationType));
        }

        $this->trustedType = $attestationType;
    }

    public function voteOnTrust(
        RegistrationResultInterface $registrationResult,
        TrustPathInterface $trustPath,
        ?MetadataInterface $metadata
    ): TrustVote {
        if ($registrationResult->getVerificationResult()->getAttestationType() === $this->trustedType) {
            return TrustVote::trusted();
        }
        return TrustVote::abstain();
    }
}
