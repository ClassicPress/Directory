<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationObject;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\CoseAlgorithm;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\DataValidationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\DataValidator;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\X509Certificate;

class AndroidKeyAttestationStatement extends AbstractAttestationStatement
{
    public const FORMAT_ID = 'android-key';

    /**
     * @var ByteBuffer
     */
    private $signature;

    /**
     * @var X509Certificate[]
     */
    private $certificates;

    /**
     * @see CoseAlgorithm enumeration
     *
     * @var int
     */
    private $algorithm;

    public function __construct(AttestationObject $attestationObject)
    {
        parent::__construct($attestationObject, self::FORMAT_ID);

        $statement = $attestationObject->getStatement();

        try {
            DataValidator::checkMap(
                $statement,
                [
                    'alg' => 'integer',
                    'x5c' => 'array',
                    'sig' => ByteBuffer::class,
                ]
            );
        } catch (DataValidationException $e) {
            throw new ParseException('Invalid Android key attestation statement.', 0, $e);
        }

        $this->signature = $statement->get('sig');
        $this->algorithm = $statement->get('alg');
        $this->certificates = $this->buildPEMCertificateArray($statement->get('x5c'));
    }

    public function getAlgorithm(): int
    {
        return $this->algorithm;
    }

    public function getSignature(): ByteBuffer
    {
        return $this->signature;
    }

    /**
     * @return X509Certificate[]
     */
    public function getCertificates(): array
    {
        return $this->certificates;
    }
}
