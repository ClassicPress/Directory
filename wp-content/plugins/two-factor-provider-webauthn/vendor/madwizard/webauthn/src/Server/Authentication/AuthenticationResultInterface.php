<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\UserCredentialInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\UserHandle;

interface AuthenticationResultInterface
{
    public function getUserCredential(): UserCredentialInterface;

    public function getUserHandle(): UserHandle;
}
