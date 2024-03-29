<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Identifier\Aaguid;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\CoseKey;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\CoseKeyInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ByteBufferException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\CborException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\DataValidationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\NotAvailableException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\CborDecoder;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\CborMap;

final class AuthenticatorData
{
    /**
     * User present (UP).
     */
    private const FLAG_UP = 1 << 0;

    /**
     * User verified (UV).
     */
    private const FLAG_UV = 1 << 2;

    /**
     * Attested credential data included (AT).
     */
    private const FLAG_AT = 1 << 6;

    /**
     * Extension data included (ED).
     */
    private const FLAG_ED = 1 << 7;

    /**
     * SHA-256 hash of the RP ID associated with the credential.
     *
     * @var ByteBuffer
     */
    private $rpIdHash;

    /**
     * @var int FLAG_* flags
     */
    private $flags;

    /**
     * @var int
     */
    private $signCount;

    /**
     * @var CoseKeyInterface|null
     */
    private $key;

    /**
     * @var ByteBuffer|null
     */
    private $credentialId;

    /**
     * @var ByteBuffer|null
     */
    private $aaguid;

    /**
     * @var ByteBuffer
     */
    private $raw;

    /**
     * @var CborMap|null Authenticator extension output
     */
    private $extensionData;

    private const LENGTH_RP_ID_HASH = 32;

    private const LENGTH_AAGUID = 16;

    /**
     * AuthenticatorData constructor.
     *
     * @throws ParseException
     * @throws DataValidationException
     */
    public function __construct(ByteBuffer $data)
    {
        $this->raw = $data;
        $offset = 0;

        try {
            $this->rpIdHash = new ByteBuffer($data->getBytes(0, self::LENGTH_RP_ID_HASH));
            $offset += self::LENGTH_RP_ID_HASH;

            $this->flags = $data->getByteVal($offset);
            $offset++;
            $this->signCount = $data->getUint32Val($offset);
            $offset += 4;

            if ($this->hasAttestedCredentialData()) {
                $this->aaguid = new ByteBuffer($data->getBytes($offset, self::LENGTH_AAGUID));
                $offset += self::LENGTH_AAGUID;
                $credentialIdLength = $data->getUint16Val($offset);
                $offset += 2;
                $this->credentialId = new ByteBuffer($data->getBytes($offset, $credentialIdLength));
                $offset += $credentialIdLength;
                $this->key = CoseKey::parseCbor($data, $offset, $endOffset);
                $offset = $endOffset;
            }

            if ($this->hasExtensionData()) {
                $extensionData = CborDecoder::decodeInPlace($data, $offset, $endOffset);
                $offset = $endOffset;
                if (!$extensionData instanceof CborMap) {
                    throw new ParseException('Expected CBOR map for extension data in authenticator data.');
                }
                $this->extensionData = $extensionData;
            }
            if ($offset !== $data->getLength()) {
                throw new ParseException('Unexpected bytes at end of AuthenticatorData.');
            }
        } catch (ByteBufferException $e) {
            throw new ParseException('Failed to parse authenticator data buffer.', 0, $e);
        } catch (CborException $e) {
            throw new ParseException('Failed to parse CBOR authenticator data.', 0, $e);
        }
    }

    public function getRpIdHash(): ByteBuffer
    {
        return $this->rpIdHash;
    }

    public function getSignCount(): int
    {
        return $this->signCount;
    }

    public function isUserPresent(): bool
    {
        return ($this->flags & self::FLAG_UP) !== 0;
    }

    public function isUserVerified(): bool
    {
        return ($this->flags & self::FLAG_UV) !== 0;
    }

    public function hasAttestedCredentialData(): bool
    {
        return ($this->flags & self::FLAG_AT) !== 0;
    }

    public function hasExtensionData(): bool
    {
        return ($this->flags & self::FLAG_ED) !== 0;
    }

    public function getCredentialId(): ?ByteBuffer
    {
        return $this->credentialId;
    }

    /**
     * @throws NotAvailableException when authenticator data does not contain a key.
     *
     * @see hasKey
     */
    public function getKey(): CoseKeyInterface
    {
        if ($this->key === null) {
            throw new NotAvailableException('AuthenticatorData does not contain a key.');
        }
        return $this->key;
    }

    public function hasKey(): bool
    {
        return $this->key !== null;
    }

    public function hasAaguid(): bool
    {
        return $this->aaguid !== null;
    }

    public function getAaguid(): Aaguid
    {
        if ($this->aaguid === null) {
            throw new NotAvailableException('AuthenticatorData does not contain an AAGUID.');
        }
        return new Aaguid($this->aaguid);
    }

    /**
     * Returns the authenticator extension data output.
     */
    public function getExtensionData(): CborMap
    {
        if ($this->extensionData === null) {
            throw new NotAvailableException('AuthenticatorData does not contain extension data.');
        }

        return $this->extensionData;
    }

    public function getRaw(): ByteBuffer
    {
        return $this->raw;
    }
}
