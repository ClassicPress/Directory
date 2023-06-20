<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki;

use Exception;
use LogicException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\CoseAlgorithm;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\ASN1\Element;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoBridge\Crypto;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoEncoding\PEM;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Feature\SignatureAlgorithmIdentifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Signature\ECDSAWithSHA256AlgorithmIdentifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Signature\SHA1WithRSAEncryptionAlgorithmIdentifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\AlgorithmIdentifier\Signature\SHA256WithRSAEncryptionAlgorithmIdentifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\CryptoTypes\Signature\Signature;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X509\Certificate\Certificate;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Sop\X509\Certificate\TBSCertificate;

class CertificateDetails implements CertificateDetailsInterface
{
    public const VERSION_1 = TBSCertificate::VERSION_1;

    public const VERSION_2 = TBSCertificate::VERSION_2;

    public const VERSION_3 = TBSCertificate::VERSION_3;

    /**
     * @var TBSCertificate
     */
    private $cert;

    private function __construct(TBSCertificate $certificate)
    {
        $this->cert = $certificate;
    }

    public static function fromPem(string $pem): CertificateDetails
    {
        try {
            return new self(Certificate::fromPEM(PEM::fromString($pem))->tbsCertificate());
        } catch (Exception $e) {
            throw new ParseException('Failed to parse PEM certificate.', 0, $e);
        }
    }

    public static function fromCertificate(X509Certificate $certificate): CertificateDetails
    {
        try {
            return new self(Certificate::fromDER($certificate->asDer())->tbsCertificate());
        } catch (Exception $e) {
            throw new ParseException('Failed to parse PEM certificate.', 0, $e);
        }
    }

    public function verifySignature(string $data, string $signature, int $coseAlgorithm): bool
    {
        $signatureAlgorithm = $this->convertCoseAlgorthm($coseAlgorithm);
        try {
            $signatureData = Signature::fromSignatureData($signature, $signatureAlgorithm);
            $key = $this->cert->subjectPublicKeyInfo();
            $crypto = Crypto::getDefault();
            return $crypto->verify($data, $signatureData, $key, $signatureAlgorithm);
        } catch (Exception $e) {
            throw new WebAuthnException('Failed to verify signature.', 0, $e);
        }
    }

    public function getPublicKeyDer(): string
    {
        try {
            return $this->cert->subjectPublicKeyInfo()->toDER();
        } catch (Exception $e) {
            throw new ParseException('Failed to get public key from certificate.', 0, $e);
        }
    }

    private function convertCoseAlgorthm(int $coseAlgorithm): SignatureAlgorithmIdentifier
    {
        switch ($coseAlgorithm) {
            case CoseAlgorithm::ES256:
            case CoseAlgorithm::ES384:
            case CoseAlgorithm::ES512:
                return new ECDSAWithSHA256AlgorithmIdentifier();
            case CoseAlgorithm::RS256:
            case CoseAlgorithm::RS384:
            case CoseAlgorithm::RS512:
                return new SHA256WithRSAEncryptionAlgorithmIdentifier();
            case CoseAlgorithm::RS1:
                return new SHA1WithRSAEncryptionAlgorithmIdentifier();
        }

        throw new WebAuthnException(sprintf('Signature format %d not supported.', $coseAlgorithm));
    }

    public function getExtensionData(string $oid): ?CertificateExtension
    {
        try {
            $extension = $this->cert->extensions()->get($oid);
        } catch (LogicException $e) {
            // No extension present
            return null;
        }
        try {
            $seq = $extension->toASN1();
            $idx = $seq->has(1, Element::TYPE_OCTET_STRING) ? 1 : 2;
            $der = $seq->at($idx)->asOctetString()->string();
            return new CertificateExtension($extension->oid(), $extension->isCritical(), new ByteBuffer($der));
        } catch (Exception $e) {
            throw new ParseException(sprintf('Failed to parse extension %s.', $oid), 0, $e);
        }
    }

    public function getCertificateVersion(): ?int
    {
        // NOTE: version() can throw a LogicException if no version is set, however this is never the case
        // when reading certificates. Even version 1 x509 certificates without the (optional) tagged version
        // will always default to version 1.
        return $this->cert->version();
    }

    public function getOrganizationalUnit(): string
    {
        try {
            return $this->cert->subject()->firstValueOf('OU')->stringValue();
        } catch (Exception $e) {
            throw new ParseException('Failed to retrieve the organizational unit', 0, $e);
        }
    }

    public function getSubject(): string
    {
        try {
            return $this->cert->subject()->toString();
        } catch (Exception $e) {
            throw new ParseException('Failed to retrieve subject unit', 0, $e);
        }
    }

    public function getSubjectCommonName(): string
    {
        try {
            return $this->cert->subject()->firstValueOf('CN')->stringValue();
        } catch (Exception $e) {
            throw new ParseException('Failed to retrieve subject CN value', 0, $e);
        }
    }

    public function getSubjectAlternateNameDN(string $oid): string
    {
        try {
            $attrValue = $this->cert->extensions()->subjectAlternativeName()->names()->firstDN()->firstValueOf($oid);
            return $attrValue->toASN1()->asUnspecified()->asUTF8String()->string();
        } catch (Exception $e) {
            throw new ParseException(sprintf('Failed to retrieve %s entry in directoryName in alternate name.', $oid), 0, $e);
        }
    }

    public function isCA(): ?bool
    {
        $extensions = $this->cert->extensions();

        if (!$extensions->hasBasicConstraints()) {
            return null;
        }

        return $extensions->basicConstraints()->isCA();
    }

    public function extendedKeyUsageContains(string $oid): bool
    {
        try {
            $extensions = $this->cert->extensions();
            if (!$extensions->hasExtendedKeyUsage()) {
                return false;
            }
            return $extensions->extendedKeyUsage()->has($oid);
        } catch (Exception $e) {
            throw new ParseException('Failed to retrieve subject unit', 0, $e);
        }
    }

    public function getPublicKeyIdentifier(): string
    {
        try {
            return \bin2hex($this->cert->subjectPublicKeyInfo()->keyIdentifier());
        } catch (Exception $e) {
            throw new ParseException('Failed to get public key identifier', 0, $e);
        }
    }
}
