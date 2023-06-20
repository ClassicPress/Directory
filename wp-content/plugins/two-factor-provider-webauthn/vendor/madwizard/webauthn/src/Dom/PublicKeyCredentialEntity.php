<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ConfigurationException;

class PublicKeyCredentialEntity extends AbstractDictionary
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $icon;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): void
    {
        // TODO: FILTER_VALIDATE_URL does not allow data urls
//        if ($icon !== null && filter_var($icon, FILTER_VALIDATE_URL) === false) {
//            throw new ConfigurationException("Invalid relying party icon url.");
//        }
        $this->icon = $icon;
    }

    public function getAsArray(): array
    {
        return  self::removeNullValues([
            'name' => $this->name,
            'icon' => $this->icon,
        ]);
    }
}
