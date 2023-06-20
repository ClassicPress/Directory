<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Registry\AttestationFormatRegistryInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Config\RelyingPartyInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\CredentialId;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\CredentialStoreInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Credential\UserHandle;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\AuthenticationExtensionsClientInputs;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\AuthenticatorSelectionCriteria;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\PublicKeyCredentialCreationOptions;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\PublicKeyCredentialDescriptor;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\PublicKeyCredentialInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\PublicKeyCredentialParameters;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\PublicKeyCredentialRequestOptions;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\PublicKeyCredentialRpEntity;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\PublicKeyCredentialUserEntity;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\ResidentKeyRequirement;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\UserVerificationRequirement;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\CredentialIdExistsException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\NoCredentialsException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\UntrustedException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\VerificationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Extension\ExtensionRegistryInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\MetadataResolverInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy\PolicyInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Policy\Trust\TrustDecisionManagerInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication\AuthenticationContext;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication\AuthenticationOptions;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication\AuthenticationRequest;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication\AuthenticationResultInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication\AuthenticationVerifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationContext;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationOptions;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationRequest;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationResultInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationVerifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\LoggerAwareInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\LoggerAwareTrait;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\NullLogger;

final class WebAuthnServer implements ServerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var RelyingPartyInterface
     */
    private $relyingParty;

    /**
     * @var PolicyInterface
     */
    private $policy;

    /**
     * @var CredentialStoreInterface
     */
    private $credentialStore;

    /**
     * @var AttestationFormatRegistryInterface
     */
    private $formatRegistry;

    /**
     * @var MetadataResolverInterface
     */
    private $metadataResolver;

    /**
     * @var TrustDecisionManagerInterface
     */
    private $trustDecisionManager;

    /**
     * @var ExtensionRegistryInterface
     */
    private $extensionRegistry;

    public function __construct(
        RelyingPartyInterface $relyingParty,
        PolicyInterface $policy,
        CredentialStoreInterface $credentialStore,
        AttestationFormatRegistryInterface $formatRegistry,
        MetadataResolverInterface $metadataResolver,
        TrustDecisionManagerInterface $trustDecisionManager,
        ExtensionRegistryInterface $extensionRegistry
    ) {
        $this->relyingParty = $relyingParty;
        $this->policy = $policy;
        $this->credentialStore = $credentialStore;
        $this->formatRegistry = $formatRegistry;
        $this->metadataResolver = $metadataResolver;
        $this->trustDecisionManager = $trustDecisionManager;
        $this->extensionRegistry = $extensionRegistry;
        $this->logger = new NullLogger();
    }

    public function startRegistration(RegistrationOptions $options): RegistrationRequest
    {
        $challenge = $this->createChallenge();

        $creationOptions = new PublicKeyCredentialCreationOptions(
            PublicKeyCredentialRpEntity::fromRelyingParty($this->relyingParty),
            $this->createUserEntity($options->getUser()),
            $challenge,
            $this->getCredentialParameters()
        );

        $creationOptions->setAttestation($options->getAttestation());
        $creationOptions->setTimeout($options->getTimeout());

        $selection = $this->createAuthenticatorSelection($options);

        $creationOptions->setAuthenticatorSelection($selection);
        $extensions = $options->getExtensionInputs();
        if (count($extensions) > 0) {
            $creationOptions->setExtensions(
                AuthenticationExtensionsClientInputs::fromArray($extensions)
            );
        }

        if ($options->getExcludeExistingCredentials()) {
            $credentialIds = $this->credentialStore->getUserCredentialIds($options->getUser()->getUserHandle());
            foreach ($credentialIds as $credential) {
                $creationOptions->addExcludeCredential(
                    new PublicKeyCredentialDescriptor($credential->toBuffer())
                );
            }
        }

        $context = $this->createRegistrationContext($options, $creationOptions);
        return new RegistrationRequest($creationOptions, $context);
    }

    private function createAuthenticatorSelection(RegistrationOptions $options): ?AuthenticatorSelectionCriteria
    {
        $criteria = null;
        $attachment = $options->getAuthenticatorAttachment();
        if ($attachment !== null) {
            $criteria = new AuthenticatorSelectionCriteria();
            $criteria->setAuthenticatorAttachment($attachment);
        }

        $userVerification = $options->getUserVerification();
        if ($userVerification !== null) {
            $criteria = $criteria ?? new AuthenticatorSelectionCriteria();
            $criteria->setUserVerification($userVerification);
        }

        $residentKey = $options->getResidentKey();
        if ($residentKey !== null) {
            $criteria = $criteria ?? new AuthenticatorSelectionCriteria();
            $criteria->setRequireResidentKey($residentKey === ResidentKeyRequirement::REQUIRED);
        }
        return $criteria;
    }

    private function createRegistrationContext(RegistrationOptions $regOptions, PublicKeyCredentialCreationOptions $options): RegistrationContext
    {
        $origin = $this->relyingParty->getOrigin();
        $rpId = $this->relyingParty->getEffectiveId();

        // TODO: mismatch $rp and rp in $options? Check?
        $context = new RegistrationContext($options->getChallenge(), $origin, $rpId, UserHandle::fromBuffer($options->getUserEntity()->getId()));

        $context->setUserPresenceRequired($this->policy->isUserPresenceRequired());

        if ($regOptions->getUserVerification() === UserVerificationRequirement::REQUIRED) {
            $context->setUserVerificationRequired(true);
        }

        foreach ($regOptions->getExtensionInputs() as $input) {
            $context->addExtensionInput($input);
        }
        return $context;
    }

    /**
     * @param PublicKeyCredentialInterface $credential Attestation credential response from the client
     *
     * @throws CredentialIdExistsException
     * @throws VerificationException
     */
    public function finishRegistration(PublicKeyCredentialInterface $credential, RegistrationContext $context): RegistrationResultInterface
    {
        $verifier = new RegistrationVerifier($this->formatRegistry, $this->extensionRegistry);
        $registrationResult = $verifier->verify($credential, $context);

        $response = $credential->getResponse()->asAttestationResponse();

        // 15. If validation is successful, obtain a list of acceptable trust anchors (attestation root certificates or
        //     ECDAA-Issuer public keys) for that attestation type and attestation statement format fmt, from a trusted
        //     source or from policy.

        $metadata = $this->metadataResolver->getMetadata($registrationResult);
        $registrationResult = $registrationResult->withMetadata($metadata);

        // 16. Assess the attestation trustworthiness using the outputs of the verification procedure in step 14,
        //     as follows:
        //       If self attestation was used, check if self attestation is acceptable under Relying Party policy.
        //       If ECDAA was used, verify that the identifier of the ECDAA-Issuer public key used is included in the
        //       set of acceptable trust anchors obtained in step 15.
        //       Otherwise, use the X.509 certificates returned by the verification procedure to verify that the
        //       attestation public key correctly chains up to an acceptable root certificate.

        try {
            $this->trustDecisionManager->verifyTrust($registrationResult, $metadata);
        } catch (UntrustedException $e) {
            throw new VerificationException('The attestation is not trusted: ' . $e->getReason(), 0, $e);
        }

        // 17. Check that the credentialId is not yet registered to any other user. If registration is requested for a
        //     credential that is already registered to a different user, the Relying Party SHOULD fail this
        //     registration ceremony, or it MAY decide to accept the registration, e.g. while deleting the older
        //     registration.
        if ($this->credentialStore->findCredential($registrationResult->getCredentialId())) {
            throw new CredentialIdExistsException('Credential is already registered.');
        }

        // 18. If the attestation statement attStmt verified successfully and is found to be trustworthy, then register
        //     the new credential with the account that was denoted in the options.user passed to create(), by
        //     associating it with the credentialId and credentialPublicKey in the attestedCredentialData in authData,
        //     as appropriate for the Relying Party's system.
        // 19. If the attestation statement attStmt successfully verified but is not trustworthy per step 16 above,
        //     the Relying Party SHOULD fail the registration ceremony.
        //
        //    NOTE: However, if permitted by policy, the Relying Party MAY register the credential ID and credential
        //    public key but treat the credential as one with self attestation (see §6.3.3 Attestation Types).
        //    If doing so, the Relying Party is asserting there is no cryptographic proof that the public key credential
        //    has been generated by a particular authenticator model. See [FIDOSecRef] and [UAFProtocol] for a more
        //    detailed discussion.
        //
        //    Verification of attestation objects requires that the Relying Party has a trusted method of determining
        //    acceptable trust anchors in step 15 above. Also, if certificates are being used, the Relying Party MUST
        //    have access to certificate status information for the intermediate CA certificates. The Relying Party MUST
        //    also be able to build the attestation certificate chain if the client did not provide this chain in the
        //    attestation information.
        return $registrationResult;
    }

    public function startAuthentication(AuthenticationOptions $options): AuthenticationRequest
    {
        $challenge = $this->createChallenge();

        $requestOptions = new PublicKeyCredentialRequestOptions($challenge);
        $requestOptions->setRpId($this->relyingParty->getId());
        $uv = $options->getUserVerification();
        if ($uv !== UserVerificationRequirement::DEFAULT) {
            $requestOptions->setUserVerification($uv);
        }
        $requestOptions->setTimeout($options->getTimeout());

        $this->addAllowCredentials($options, $requestOptions);

        $extensions = $options->getExtensionInputs();
        if (count($extensions) > 0) {
            $requestOptions->setExtensions(
                AuthenticationExtensionsClientInputs::fromArray($extensions)
            );
        }

        $context = $this->createAuthenticationContext($options, $requestOptions);
        return new AuthenticationRequest($requestOptions, $context);
    }

    private function createAuthenticationContext(AuthenticationOptions $authOptions, PublicKeyCredentialRequestOptions $options): AuthenticationContext
    {
        $origin = $this->relyingParty->getOrigin();
        $rpId = $this->relyingParty->getEffectiveId();

        // TODO: mismatch $rp and rp in $policy? Check?
        $context = new AuthenticationContext($options->getChallenge(), $origin, $rpId, $authOptions->getUserHandle());

        if ($authOptions->getUserVerification() === UserVerificationRequirement::REQUIRED) {
            $context->setUserVerificationRequired(true);
        }

        $context->setUserPresenceRequired($this->policy->isUserPresenceRequired());

        $allowCredentials = $options->getAllowCredentials();
        if ($allowCredentials !== null) {
            foreach ($allowCredentials as $credential) {
                $context->addAllowCredentialId(CredentialId::fromBuffer($credential->getId()));
            }
        }
        foreach ($authOptions->getExtensionInputs() as $input) {
            $context->addExtensionInput($input);
        }
        return $context;
    }

    /**
     * @param PublicKeyCredentialInterface $credential Assertion credential response from the client
     *
     * @throws VerificationException
     */
    public function finishAuthentication(PublicKeyCredentialInterface $credential, AuthenticationContext $context): AuthenticationResultInterface
    {
        $verifier = new AuthenticationVerifier($this->credentialStore, $this->extensionRegistry);

        $authenticationResult = $verifier->verifyAuthenticatonAssertion($credential, $context);

        return $authenticationResult;
    }

    /**
     * @throws WebAuthnException
     */
    private function addAllowCredentials(AuthenticationOptions $options, PublicKeyCredentialRequestOptions $requestOptions): void
    {
        $userHandle = $options->getUserHandle();
        if ($userHandle !== null) {
            $credentialIds = $this->credentialStore->getUserCredentialIds($userHandle);
            if (count($credentialIds) === 0) {
                throw new NoCredentialsException('User being authenticated has no credentials.');
            }
            foreach ($credentialIds as $credentialId) {
                $descriptor = new PublicKeyCredentialDescriptor($credentialId->toBuffer());
                $requestOptions->addAllowedCredential($descriptor);
            }
        }

        $credentialIds = $options->getAllowCredentials();
        if (count($credentialIds) > 0) {
            foreach ($credentialIds as $credential) {
                $credentialId = $credential->toBuffer();
                $descriptor = new PublicKeyCredentialDescriptor($credentialId);
                // TODO
//                foreach ($transports as $transport) {
//                    $descriptor->addTransport($transport);
//                }
                $requestOptions->addAllowedCredential($descriptor);
            }
        }
    }

    private function createUserEntity(UserIdentityInterface $user): PublicKeyCredentialUserEntity
    {
        return new PublicKeyCredentialUserEntity(
            $user->getUsername(),
            $user->getUserHandle()->toBuffer(),
            $user->getDisplayName()
        );
    }

    /**
     * @return PublicKeyCredentialParameters[]
     */
    private function getCredentialParameters(): array
    {
        $parameters = [];
        $algorithms = $this->policy->getAllowedAlgorithms();        // TODO: verify server side?
        foreach ($algorithms as $algorithm) {
            $parameters[] = new PublicKeyCredentialParameters($algorithm);
        }
        return $parameters;
    }

    private function createChallenge(): ByteBuffer
    {
        return ByteBuffer::randomBuffer($this->policy->getChallengeLength());
    }
}
