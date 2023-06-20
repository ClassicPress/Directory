<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationObject;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\DataValidationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\DataValidator;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\X509Certificate;

class FidoU2fAttestationStatement extends AbstractAttestationStatement
{
    public const FORMAT_ID = 'fido-u2f';

    /**
     * @var ByteBuffer
     */
    private $signature;

    /**
     * @var X509Certificate[]
     */
    private $certificates;

    public function __construct(AttestationObject $attestationObject)
    {
        parent::__construct($attestationObject, self::FORMAT_ID);

        $statement = $attestationObject->getStatement();

        try {
            DataValidator::checkMap(
                $statement,
                [
                    'x5c' => 'array',
                    'sig' => ByteBuffer::class,
                ]
            );
        } catch (DataValidationException $e) {
            throw new ParseException('Invalid FIDO U2F attestation statement.', 0, $e);
        }

        $sig = $statement->get('sig');
        $x5c = $statement->get('x5c');

        $this->signature = $sig;
        $this->certificates = $this->buildPEMCertificateArray($x5c);
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
