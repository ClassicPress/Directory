<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X501\ASN1\AttributeValue;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X501\ASN1\AttributeType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X501\ASN1\AttributeValue\Feature\PrintableStringValue;

/**
 * 'serialNumber' attribute value.
 *
 * @see https://www.itu.int/ITU-T/formal-language/itu-t/x/x520/2012/SelectedAttributeTypes.html#SelectedAttributeTypes.serialNumber
 */
class SerialNumberValue extends PrintableStringValue
{
    /**
     * Constructor.
     *
     * @param string $value String value
     */
    public function __construct(string $value)
    {
        $this->_oid = AttributeType::OID_SERIAL_NUMBER;
        parent::__construct($value);
    }
}
