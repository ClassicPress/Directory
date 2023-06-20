<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Kevinrob\GuzzleCache\Strategy\Delegate;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Http\Message\RequestInterface;

interface RequestMatcherInterface
{

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function matches(RequestInterface $request);
}
