<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\CredentialId;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\UserHandle;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\AbstractContext;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Web\Origin;
use Serializable;

final class AuthenticationContext extends AbstractContext implements Serializable
{
    /**
     * @var CredentialId[]
     */
    private $allowCredentialIds = [];

    /**
     * @var UserHandle|null
     */
    private $userHandle;

    public function __construct(ByteBuffer $challenge, Origin $origin, string $rpId, ?UserHandle $userHandle)
    {
        parent::__construct($challenge, $origin, $rpId);
        $this->userHandle = $userHandle;
    }

    public function addAllowCredentialId(CredentialId $credentialId): void
    {
        $this->allowCredentialIds[] = $credentialId;
    }

    public function getUserHandle(): ?UserHandle
    {
        return $this->userHandle;
    }

    /**
     * @return CredentialId[]
     */
    public function getAllowCredentialIds(): array
    {
        return $this->allowCredentialIds;
    }

    public function __serialize(): array
    {
        return [
            'parent' => parent::__serialize(),
            'userHandle' => $this->userHandle,
            'allowCredentialIds' => $this->allowCredentialIds,
        ];
    }

    public function __unserialize(array $data): void
    {
        parent::__unserialize($data['parent']);
        $this->userHandle = $data['userHandle'];
        $this->allowCredentialIds = $data['allowCredentialIds'];
    }
}
