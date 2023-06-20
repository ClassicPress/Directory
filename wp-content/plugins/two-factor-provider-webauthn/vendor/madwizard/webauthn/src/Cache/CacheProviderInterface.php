<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Cache;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Cache\CacheItemPoolInterface;

interface CacheProviderInterface
{
    public function getCachePool(string $scope): CacheItemPoolInterface;
}
