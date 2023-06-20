<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\UnsupportedException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;

class OpenSslVerifier
{
    private const OPENSSL_ALGO_MAP = [
        CoseAlgorithm::ES256 => OPENSSL_ALGO_SHA256,
        CoseAlgorithm::ES384 => OPENSSL_ALGO_SHA384,
        CoseAlgorithm::ES512 => OPENSSL_ALGO_SHA512,

        CoseAlgorithm::RS256 => OPENSSL_ALGO_SHA256,
        CoseAlgorithm::RS384 => OPENSSL_ALGO_SHA384,
        CoseAlgorithm::RS512 => OPENSSL_ALGO_SHA512,
        CoseAlgorithm::RS1 => OPENSSL_ALGO_SHA1,
    ];

    /**
     * @var int
     */
    private $openSslAlgorithm;

    public function __construct(int $coseAlgorithm)
    {
        $this->openSslAlgorithm = $this->getOpenSslAlgorithm($coseAlgorithm);
    }

    private function getOpenSslAlgorithm(int $algorithm): int
    {
        $openSslAlgorithm = self::OPENSSL_ALGO_MAP[$algorithm] ?? null;

        if ($openSslAlgorithm === null) {
            throw new UnsupportedException('Unsupported algorithm');
        }

        return $openSslAlgorithm;
    }

    public function verify(string $data, string $signature, string $keyPem): bool
    {
        $publicKey = openssl_pkey_get_public($keyPem);
        if ($publicKey === false) {
            throw new WebAuthnException('Public key invalid');
        }
        try {
            $verify = openssl_verify($data, $signature, $publicKey, $this->openSslAlgorithm);
            if ($verify === 1) {
                return true;
            }
            if ($verify === 0) {
                return false;
            }

            throw new WebAuthnException('Failed to check signature');
        } finally {
            if (PHP_VERSION_ID < 80000) {
                openssl_free_key($publicKey);
            }
        }
    }
}
