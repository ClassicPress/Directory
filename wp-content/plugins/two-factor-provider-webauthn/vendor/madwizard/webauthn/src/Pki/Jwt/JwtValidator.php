<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\Jwt;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\Der;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\VerificationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;

final class JwtValidator implements JwtValidatorInterface
{
    private const ALG_INFO =
        [
            'ES256' => ['convert' => true, 'sigComponentLen' => 32],
            'ES384' => ['convert' => true, 'sigComponentLen' => 48],
            'ES512' => ['convert' => true, 'sigComponentLen' => 66],
            'RS256' => ['convert' => false],
            'RS384' => ['convert' => false],
            'RS512' => ['convert' => false],
        ];

    public function __construct()
    {
    }

    public function validate(JwtInterface $token, ValidationContext $context): array
    {
        // TODO: validate other header items
        $header = $token->getHeader();
        $alg = $this->validateAlgorithm($header, $context);

        $asn1Sig = $this->convertSignature($token->getSignature(), $alg);
        if (!$context->getKey()->verifySignature($token->getSignedData(), $asn1Sig)) {
            throw new VerificationException('Invalid signature.');
        }
        /* TODO
                $now = $context->getReferenceUnixTime();

                $exp = $header['exp'] ?? null;
                if ($exp !== null) {
                    if (!is_int($exp)) {
                        throw new VerificationException('Invalid "exp" header value.');
                    }
                }
        */
        return $token->getBody();
    }

    private function convertSignature(ByteBuffer $signature, string $algorithm): ByteBuffer
    {
        $algInfo = self::ALG_INFO[$algorithm];
        if (!$algInfo['convert']) {
            return $signature;
        }
        $componentLen = $algInfo['sigComponentLen'];
        if ($signature->getLength() !== ($componentLen * 2)) {
            throw new ParseException(sprintf('Invalid signature length %d.', $signature->getLength()));
        }
        $r = $signature->getBytes(0, $componentLen);
        $s = $signature->getBytes($componentLen, $componentLen);
        return new ByteBuffer(Der::sequence(Der::unsignedInteger($r) . Der::unsignedInteger($s)));
    }

    private function validateAlgorithm(array $header, ValidationContext $ctx): string
    {
        $alg = $header['alg'] ?? null;
        if (in_array($alg, $ctx->getAllowedAlgorithms(), true)) {
            return $alg;
        }
        throw new VerificationException('Algorithm not allowed.');
    }
}
