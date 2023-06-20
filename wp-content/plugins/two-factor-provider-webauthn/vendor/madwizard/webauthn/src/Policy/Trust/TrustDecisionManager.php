<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy\Trust;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor\MetadataInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\UntrustedException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy\Trust\Voter\TrustVoterInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationResultInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\LoggerAwareInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\LoggerAwareTrait;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\NullLogger;

final class TrustDecisionManager implements TrustDecisionManagerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var TrustVoterInterface[]
     */
    private $voters = [];

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public function addVoter(TrustVoterInterface $trustVoter): self
    {
        $this->voters[] = $trustVoter;
        return $this;
    }

    public function verifyTrust(RegistrationResultInterface $registrationResult, ?MetadataInterface $metadata): void
    {
        $trusted = false;
        $trustPath = $registrationResult->getVerificationResult()->getTrustPath();
        foreach ($this->voters as $voter) {
            $vote = $voter->voteOnTrust($registrationResult, $trustPath, $metadata);
            if ($vote->isTrusted()) {
                $this->logger->debug("Voter {class} voted 'trusted'.", ['class' => get_class($voter)]);
                $trusted = true;
            } elseif ($vote->isUntrusted()) {
                $this->logger->debug("Voter {class} voted 'untrusted'.", ['class' => get_class($voter), 'reason' => $vote->getReason()]);
                throw UntrustedException::createWithReason($vote->getReason());
            } elseif ($vote->isAbstain()) {
                $this->logger->debug('Voter {class} abstained from voting.', ['class' => get_class($voter)]);
            } else {
                throw new WebAuthnException('Unsupported vote type.');
            }
        }

        if (!$trusted) {
            $this->logger->debug('No voter trusted the registration.');
            throw UntrustedException::createWithReason('No voter trusted the registration.');
        }
    }
}
