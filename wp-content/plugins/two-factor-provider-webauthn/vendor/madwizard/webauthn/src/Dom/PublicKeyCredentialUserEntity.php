<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\UserHandle;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

final class PublicKeyCredentialUserEntity extends PublicKeyCredentialEntity
{
    /**
     * @var ByteBuffer Binary user handle of the account (max MAX_USER_HANDLE_BYTES)
     */
    private $id;

    /**
     * @var string The user handle of the user account entity
     */
    private $displayName;

    /**
     * PublicKeyCredentialUserEntity constructor.
     *
     * @param ByteBuffer $id          The user handle of the user account entity (max length MAX_USER_HANDLE_BYTES)
     * @param string     $displayName A human-friendly name for the user account. Used for display purposes only, may be truncated by authenticators if too long
     *
     * @throws WebAuthnException
     */
    public function __construct(string $name, ByteBuffer $id, string $displayName)
    {
        parent::__construct($name);
        $this->setId($id);
        $this->displayName = $displayName;
    }

    public function getId(): ByteBuffer
    {
        return $this->id;
    }

    private function setId(ByteBuffer $id): void
    {
        if ($id->isEmpty()) {
            throw new WebAuthnException('User handle cannot be empty.');
        }

        if ($id->getLength() > UserHandle::MAX_USER_HANDLE_BYTES) {
            throw new WebAuthnException(sprintf('User handle cannot be larger than %d bytes.', UserHandle::MAX_USER_HANDLE_BYTES));
        }
        $this->id = $id;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getAsArray(): array
    {
        return array_merge(
            parent::getAsArray(),
            [
                'id' => $this->id,
                'displayName' => $this->displayName,
            ]
        );
    }
}
