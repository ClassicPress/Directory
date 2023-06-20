<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X509\CertificationPath\Exception;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X509\Exception\X509ValidationException;

/**
 * Exception thrown on certification path validation errors.
 */
class PathValidationException extends X509ValidationException
{
}
