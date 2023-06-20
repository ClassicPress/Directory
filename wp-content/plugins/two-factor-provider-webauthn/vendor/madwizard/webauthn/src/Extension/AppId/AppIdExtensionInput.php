<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\AppId;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\AbstractExtensionInput;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\AuthenticationExtensionInputInterface;

class AppIdExtensionInput extends AbstractExtensionInput implements AuthenticationExtensionInputInterface
{
    public function __construct(string $appId)
    {
        parent::__construct('appid');
        $this->input = $appId;
    }

    public function getAppId(): string
    {
        return $this->input;
    }
}
