<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X509\GeneralName;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Primitive\IA5String;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\Tagged\ImplicitlyTaggedType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\TaggedType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Type\UnspecifiedType;

/**
 * Implements *rfc822Name* CHOICE type of *GeneralName*.
 *
 * @see https://tools.ietf.org/html/rfc5280#section-4.2.1.6
 */
class RFC822Name extends GeneralName
{
    /**
     * Email.
     *
     * @var string
     */
    protected $_email;

    /**
     * Constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->_tag = self::TAG_RFC822_NAME;
        $this->_email = $email;
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public static function fromChosenASN1(UnspecifiedType $el): GeneralName
    {
        return new self($el->asIA5String()->string());
    }

    /**
     * {@inheritdoc}
     */
    public function string(): string
    {
        return $this->_email;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function email(): string
    {
        return $this->_email;
    }

    /**
     * {@inheritdoc}
     */
    protected function _choiceASN1(): TaggedType
    {
        return new ImplicitlyTaggedType($this->_tag, new IA5String($this->_email));
    }
}
