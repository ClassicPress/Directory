<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\UnsupportedException;

class CoseHash
{
    /**
     * @var string
     */
    private $phpAlg;

    private const MAP = [
        CoseAlgorithm::RS1 => 'sha1',
        CoseAlgorithm::ES256 => 'sha256',
        CoseAlgorithm::ES384 => 'sha384',
        CoseAlgorithm::ES512 => 'sha512',
        CoseAlgorithm::RS256 => 'sha256',
        CoseAlgorithm::RS384 => 'sha384',
        CoseAlgorithm::RS512 => 'sha512',
    ];

    /**
     * CoseHash constructor.
     *
     * @param int $algorithm CoseAlgorithm identifier
     *
     * @see CoseAlgorithm
     *
     * @throws UnsupportedException
     */
    public function __construct(int $algorithm)
    {
        $phpAlg = self::MAP[$algorithm] ?? null;
        if ($phpAlg === null) {
            throw new UnsupportedException(sprintf('COSE algorithm %d not supported for hashing.', $algorithm));
        }
        $this->phpAlg = $phpAlg;
    }

    public function hash(string $data): string
    {
        return hash($this->phpAlg, $data, true);
    }
}
