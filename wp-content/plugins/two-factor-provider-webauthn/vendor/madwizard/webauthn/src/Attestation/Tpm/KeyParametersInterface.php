<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Tpm;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

interface KeyParametersInterface
{
    public function getAlgorithm(): int;

    public static function parse(ByteBuffer $buffer, int $offset, ?int &$endOffset): KeyParametersInterface;
}
