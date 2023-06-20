<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Config;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Web\Origin;

interface RelyingPartyInterface
{
    public function getName(): string;

    public function getOrigin(): Origin;

    /**
     * Returns the configured RelyingParty.
     */
    public function getId(): ?string;

    public function getEffectiveId(): string;

    public function getIconUrl(): ?string;
}
