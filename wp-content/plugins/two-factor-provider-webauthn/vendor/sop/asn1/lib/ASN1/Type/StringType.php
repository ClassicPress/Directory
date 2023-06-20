<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Feature\ElementBase;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Feature\Stringable;

/**
 * Interface to mark types that correspond to ASN.1 specification's
 * character strings. That being all simple strings and time types.
 */
interface StringType extends ElementBase, Stringable
{
}
