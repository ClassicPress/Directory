<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\Generic;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\AbstractExtension;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionInputInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionOutputInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionProcessingContext;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionResponseInterface;

class GenericExtension extends AbstractExtension
{
    public function parseResponse(ExtensionResponseInterface $extensionResponse): ExtensionOutputInterface
    {
        return new GenericExtensionOutput($extensionResponse);
    }

    public function processExtension(ExtensionInputInterface $input, ExtensionOutputInterface $output, ExtensionProcessingContext $context): void
    {
        // No action
    }
}
