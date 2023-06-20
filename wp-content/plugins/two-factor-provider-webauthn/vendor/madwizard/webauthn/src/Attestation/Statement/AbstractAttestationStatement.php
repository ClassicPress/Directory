<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationObject;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\X509Certificate;

abstract class AbstractAttestationStatement implements AttestationStatementInterface
{
    /**
     * @var string
     */
    private $formatId;

    public function __construct(AttestationObject $attestationObject, string $formatId)
    {
        $actualFormat = $attestationObject->getFormat();
        if ($actualFormat !== $formatId) {
            throw new ParseException(sprintf("Not expecting format '%s' but '%s'.", $actualFormat, $formatId));
        }
        $this->formatId = $formatId;
    }

    /**
     * @param ByteBuffer[] $x5c
     *
     * @return X509Certificate[]
     *
     * @throws ParseException
     */
    protected function buildPEMCertificateArray(array $x5c): array
    {
        $certificates = [];
        foreach ($x5c as $item) {
            if (!($item instanceof ByteBuffer)) {
                throw new ParseException('x5c should be array of binary data elements.');
            }
            $certificates[] = X509Certificate::fromDer($item->getBinaryString());
        }
        return $certificates;
    }

    public function getFormatId(): string
    {
        return $this->formatId;
    }
}
