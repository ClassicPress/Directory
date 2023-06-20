<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\AppId;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ExtensionException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\AbstractExtension;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionInputInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionOutputInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionProcessingContext;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionResponseInterface;

class AppIdExtension extends AbstractExtension
{
    public function __construct()
    {
        parent::__construct('appid');
    }

    public function parseResponse(ExtensionResponseInterface $extensionResponse): ExtensionOutputInterface
    {
        $extensionOutput = $extensionResponse->getClientExtensionOutput();
        if (!is_bool($extensionOutput)) {
            throw new ParseException('Expecting boolean value in appid extension output.');
        }

        return new AppIdExtensionOutput($extensionOutput);
    }

    public function processExtension(ExtensionInputInterface $input, ExtensionOutputInterface $output, ExtensionProcessingContext $context): void
    {
        if (!$input instanceof AppIdExtensionInput) {
            throw new ExtensionException('Expecting appid extension input to be AppIdExtensionInput.');
        }
        if (!$output instanceof AppIdExtensionOutput) {
            throw new ExtensionException('Expecting appid extension output to be AppIdExtensionOutput.');
        }
        // SPEC: Client extension output: If true, the AppID was used and thus, when verifying an assertion,
        // the Relying Party MUST expect the rpIdHash to be the hash of the AppID, not the RP ID.
        if ($output->getAppIdUsed()) {
            $context->setOverruledRpId($input->getAppId());
        }
    }
}
