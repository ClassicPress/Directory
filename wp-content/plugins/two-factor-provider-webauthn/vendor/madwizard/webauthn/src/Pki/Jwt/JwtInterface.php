<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\Jwt;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

interface JwtInterface
{
    public const ES_AND_RSA = ['ES256', 'ES384', 'ES512', 'RS256', 'RS384', 'RS512'];

    public function getHeader(): array;

    public function getBody(): array;

    public function getSignedData(): ByteBuffer;

    public function getSignature(): ByteBuffer;
}
