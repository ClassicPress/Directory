<?php

declare(strict_types = 1);

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Cipher;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\SpecificAlgorithmIdentifier;

/**
 * Base class for cipher algorithm identifiers.
 */
abstract class CipherAlgorithmIdentifier extends SpecificAlgorithmIdentifier
{
    /**
     * Initialization vector.
     *
     * @var null|string
     */
    protected $_initializationVector;

    /**
     * Get key size in bytes.
     *
     * @return int
     */
    abstract public function keySize(): int;

    /**
     * Get the initialization vector size in bytes.
     *
     * @return int
     */
    abstract public function ivSize(): int;

    /**
     * Get initialization vector.
     *
     * @return null|string
     */
    public function initializationVector(): ?string
    {
        return $this->_initializationVector;
    }

    /**
     * Get copy of the object with given initialization vector.
     *
     * @param null|string $iv Initialization vector or null to remove
     *
     * @throws \UnexpectedValueException If initialization vector size is invalid
     *
     * @return self
     */
    public function withInitializationVector(?string $iv): self
    {
        $this->_checkIVSize($iv);
        $obj = clone $this;
        $obj->_initializationVector = $iv;
        return $obj;
    }

    /**
     * Check that initialization vector size is valid for the cipher.
     *
     * @param null|string $iv
     *
     * @throws \UnexpectedValueException
     */
    protected function _checkIVSize(?string $iv): void
    {
        if (null !== $iv && strlen($iv) !== $this->ivSize()) {
            throw new \UnexpectedValueException('Invalid IV size.');
        }
    }
}
