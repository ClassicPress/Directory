<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

interface AuthenticatorAssertionResponseInterface extends AuthenticatorResponseInterface
{
    public function getAuthenticatorData(): ByteBuffer;

    public function getSignature(): ByteBuffer;

    public function getUserHandle(): ?ByteBuffer;
}
