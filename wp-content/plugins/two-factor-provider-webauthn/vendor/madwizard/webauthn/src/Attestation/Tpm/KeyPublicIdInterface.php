<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Tpm;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

interface KeyPublicIdInterface
{
    public static function parse(ByteBuffer $buffer, int $offset, ?int &$endOffset): self;
}
