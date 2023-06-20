<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Cache;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Cache\CacheItemPoolInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class FileCacheProvider implements CacheProviderInterface
{
    /**
     * @var string
     */
    private $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function getCachePool(string $scope): CacheItemPoolInterface
    {
        return new FilesystemAdapter($scope, 0, $this->cacheDir);
    }
}
