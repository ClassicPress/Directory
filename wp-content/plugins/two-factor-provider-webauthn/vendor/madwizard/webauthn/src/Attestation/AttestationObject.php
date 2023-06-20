<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\CborException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\CborDecoder;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\CborMap;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\DataValidator;

final class AttestationObject
{
    /**
     * @var string
     */
    private $format;

    /**
     * @var CborMap
     */
    private $statement;

    /**
     * @var ByteBuffer
     */
    private $authData;

    public function __construct(string $format, CborMap $statement, ByteBuffer $authData)
    {
        $this->format = $format;
        $this->statement = $statement;
        $this->authData = $authData;
    }

    public static function parse(ByteBuffer $buffer): self
    {
        try {
            $data = CborDecoder::decode($buffer);
            if (!$data instanceof CborMap) {
                throw new WebAuthnException('Expecting attestation object to be a CBOR map.');
            }

            DataValidator::checkMap(
                $data,
                [
                    'fmt' => 'string',
                    'attStmt' => CborMap::class,
                    'authData' => ByteBuffer::class,
                ]
            );
            return new self($data->get('fmt'), $data->get('attStmt'), $data->get('authData'));
        } catch (CborException $e) {
            throw new ParseException('Failed to parse CBOR attestation object.', 0, $e);
        }
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getStatement(): CborMap
    {
        return $this->statement;
    }

    public function getAuthenticatorData(): ByteBuffer
    {
        return $this->authData;
    }
}
