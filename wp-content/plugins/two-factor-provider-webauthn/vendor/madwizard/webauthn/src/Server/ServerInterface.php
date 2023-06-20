<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom\PublicKeyCredentialInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\CredentialIdExistsException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication\AuthenticationContext;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication\AuthenticationOptions;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication\AuthenticationRequest;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Authentication\AuthenticationResultInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationContext;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationOptions;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationRequest;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationResultInterface;

interface ServerInterface
{
    public function startRegistration(RegistrationOptions $options): RegistrationRequest;

    /**
     * @param PublicKeyCredentialInterface $credential Attestation credential response from the client
     *
     * @throws CredentialIdExistsException
     */
    public function finishRegistration(PublicKeyCredentialInterface $credential, RegistrationContext $context): RegistrationResultInterface;

    public function startAuthentication(AuthenticationOptions $options): AuthenticationRequest;

    /**
     * @param PublicKeyCredentialInterface $credential Assertion credential response from the client
     */
    public function finishAuthentication(PublicKeyCredentialInterface $credential, AuthenticationContext $context): AuthenticationResultInterface;
}
