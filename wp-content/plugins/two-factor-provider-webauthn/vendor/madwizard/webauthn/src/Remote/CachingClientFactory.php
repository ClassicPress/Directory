<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Remote;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\GuzzleHttp\Client;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\GuzzleHttp\HandlerStack;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Kevinrob\GuzzleCache\CacheMiddleware;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Cache\CacheProviderInterface;

final class CachingClientFactory
{
    /**
     * @var CacheProviderInterface
     */
    private $cacheProvider;

    public function __construct(CacheProviderInterface $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    public function createClient(): Client
    {
        $stack = HandlerStack::create();

        $stack->push(
            new CacheMiddleware(
                new PrivateCacheStrategy(
                    new Psr6CacheStorage(
                        $this->cacheProvider->getCachePool('http')
                    )
                )
            )
        );

        return new Client(['handler' => $stack]);
    }
}
