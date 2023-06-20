<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Verifier;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationType;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AuthenticatorData;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Fido\FidoAaguidExtension;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Registry\AttestationFormatInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Registry\BuiltInAttestationFormat;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement\AttestationStatementInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement\PackedAttestationStatement;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustPath\CertificateTrustPath;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustPath\EmptyTrustPath;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\UnsupportedException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\VerificationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\CertificateDetails;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\CertificateDetailsInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\X509Certificate;

final class PackedAttestationVerifier implements AttestationVerifierInterface
{
    public function verify(AttestationStatementInterface $attStmt, AuthenticatorData $authenticatorData, string $clientDataHash): VerificationResult
    {
        // Verification procedure from https://www.w3.org/TR/webauthn/#packed-attestation

        if (!($attStmt instanceof PackedAttestationStatement)) {
            throw new VerificationException('Expecting PackedAttestationStatement');
        }

        // 1. Verify that attStmt is valid CBOR conforming to the syntax defined above and perform CBOR decoding on it
        //    to extract the contained fields.
        // -> This is done in PackedAttestationStatement

        // 2. If x5c is present, this indicates that the attestation type is not ECDAA.
        $x5c = $attStmt->getCertificates();
        if ($x5c !== null) {
            return $this->verifyX5C($x5c, $attStmt->getSignature(), $attStmt->getAlgorithm(), $authenticatorData, $clientDataHash);
        }

        // 3. If ecdaaKeyId is present, then the attestation type is ECDAA.
        $ecdaaKeyId = $attStmt->getEcdaaKeyId();
        if ($ecdaaKeyId !== null) {
            throw new UnsupportedException('ECDAA is not supported by this library and is removed in later WebAuthn specifications.');
        }

        // 4. If neither x5c nor ecdaaKeyId is present, self attestation is in use.
        return $this->verifySelf($attStmt->getSignature(), $attStmt->getAlgorithm(), $authenticatorData, $clientDataHash);
    }

    /**
     * @param X509Certificate[] $x5c
     */
    private function verifyX5c(array $x5c, ByteBuffer $signature, int $signatureAlgorithm, AuthenticatorData $authenticatorData, string $clientDataHash): VerificationResult
    {
        // Verify that sig is a valid signature over the concatenation of authenticatorData and clientDataHash using
        // the attestation public key in attestnCert with the algorithm specified in alg.

        if (!isset($x5c[0])) {
            throw new VerificationException('Empty X5C in attestation.');
        }
        try {
            $cert = CertificateDetails::fromPem($x5c[0]->asPem());
            $verificationData = $authenticatorData->getRaw()->getBinaryString() . $clientDataHash;
            $valid = $cert->verifySignature($verificationData, $signature->getBinaryString(), $signatureAlgorithm);
        } catch (WebAuthnException $e) {
            throw new VerificationException('Failed to process attestation certificate.', 0, $e);
        }

        if (!$valid) {
            throw new VerificationException('Attestation signature is invalid.');
        }

        // Verify that attestnCert meets the requirements in §8.2.1 Packed attestation statement certificate requirements.
        $this->checkCertRequirements($cert);

        // If attestnCert contains an extension with OID 1.3.6.1.4.1.45724.1.1.4 (id-fido-gen-ce-aaguid) verify that the value of this extension matches the aaguid in authenticatorData.
        FidoAaguidExtension::checkAaguidExtension($cert, $authenticatorData->getAaguid());

        // If successful, return attestation type Basic and attestation trust path x5c.
        return new VerificationResult(AttestationType::BASIC, new CertificateTrustPath(...$x5c));
    }

    private function verifySelf(ByteBuffer $signature, int $algorithm, AuthenticatorData $authenticatorData, string $clientDataHash): VerificationResult
    {
        // Validate that alg matches the algorithm of the credentialPublicKey in authenticatorData.
        if (!$authenticatorData->hasKey()) {
            throw new VerificationException('No key in authenticator data.');
        }
        $key = $authenticatorData->getKey();
        if ($key->getAlgorithm() !== $algorithm) {
            throw new VerificationException(sprintf('Algorithm in packed attestation statement (%d) should match public key algorithm (%d)', $algorithm, $key->getAlgorithm()));
        }

        // Verify that sig is a valid signature over the concatenation of authenticatorData and clientDataHash using the credential public key with alg.

        try {
            $verificationData = $authenticatorData->getRaw()->getBinaryString() . $clientDataHash;
            $valid = $key->verifySignature(new ByteBuffer($verificationData), $signature);
        } catch (WebAuthnException $e) {
            throw new VerificationException('Error while verifying signature for packed attestation', 0, $e);
        }

        // If successful, return attestation type Self and empty attestation trust path.
        if ($valid) {
            return new VerificationResult(AttestationType::SELF, new EmptyTrustPath());
        }

        throw new VerificationException('Signature for self attestation could not be verified.');
    }

    private function checkCertRequirements(CertificateDetailsInterface $cert): void
    {
        // 8.2.1. Packed attestation statement certificate requirements
        //  The attestation certificate MUST have the following fields/extensions:

        // Version MUST be set to 3 (which is indicated by an ASN.1 INTEGER with value 2).
        $version = $cert->getCertificateVersion();
        if ($version !== CertificateDetails::VERSION_3) {
            throw new VerificationException(sprintf('Attestation certificate version value is %s but should be %s (version 3).', $version ?? 'null', CertificateDetails::VERSION_3));
        }

        // Subject field MUST be set to:
        // [... Most fields are vendor specific, only subject-OU is specified in the spec ...]
        // Subject-OU Literal string "Authenticator Attestation" (UTF8String)
        try {
            $ou = $cert->getOrganizationalUnit();
            if ($ou !== 'Authenticator Attestation') {
                throw new VerificationException(sprintf("Subject-OU is '%s' but expecting 'Authenticator Attestation'.", $ou));
            }
        } catch (ParseException $e) {
            throw new VerificationException('Failed to parse Subject-OU in attestation certificate.', 0, $e);
        }

        // If the related attestation root certificate is used for multiple authenticator models, the Extension OID
        // 1.3.6.1.4.1.45724.1.1.4 (id-fido-gen-ce-aaguid) MUST be present, containing the AAGUID as a 16-byte OCTET
        // STRING. The extension MUST NOT be marked as critical.
        // -> this is already verified in checkAaguidExtension

        // The Basic Constraints extension MUST have the CA component set to false.
        if ($cert->isCA() !== false) {
            throw new VerificationException('Attestation certificate should not the CA basic constraint set to false.');
        }

        // An Authority Information Access (AIA) extension with entry id-ad-ocsp and a CRL Distribution Point extension
        // [RFC5280] are both OPTIONAL as the status of many attestation certificates is available through authenticator
        // metadata services. See, for example, the FIDO Metadata Service [FIDOMetadataService].
        // -> not handled here.
    }

    public function getSupportedFormat(): AttestationFormatInterface
    {
        return new BuiltInAttestationFormat(
            PackedAttestationStatement::FORMAT_ID,
            PackedAttestationStatement::class,
            $this
        );
    }
}
