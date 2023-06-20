<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\Generic;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\AbstractExtensionInput;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\AuthenticationExtensionInputInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\RegistrationExtensionInputInterface;

class GenericExtensionInput extends AbstractExtensionInput implements RegistrationExtensionInputInterface, AuthenticationExtensionInputInterface
{
    /**
     * GenericExtensionInput constructor.
     *
     * @param mixed $input
     *
     * @throws \MadWizard\WebAuthn\Exception\WebAuthnException
     */
    public function __construct(string $identifier, $input = null)
    {
        parent::__construct($identifier);
        $this->input = $input;
    }

    /**
     * @param mixed $input
     */
    public function setInput($input): void
    {
        $this->input = $input;
    }
}
