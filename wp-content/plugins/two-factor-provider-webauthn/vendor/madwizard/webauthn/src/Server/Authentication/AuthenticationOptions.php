<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\CredentialId;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\UserHandle;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\UserVerificationRequirement;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\AuthenticationExtensionInputInterface;

final class AuthenticationOptions
{
    /**
     * @var CredentialId[]
     */
    private $allowCredentials = [];

    /**
     * @var string|null
     */
    private $userVerification;

    /**
     * @var int|null
     */
    private $timeout;

    /**
     * User handle to load credentials from, or null for client side discoverable.
     *
     * @var UserHandle|null
     */
    private $userHandle;

    /**
     * @var AuthenticationExtensionInputInterface[]
     */
    private $extensions = [];

    private function __construct(?UserHandle $userHandle)
    {
        $this->userHandle = $userHandle;
    }

    public static function createForUser(UserHandle $userHandle): self
    {
        return new self($userHandle);
    }

    public static function createForAnyUser(): self
    {
        return new self(null);
    }

    public function getUserHandle(): ?UserHandle
    {
        return $this->userHandle;
    }

    public function addAllowCredential(CredentialId $credential): void
    {
        $this->allowCredentials[] = $credential;
    }

    /**
     * @return CredentialId[]
     */
    public function getAllowCredentials(): array
    {
        return $this->allowCredentials;
    }

    public function getUserVerification(): ?string
    {
        return $this->userVerification;
    }

    public function setUserVerification(?string $value): void
    {
        if ($value !== null && !UserVerificationRequirement::isValidValue($value)) {
            throw new WebAuthnException(sprintf('Value %s is not a valid UserVerificationRequirement', $value));
        }

        $this->userVerification = $value;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    public function setTimeout(?int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function addExtensionInput(AuthenticationExtensionInputInterface $input): void
    {
        $this->extensions[] = $input;
    }

    /**
     * @return AuthenticationExtensionInputInterface[]
     */
    public function getExtensionInputs(): array
    {
        return $this->extensions;
    }
}
