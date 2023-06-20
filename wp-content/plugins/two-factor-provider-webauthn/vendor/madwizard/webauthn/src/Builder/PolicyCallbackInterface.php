<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Builder;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy\Policy;

interface PolicyCallbackInterface
{
    /**
     * @return void
     */
    public function __invoke(Policy $policy);
}
