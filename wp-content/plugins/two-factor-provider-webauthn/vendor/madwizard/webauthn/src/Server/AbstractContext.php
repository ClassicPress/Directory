<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionInputInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\SerializableTrait;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Web\Origin;

abstract class AbstractContext
{
    use SerializableTrait;

    /**
     * @var ByteBuffer
     */
    private $challenge;

    /**
     * @var string
     */
    private $rpId;

    /**
     * @var bool
     */
    private $userVerificationRequired = false;

    /**
     * @var bool
     */
    private $userPresenceRequired = true;

    /**
     * @var Origin
     */
    private $origin;

    /**
     * @var ExtensionInputInterface[]
     */
    private $extensionInputs = [];

    public function __construct(ByteBuffer $challenge, Origin $origin, string $rpId)
    {
        $this->challenge = $challenge;
        $this->origin = $origin;
        $this->rpId = $rpId;
    }

    public function getChallenge(): ByteBuffer
    {
        return $this->challenge;
    }

    public function getRpId(): string
    {
        return $this->rpId;
    }

    public function isUserVerificationRequired(): bool
    {
        return $this->userVerificationRequired;
    }

    public function setUserVerificationRequired(bool $required): void
    {
        $this->userVerificationRequired = $required;
    }

    public function isUserPresenceRequired(): bool
    {
        return $this->userPresenceRequired;
    }

    public function setUserPresenceRequired(bool $required): void
    {
        $this->userPresenceRequired = $required;
    }

    public function getOrigin(): Origin
    {
        return $this->origin;
    }

    public function addExtensionInput(ExtensionInputInterface $input): void
    {
        $this->extensionInputs[] = $input;
    }

    /**
     * @return ExtensionInputInterface[]
     */
    public function getExtensionInputs(): array
    {
        return $this->extensionInputs;
    }

    public function __serialize(): array
    {
        return [
            'challenge' => $this->challenge,
            'rpId' => $this->rpId,
            'userVerificationRequired' => $this->userVerificationRequired,
            'origin' => $this->origin,
            'userPresenceRequired' => $this->userPresenceRequired,
            'extensionInputs' => $this->extensionInputs,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->challenge = $data['challenge'];
        $this->rpId = $data['rpId'];
        $this->userVerificationRequired = $data['userVerificationRequired'];
        $this->origin = $data['origin'];
        $this->userPresenceRequired = $data['userPresenceRequired'];
        $this->extensionInputs = $data['extensionInputs'];
    }
}
