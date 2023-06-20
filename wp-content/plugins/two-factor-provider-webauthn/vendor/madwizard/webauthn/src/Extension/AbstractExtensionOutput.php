<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use function sprintf;

abstract class AbstractExtensionOutput implements ExtensionOutputInterface
{
    /**
     * @var string
     */
    private $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;

        if (!ExtensionHelper::validExtensionIdentifier($identifier)) {
            throw new WebAuthnException(sprintf("Invalid extension identifier '%s'.", $identifier));
        }
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
