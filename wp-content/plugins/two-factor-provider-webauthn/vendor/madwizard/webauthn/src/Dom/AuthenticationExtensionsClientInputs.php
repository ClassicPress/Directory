<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom;

// SPEC 5.7 This is a dictionary containing the client extension input values for zero or more WebAuthn extensions, as defined in §9 WebAuthn Extensions.

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionInputInterface;

final class AuthenticationExtensionsClientInputs extends AbstractDictionary
{
    /**
     * @var ExtensionInputInterface[]
     */
    private $inputs = [];

    public function __construct()
    {
    }

    public function addInput(ExtensionInputInterface $input): void
    {
        $this->inputs[] = $input;
    }

    public function getAsArray(): array
    {
        $map = [];
        foreach ($this->inputs as $input) {
            $map[$input->getIdentifier()] = $input->getInput();
        }
        return $map;
    }

    /**
     * @param ExtensionInputInterface[] $inputs
     *
     * @return AuthenticationExtensionsClientInputs
     */
    public static function fromArray(array $inputs): self
    {
        $obj = new AuthenticationExtensionsClientInputs();
        foreach ($inputs as $input) {
            $obj->addInput($input);
        }
        return $obj;
    }
}
