<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

interface AuthenticatorAttestationResponseInterface extends AuthenticatorResponseInterface
{
    public function getAttestationObject(): ByteBuffer;
}
