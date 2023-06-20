<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X501\ASN1\AttributeValue\Feature;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Element;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Primitive\PrintableString;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\UnspecifiedType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X501\ASN1\AttributeValue\AttributeValue;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X501\DN\DNParser;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X501\MatchingRule\CaseIgnoreMatch;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X501\MatchingRule\MatchingRule;

/**
 * Base class for attribute values having *PrintableString* syntax.
 */
abstract class PrintableStringValue extends AttributeValue
{
    /**
     * String value.
     *
     * @var string
     */
    protected $_string;

    /**
     * Constructor.
     *
     * @param string $value String value
     */
    public function __construct(string $value)
    {
        $this->_string = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public static function fromASN1(UnspecifiedType $el): AttributeValue
    {
        return new static($el->asPrintableString()->string());
    }

    /**
     * {@inheritdoc}
     */
    public function toASN1(): Element
    {
        return new PrintableString($this->_string);
    }

    /**
     * {@inheritdoc}
     */
    public function stringValue(): string
    {
        return $this->_string;
    }

    /**
     * {@inheritdoc}
     */
    public function equalityMatchingRule(): MatchingRule
    {
        // default to caseIgnoreMatch
        return new CaseIgnoreMatch(Element::TYPE_PRINTABLE_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function rfc2253String(): string
    {
        return DNParser::escapeString($this->_transcodedString());
    }

    /**
     * {@inheritdoc}
     */
    protected function _transcodedString(): string
    {
        // PrintableString maps directly to UTF-8
        return $this->_string;
    }
}
