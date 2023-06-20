<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\CoseKeyInterface;

class UserCredential implements UserCredentialInterface
{
    /**
     * @var CredentialId
     */
    private $credentialId;

    /**
     * @var CoseKeyInterface
     */
    private $publicKey;

    /**
     * @var UserHandle
     */
    private $userHandle;

    public function __construct(CredentialId $credentialId, CoseKeyInterface $publicKey, UserHandle $userHandle)
    {
        $this->credentialId = $credentialId;
        $this->publicKey = $publicKey;
        $this->userHandle = $userHandle;
    }

    public function getCredentialId(): CredentialId
    {
        return $this->credentialId;
    }

    public function getPublicKey(): CoseKeyInterface
    {
        return $this->publicKey;
    }

    public function getUserHandle(): UserHandle
    {
        return $this->userHandle;
    }
}
