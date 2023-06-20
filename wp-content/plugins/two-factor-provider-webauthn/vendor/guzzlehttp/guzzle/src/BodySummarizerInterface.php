<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\GuzzleHttp;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Http\Message\MessageInterface;

interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string;
}
