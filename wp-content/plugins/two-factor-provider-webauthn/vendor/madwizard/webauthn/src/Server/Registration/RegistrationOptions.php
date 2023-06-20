<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\AttestationConveyancePreference;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\AuthenticatorAttachment;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\ResidentKeyRequirement;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\UserVerificationRequirement;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ConfigurationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\UnexpectedValueException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\RegistrationExtensionInputInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\UserIdentityInterface;

final class RegistrationOptions     // TODO: add timeout (via trait?)
{
    /**
     * @var string|null
     */
    private $attestation;

    /**
     * @var UserIdentityInterface
     */
    private $user;

    /**
     * @var bool
     */
    private $excludeExistingCredentials = false;

    /**
     * @var RegistrationExtensionInputInterface[]
     */
    private $extensions = [];

    /**
     * @var int|null
     */
    private $timeout;

    /**
     * @var string|null
     *
     * @see ResidentKeyRequirement
     */
    private $residentKey;

    /**
     * @var string|null
     *
     * @see AuthenticatorAttachment
     */
    private $authenticatorAttachment;

    /**
     * @var string|null
     *
     * @see UserVerificationRequirement
     */
    private $userVerification;

    private function __construct(UserIdentityInterface $userIdentity)
    {
        $this->user = $userIdentity;
    }

    public static function createForUser(UserIdentityInterface $userIdentity): self
    {
        return new RegistrationOptions($userIdentity);
    }

    public function getUser(): UserIdentityInterface
    {
        return $this->user;
    }

    public function getAttestation(): ?string
    {
        return $this->attestation;
    }

    public function setAttestation(?string $attestation): void
    {
        if ($attestation !== null && !AttestationConveyancePreference::isValidValue($attestation)) {
            throw new ConfigurationException(sprintf('Value "%s" is not a valid attestation conveyance preference.', $attestation));
        }
        $this->attestation = $attestation;
    }

    public function getExcludeExistingCredentials(): bool
    {
        return $this->excludeExistingCredentials;
    }

    public function setExcludeExistingCredentials(bool $excludeExistingCredentials): void
    {
        $this->excludeExistingCredentials = $excludeExistingCredentials;
    }

    public function addExtensionInput(RegistrationExtensionInputInterface $input): void
    {
        $this->extensions[] = $input;
    }

    /**
     * @return RegistrationExtensionInputInterface[]
     */
    public function getExtensionInputs(): array
    {
        return $this->extensions;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    public function setTimeout(?int $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * @return string
     */
    public function getResidentKey(): ?string
    {
        return $this->residentKey;
    }

    /**
     * @param string $residentKey
     */
    public function setResidentKey(?string $residentKey): void
    {
        if ($residentKey !== null && !ResidentKeyRequirement::isValidValue($residentKey)) {
            throw new UnexpectedValueException('Invalid ResidentKeyRequirement value.');
        }
        $this->residentKey = $residentKey;
    }

    public function getAuthenticatorAttachment(): ?string
    {
        return $this->authenticatorAttachment;
    }

    public function setAuthenticatorAttachment(?string $authenticatorAttachment): void
    {
        if ($authenticatorAttachment !== null && !AuthenticatorAttachment::isValidValue($authenticatorAttachment)) {
            throw new UnexpectedValueException('Invalid AuthenticatorAttachment value.');
        }
        $this->authenticatorAttachment = $authenticatorAttachment;
    }

    public function getUserVerification(): ?string
    {
        return $this->userVerification;
    }

    public function setUserVerification(?string $userVerification): void
    {
        if ($userVerification !== null && !UserVerificationRequirement::isValidValue($userVerification)) {
            throw new UnexpectedValueException('Invalid UserVerificationRequirement value.');
        }
        $this->userVerification = $userVerification;
    }
}
