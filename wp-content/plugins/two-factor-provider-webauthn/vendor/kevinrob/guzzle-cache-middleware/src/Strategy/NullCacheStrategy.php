<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Kevinrob\GuzzleCache\Strategy;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Kevinrob\GuzzleCache\CacheEntry;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Http\Message\RequestInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Http\Message\ResponseInterface;

class NullCacheStrategy implements CacheStrategyInterface
{

    /**
     * @inheritDoc
     */
    public function fetch(RequestInterface $request)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function cache(RequestInterface $request, ResponseInterface $response)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function update(RequestInterface $request, ResponseInterface $response)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RequestInterface $request)
    {
        return true;
    }
}
