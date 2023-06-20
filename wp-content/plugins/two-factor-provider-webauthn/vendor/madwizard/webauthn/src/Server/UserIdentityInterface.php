<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\UserHandle;

interface UserIdentityInterface
{
    public function getUserHandle(): UserHandle;

    public function getUsername(): string;

    public function getDisplayName(): string;
}
