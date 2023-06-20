<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Tpm;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

final class TpmAttest
{
    use TpmStructureTrait;

    private const TPM_GENERATED = "\xFF\x54\x43\x47";

    public const TPM_ST_ATTEST_CERTIFY = 0x8017;

    /**
     * @var ByteBuffer
     */
    private $attName;

    /**
     * @var ByteBuffer
     */
    private $extraData;

    public function __construct(ByteBuffer $data)
    {
        // Read magic
        $magic = $data->getBytes(0, 4);
        if ($magic !== self::TPM_GENERATED) {
            throw new ParseException('Magic bytes of TPM attestation are not TPM_GENERATED sequence.');
        }

        // Read type
        $type = $data->getUint16Val(4);
        if ($type !== self::TPM_ST_ATTEST_CERTIFY) {
            throw new ParseException(sprintf('Wrong type for TPMS_ATTEST structure, expecting TPM_ST_ATTEST_CERTIFY, not 0x%04Xd.', $type));
        }
        //$this->objectAttributes = $data->getUint32Val(6);

        $offset = 6;

        // qualifiedSigner
        self::readLengthPrefixed($data, $offset);

        // Extra data
        $this->extraData = self::readLengthPrefixed($data, $offset);

        // Clock info
        self::readFixed($data, $offset, 17);

        // Firmware version
        self::readFixed($data, $offset, 8);

        // Attested name
        $this->attName = self::readLengthPrefixed($data, $offset);

        // Attested qualified name
        self::readLengthPrefixed($data, $offset);

        if ($offset !== $data->getLength()) {
            throw new ParseException('Unexpected bytes after TPMS_ATTEST structure.');
        }
    }

    public function getAttName(): ByteBuffer
    {
        return $this->attName;
    }

    public function getExtraData(): ByteBuffer
    {
        return $this->extraData;
    }
}
