<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom;

use InvalidArgumentException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

final class PublicKeyCredentialCreationOptions extends AbstractDictionary
{
    /**
     * @var PublicKeyCredentialRpEntity
     */
    private $rp;

    /**
     * @var PublicKeyCredentialUserEntity
     */
    private $user;

    /**
     * @var ByteBuffer
     */
    private $challenge;

    /**
     * @var PublicKeyCredentialParameters[]
     */
    private $pubKeyCredParams;

    /**
     * @var int|null
     */
    private $timeout;

    /**
     * @var PublicKeyCredentialDescriptor[]|null
     */
    private $excludeCredentials;

    /**
     * @var AuthenticatorSelectionCriteria|null
     */
    private $authenticatorSelection;

    /**
     * @var string|null
     */
    private $attestation;

    /**
     * @var AuthenticationExtensionsClientInputs|null
     */
    private $extensions;

    /**
     * PublicKeyCredentialCreationOptions constructor.
     *
     * @param PublicKeyCredentialParameters[] $pubKeyCredParams
     */
    public function __construct(PublicKeyCredentialRpEntity $rp, PublicKeyCredentialUserEntity $user, ByteBuffer $challenge, array $pubKeyCredParams)
    {
        $this->rp = $rp;
        $this->user = $user;
        $this->challenge = $challenge;
        $this->pubKeyCredParams = $pubKeyCredParams;
    }

    public function getAsArray(): array
    {
        $map = [
            'rp' => $this->rp,
            'user' => $this->user,
            'challenge' => $this->challenge,
            'pubKeyCredParams' => $this->pubKeyCredParams,
        ];

        $map = array_merge(
            $map,
            self::removeNullValues(
                [
                    'timeout' => $this->timeout,
                    'excludeCredentials' => $this->excludeCredentials,
                    'authenticatorSelection' => $this->authenticatorSelection,
                    'attestation' => $this->attestation,
                    'extensions' => $this->extensions,
                ]
            )
        );

        return $map;
    }

    public function getAttestation(): ?string
    {
        return $this->attestation;
    }

    public function setAttestation(?string $attestation): void
    {
        if ($attestation !== null && !AttestationConveyancePreference::isValidValue($attestation)) {
            throw new InvalidArgumentException(sprintf("String '%s' is not a valid attestation preference.", $attestation));
        }
        $this->attestation = $attestation;
    }

    /**
     * @return AuthenticatorSelectionCriteria
     */
    public function getAuthenticatorSelection(): ?AuthenticatorSelectionCriteria
    {
        return $this->authenticatorSelection;
    }

    /**
     * @param AuthenticatorSelectionCriteria $authenticatorSelection
     */
    public function setAuthenticatorSelection(?AuthenticatorSelectionCriteria $authenticatorSelection): void
    {
        $this->authenticatorSelection = $authenticatorSelection;
    }

    public function getRpEntity(): PublicKeyCredentialRpEntity
    {
        return $this->rp;
    }

    public function getUserEntity(): PublicKeyCredentialUserEntity
    {
        return $this->user;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    /**
     * @return PublicKeyCredentialDescriptor[]
     */
    public function getExcludeCredentials(): ?array
    {
        return $this->excludeCredentials;
    }

    public function addExcludeCredential(PublicKeyCredentialDescriptor $descriptor): void
    {
        if ($this->excludeCredentials === null) {
            $this->excludeCredentials = [];
        }

        $this->excludeCredentials[] = $descriptor;
    }

    /**
     * @return PublicKeyCredentialParameters[]
     */
    public function getCredentialParameters(): ?array
    {
        return $this->pubKeyCredParams;
    }

    public function getChallenge(): ByteBuffer
    {
        return $this->challenge;
    }

    public function getExtensions(): ?AuthenticationExtensionsClientInputs
    {
        return $this->extensions;
    }

    public function setExtensions(?AuthenticationExtensionsClientInputs $extensions): void
    {
        $this->extensions = $extensions;
    }

    public function setTimeout(?int $timeout): void
    {
        $this->timeout = $timeout;
    }
}
