<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format;

use InvalidArgumentException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ByteBufferException;
use Serializable;
use function bin2hex;
use function hex2bin;
use const INF;
use const PHP_INT_SIZE;

class ByteBuffer implements Serializable
{
    use SerializableTrait;

    /**
     * @var string
     */
    private $data;

    /**
     * @var int
     */
    private $length;

    public function __construct(string $binaryData)
    {
        $this->data = $binaryData;
        $this->length = \strlen($binaryData);
    }

    public static function fromHex(string $hex): ByteBuffer
    {
        $bin = @hex2bin($hex);
        if ($bin === false) {
            throw new InvalidArgumentException('Invalid hex string');
        }
        return new ByteBuffer($bin);
    }

    public static function fromBase64Url(string $base64url): ByteBuffer
    {
        return new ByteBuffer(Base64UrlEncoding::decode($base64url));
    }

    public function isEmpty(): bool
    {
        return $this->length === 0;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public static function randomBuffer(int $length): ByteBuffer
    {
        return new ByteBuffer(\random_bytes($length));
    }

    /**
     * @throws ByteBufferException
     */
    public function getBytes(int $offset, int $length): string
    {
        if ($offset < 0 || $length < 0 || ($offset + $length > $this->length)) {
            throw new ByteBufferException('Invalid offset or length');
        }
        return \substr($this->data, $offset, $length);
    }

    /**
     * @throws ByteBufferException
     */
    public function getByteVal(int $offset): int
    {
        if ($offset < 0 || $offset >= $this->length) {
            throw new ByteBufferException('Invalid offset');
        }
        return \ord($this->data[$offset]);
    }

    /**
     * @throws ByteBufferException
     */
    public function getUint16Val(int $offset): int
    {
        if ($offset < 0 || ($offset + 2) > $this->length) {
            throw new ByteBufferException('Invalid offset');
        }
        return unpack('n', $this->data, $offset)[1];
    }

    /**
     * @throws ByteBufferException
     */
    public function getUint32Val(int $offset): int
    {
        if ($offset < 0 || ($offset + 4) > $this->length) {
            throw new ByteBufferException('Invalid offset');
        }
        $val = unpack('N', $this->data, $offset)[1];
        // Signed integer overflow causes signed negative numbers
        if ($val < 0) {
            throw new ByteBufferException('Value out of integer range.');
        }
        return $val;
    }

    /**
     * @throws ByteBufferException
     */
    public function getUint64Val(int $offset): int
    {
        if (PHP_INT_SIZE < 8) {
            throw new ByteBufferException('64-bit values not supported by this system');
        }
        if ($offset < 0 || ($offset + 8) > $this->length) {
            throw new ByteBufferException('Invalid offset');
        }
        $val = unpack('J', $this->data, $offset)[1];

        // Signed integer overflow causes signed negative numbers
        if ($val < 0) {
            throw new ByteBufferException('Value out of integer range.');
        }

        return $val;
    }

    public function getHalfFloatVal(int $offset): float
    {
        //FROM spec pseudo decode_half(unsigned char *halfp)
        $half = $this->getUint16Val($offset);

        $exp = ($half >> 10) & 0x1f;
        $mant = $half & 0x3ff;

        if ($exp === 0) {
            $val = $mant * (2 ** -24);
        } elseif ($exp !== 31) {
            $val = ($mant + 1024) * (2 ** ($exp - 25));
        } else {
            $val = ($mant === 0) ? INF : NAN;
        }

        return ($half & 0x8000) ? -$val : $val;
    }

    /**
     * @throws ByteBufferException
     */
    public function getFloatVal(int $offset): float
    {
        if ($offset < 0 || ($offset + 4) > $this->length) {
            throw new ByteBufferException('Invalid offset');
        }
        return unpack('G', $this->data, $offset)[1];
    }

    /**
     * @throws ByteBufferException
     */
    public function getDoubleVal(int $offset): float
    {
        if ($offset < 0 || ($offset + 8) > $this->length) {
            throw new ByteBufferException('Invalid offset');
        }
        return unpack('E', $this->data, $offset)[1];
    }

    public function getBinaryString(): string
    {
        return $this->data;
    }

    public function equals(ByteBuffer $buffer): bool
    {
        return $this->data === $buffer->data; // TODO constant time
    }

    public function getHex(): string
    {
        return bin2hex($this->data);
    }

    public function getBase64Url(): string
    {
        return Base64UrlEncoding::encode($this->data);
    }

    public function __serialize(): array
    {
        return ['d' => $this->data];
    }

    public function __unserialize(array $data): void
    {
        $this->data = $data['d'];
        $this->length = strlen($this->data);
    }
}
