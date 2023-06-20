<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AuthenticatorData;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\CredentialId;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\CredentialStoreInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\UserCredentialInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Crypto\CoseKeyInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\AuthenticatorAssertionResponseInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\CollectedClientData;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\PublicKeyCredentialInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\TokenBindingStatus;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\UnsupportedException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\VerificationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionRegistryInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\AbstractVerifier;

final class AuthenticationVerifier extends AbstractVerifier
{
    /**
     * @var CredentialStoreInterface
     */
    private $credentialCollection;

    public function __construct(CredentialStoreInterface $credentialCollection, ExtensionRegistryInterface $extensionRegistry)
    {
        parent::__construct($extensionRegistry);
        $this->credentialCollection = $credentialCollection;
    }

    public function verifyAuthenticatonAssertion(PublicKeyCredentialInterface $credential, AuthenticationContext $context): AuthenticationResult
    {
        // SPEC 7.2 Verifying an authentication assertion

        $response = $credential->getResponse()->asAssertionResponse();
        $authData = new AuthenticatorData($response->getAuthenticatorData());
        $extensionContext = $this->processExtensions($credential, $authData, $context, ExtensionInterface::OPERATION_AUTHENTICATION);

        // 1. If the allowCredentials option was given when this authentication ceremony was initiated, verify that
        //    credential.id identifies one of the public key credentials that were listed in allowCredentials.
        if (!$this->checkAllowCredentials($credential, $context->getAllowCredentialIds())) {
            throw new VerificationException('Credential not in list of allowed credentials.');
        }

        // Note: step 2 done after 3 because credential is available then.
        // 3. Using credential’s id attribute (or the corresponding rawId, if base64url encoding is inappropriate for
        // your use case), look up the corresponding credential public key.
        $accountCredential = $this->credentialCollection->findCredential(CredentialId::fromBinary($credential->getRawId()->getBinaryString()));
        if ($accountCredential === null) {
            throw new VerificationException('Account was not found');
        }

        // 2. If credential.response.userHandle is present, verify that the user identified by this value is the owner
        //    of the public key credential identified by credential.id.
        if ($response->getUserHandle() !== null && !$response->getUserHandle()->equals($accountCredential->getUserHandle()->toBuffer())) {
            throw new VerificationException("Credential does not belong to the user identified by the client's userHandle.");
        }

        // 4. Let cData, aData and sig denote the value of credential’s response's clientDataJSON, authenticatorData,
        //    and signature respectively.
        // 5. Let JSONtext be the result of running UTF-8 decode on the value of cData.
        // 6. Let C, the client data claimed as used for the signature, be the result of running an
        //    implementation-specific JSON parser on JSONtext.
        // 7 - 10
        $clientData = $response->getParsedClientData();
        $this->checkClientData($clientData, $context);

        // 11. Verify that the rpIdHash in aData is the SHA-256 hash of the RP ID expected by the Relying Party.
        if (!$this->verifyRpIdHash($authData, $context, $extensionContext)) {
            throw new VerificationException('rpIdHash was not correct.');
        }

        // 12 and 13
        if (!$this->verifyUser($authData, $context)) {
            throw new VerificationException('User verification failed');
        }

        // 14. Verify that the values of the client extension outputs in clientExtensionResults and the authenticator
        //     extension outputs in the extensions in authData are as expected, considering the client extension input
        //     values that were given as the extensions option in the get() call. In particular, any extension
        //     identifier values in the clientExtensionResults and the extensions in authData MUST be also be present
        //     as extension identifier values in the extensions member of options, i.e., no extensions are present that
        //     were not requested. In the general case, the meaning of "are as expected" is specific to the Relying
        //     Party and which extensions are in use.
        //     Note: Since all extensions are OPTIONAL for both the client and the authenticator, the Relying Party MUST
        //     be prepared to handle cases where none or not all of the requested extensions were acted upon.

        // -> This is already checked in processExtensions above, the extensions need to be processed earlier because
        // extensions such as appid affect the effective rpId

        // 15 and 16
        if (!$this->verifySignature($response, $accountCredential->getPublicKey())) {
            throw new VerificationException('Invalid signature');
        }

        // 17 and 18
        if (!$this->verifySignatureCounter($authData, $accountCredential)) {
            throw new VerificationException('Signature counter invalid');
        }

        // If all the above steps are successful, continue with the authentication ceremony as appropriate. Otherwise, fail the authentication ceremony.

        // Additional custom checks:
        // Ensure credential belongs to user being authenticated
        $userHandle = $context->getUserHandle();
        if ($userHandle !== null && !$userHandle->equals($accountCredential->getUserHandle())) {
            throw new VerificationException('Credential does not belong to the user currently being authenticated.');
        }

        return new AuthenticationResult($accountCredential, $authData);
    }

    /**
     * @param CredentialId[]|null $allowCredentialIds
     */
    private function checkAllowCredentials(PublicKeyCredentialInterface $credential, ?array $allowCredentialIds): bool
    {
        if ($allowCredentialIds === null || \count($allowCredentialIds) === 0) {
            return true;
        }

        $credentialId = CredentialId::fromBuffer($credential->getRawId());
        foreach ($allowCredentialIds as $allowCredentialId) {
            if ($allowCredentialId->equals($credentialId)) {
                return true;
            }
        }
        return false;
    }

    private function verifySignature(AuthenticatorAssertionResponseInterface $response, CoseKeyInterface $publicKey): bool
    {
        // 15. Let hash be the result of computing a hash over the cData using SHA-256.
        $clientData = $response->getClientDataJson();
        $clientDataHash = hash('sha256', $clientData, true);

        // 16. Using the credential public key looked up in step 3, verify that sig is a valid signature over the binary concatenation of aData and hash.
        $aData = $response->getAuthenticatorData()->getBinaryString();

        $signData = $aData . $clientDataHash;

        return $publicKey->verifySignature(new ByteBuffer($signData), $response->getSignature());
    }

    private function verifySignatureCounter(AuthenticatorData $authData, UserCredentialInterface $accountCredential): bool
    {
        // 17. If the signature counter value adata.signCount is nonzero or the value stored in conjunction with credential’s id attribute is nonzero, then run the following sub-step:
        $counter = $authData->getSignCount();
        if ($counter === 0) {
            return true;
        }

        $lastCounter = $this->credentialCollection->getSignatureCounter($accountCredential->getCredentialId());

        if ($lastCounter === null) {
            // counter not known
            $this->credentialCollection->updateSignatureCounter($accountCredential->getCredentialId(), $counter);

            // TODO policy
            return true;
        }

        if ($lastCounter === 0) {
            return true;
        }
        if ($counter > $lastCounter) {
            // 18. If the signature counter value adata.signCount is
            // -> greater than the signature counter value stored in conjunction with credential’s id attribute.
            //    Update the stored signature counter value, associated with credential’s id attribute, to be the value of adata.signCount.
            $this->credentialCollection->updateSignatureCounter($accountCredential->getCredentialId(), $counter);
            return true;
        } else {
            // -> less than or equal to the signature counter value stored in conjunction with credential’s id attribute.
            //    This is a signal that the authenticator may be cloned, i.e. at least two copies of the credential private key may exist and are being used in
            // parallel. Relying Parties should incorporate this information into their risk scoring. Whether the Relying Party updates the stored signature counter value in this case, or not, or fails the authentication ceremony or not, is Relying Party-specific.

            // TODO add policy
            return false;
        }
    }

    private function checkClientData(CollectedClientData $clientData, AuthenticationContext $context): void
    {
        // 7. Verify that the value of C.type is the string webauthn.get.
        if ($clientData->getType() !== 'webauthn.get') {
            throw new VerificationException('Expecting type in clientDataJSON to be webauthn.get.');
        }

        // 8. Verify that the value of C.challenge matches the challenge that was sent to the authenticator in the
        //    PublicKeyCredentialRequestOptions passed to the get() call.
        if (!\hash_equals($context->getChallenge()->getBase64Url(), $clientData->getChallenge())) {
            throw new VerificationException('Challenge in clientDataJSON does not match the challenge in the request.');
        }

        // 9. Verify that the value of C.origin matches the Relying Party's origin.
        if (!$this->verifyOrigin($clientData->getOrigin(), $context->getOrigin())) {
            throw new VerificationException(sprintf("Origin '%s' does not match relying party origin.", $clientData->getOrigin()));
        }

        // 10. Verify that the value of C.tokenBinding.status matches the state of Token Binding for the TLS connection
        //     over which the attestation was obtained. If Token Binding was used on that TLS connection, also verify
        //     that C.tokenBinding.id matches the base64url encoding of the Token Binding ID for the connection.
        $tokenBinding = $clientData->getTokenBinding();
        if ($tokenBinding !== null && $tokenBinding->getStatus() === TokenBindingStatus::PRESENT) {
            throw new UnsupportedException('Token binding is not yet supported by this library.');
        }
    }
}
