<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension;

interface ExtensionRegistryInterface
{
    public function getExtension(string $extensionId): ExtensionInterface;
}
