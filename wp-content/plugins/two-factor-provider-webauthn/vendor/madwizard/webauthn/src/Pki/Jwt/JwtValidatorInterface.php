<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\Jwt;

interface JwtValidatorInterface
{
    public function validate(JwtInterface $token, ValidationContext $context): array;
}
