<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\CoseKeyInterface;

interface UserCredentialInterface
{
    public function getCredentialId(): CredentialId;

    public function getPublicKey(): CoseKeyInterface;

    public function getUserHandle(): UserHandle;
}
