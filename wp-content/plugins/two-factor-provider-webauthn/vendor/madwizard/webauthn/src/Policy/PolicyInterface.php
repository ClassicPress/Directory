<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\CoseAlgorithm;

interface PolicyInterface
{
    public function isUserPresenceRequired(): bool;

    public function getChallengeLength(): int;

    /**
     * @return int[]
     *
     * @see CoseAlgorithm
     */
    public function getAllowedAlgorithms(): array;
}
