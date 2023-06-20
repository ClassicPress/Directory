<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Identifier;

interface IdentifierInterface
{
    public function getType(): string;

    public function toString(): string;

    public function equals(IdentifierInterface $identifier): bool;
}
