<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Tpm;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\UnsupportedException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

/**
 * Represents TPMT_PUBLIC structure.
 */
final class TpmPublic
{
    use TpmStructureTrait;

    public const TPM_ALG_RSA = 0x0001;

    public const TPM_ALG_ECC = 0x0023;

    public const TPM_ALG_NULL = 0x0010;

    public const TPM_ALG_SHA = 0x0004; // ISO/IEC 10118-3 the SHA1 algorithm

    public const TPM_ALG_SHA1 = 0x0004; // ISO/IEC 10118-3 redefinition for documentation consistency TPM

    public const TPM_ALG_SHA256 = 0x000B; // ISO/IEC 10118-3 the SHA 256 algorithm

    public const TPM_ALG_SHA384 = 0x000C; //  ISO/IEC 10118-3 the SHA 384 algorithm

    public const TPM_ALG_SHA512 = 0x000D; // ISO/IEC 10118-3 the SHA 512 algorithm

    private const PHP_HASH_ALG_MAP = [
        self::TPM_ALG_SHA1 => 'sha1',
        self::TPM_ALG_SHA256 => 'sha256',
        self::TPM_ALG_SHA384 => 'sha384',
        self::TPM_ALG_SHA512 => 'sha512',
    ];

    /**
     * @var int
     */
    private $type;

    /**
     * @var int
     */
    private $objectAttributes;

    /**
     * @var int
     */
    private $nameAlg;

    /**
     * @var string
     */
    private $rawData;

    /**
     * @var KeyParametersInterface
     */
    private $parameters;

    /**
     * @var KeyPublicIdInterface
     */
    private $unique;

    public function __construct(ByteBuffer $data)
    {
        $this->rawData = $data->getBinaryString();
        $this->type = $data->getUint16Val(0);

        $this->nameAlg = $data->getUint16Val(2);
        $this->objectAttributes = $data->getUint32Val(4);

        $offset = 8;

        // Auth policy
        self::readLengthPrefixed($data, $offset);

        $this->parameters = $this->parseParameters($this->type, $data, $offset);
        $this->unique = $this->parseUnique($this->type, $data, $offset);

        if ($offset !== $data->getLength()) {
            throw new ParseException('Unexpected bytes after TPMT_PUBLIC structure.');
        }
    }

    private function parseParameters(int $type, ByteBuffer $buffer, int &$offset): KeyParametersInterface
    {
        if ($type === self::TPM_ALG_RSA) {
            $parameters = TpmRsaParameters::parse($buffer, $offset, $endOffset);
            $offset = $endOffset;
            return $parameters;
        }
        if ($type === self::TPM_ALG_ECC) {
            $parameters = TpmEccParameters::parse($buffer, $offset, $endOffset);
            $offset = $endOffset;
            return $parameters;
        }
        throw new UnsupportedException(sprintf('TPM public key type %d is not supported.', $type));
    }

    private function parseUnique(int $type, ByteBuffer $buffer, int &$offset): KeyPublicIdInterface
    {
        if ($type === self::TPM_ALG_RSA) {
            $unique = TpmRsaPublicId::parse($buffer, $offset, $endOffset);
            $offset = $endOffset;
            return $unique;
        }
        if ($type === self::TPM_ALG_ECC) {
            $unique = TpmEccPublicId::parse($buffer, $offset, $endOffset);
            $offset = $endOffset;
            return $unique;
        }
        throw new UnsupportedException(sprintf('TPM public key type %d is not supported.', $type));
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getObjectAttributes(): int
    {
        return $this->objectAttributes;
    }

    public function getParameters(): KeyParametersInterface
    {
        return $this->parameters;
    }

    public function getNameAlg(): int
    {
        return $this->nameAlg;
    }

    public function getUnique(): KeyPublicIdInterface
    {
        return $this->unique;
    }

    public function isValidPubInfoName(ByteBuffer $name): bool
    {
        // TPM names are prefixed by a 16-bit integer representing the hashing algorithm
        $algo = $name->getUint16Val(0);

        // The name itself is a concatenation of the algorithm and a hash of this whole structure.
        $phpAlgo = self::PHP_HASH_ALG_MAP[$algo] ?? null;
        if ($phpAlgo === null) {
            throw new UnsupportedException(sprintf('Algorithm 0x%04X is not allowed for names for TPMT_PUBLIC.', $algo));
        }

        $validName = pack('n', $algo) . hash($phpAlgo, $this->rawData, true);
        return \hash_equals($validName, $name->getBinaryString());
    }
}
