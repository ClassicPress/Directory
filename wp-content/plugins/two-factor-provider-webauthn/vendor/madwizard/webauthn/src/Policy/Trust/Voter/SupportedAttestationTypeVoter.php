<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy\Trust\Voter;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor\MetadataInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustPath\TrustPathInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy\Trust\TrustVote;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationResultInterface;

final class SupportedAttestationTypeVoter implements TrustVoterInterface
{
    public function voteOnTrust(
        RegistrationResultInterface $registrationResult,
        TrustPathInterface $trustPath,
        ?MetadataInterface $metadata
    ): TrustVote {
        if ($metadata === null) {
            return TrustVote::abstain();
        }

        $type = $registrationResult->getVerificationResult()->getAttestationType();
        // 'None' attestation is not specified in metadata so ignore that case, otherwise authenticator should support
        // this type.
        if ($type !== AttestationType::NONE && !$metadata->supportsAttestationType($type)) {
            return TrustVote::untrusted();
        }
        return TrustVote::abstain();
    }
}
