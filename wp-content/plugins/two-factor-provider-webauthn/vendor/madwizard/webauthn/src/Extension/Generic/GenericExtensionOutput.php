<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\Generic;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\AbstractExtensionOutput;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionResponseInterface;

class GenericExtensionOutput extends AbstractExtensionOutput
{
    /**
     * @var ExtensionResponseInterface
     */
    private $response;

    public function __construct(ExtensionResponseInterface $response)
    {
        parent::__construct($response->getIdentifier());
        $this->response = $response;
    }

    public function getResponse(): ExtensionResponseInterface
    {
        return $this->response;
    }
}
