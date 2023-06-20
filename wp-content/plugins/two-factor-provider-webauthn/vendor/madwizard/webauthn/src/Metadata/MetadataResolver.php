<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor\MetadataInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Provider\MetadataProviderInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationResultInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\LoggerAwareInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\LoggerAwareTrait;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\NullLogger;

final class MetadataResolver implements MetadataResolverInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var MetadataProviderInterface[]
     */
    private $providers;

    public function __construct(array $providers)
    {
        $this->providers = $providers;
        $this->logger = new NullLogger();
    }

    public function getMetadata(RegistrationResultInterface $registrationResult): ?MetadataInterface
    {
        foreach ($this->providers as $provider) {
            try {
                $metadata = $provider->getMetadata($registrationResult);
                if ($metadata !== null) {
                    $this->logger->info('Found metadata for authenticator in provider {provider}.', ['provider' => $provider->getDescription()]);
                    return $metadata;
                }
            } catch (WebAuthnException $e) {
                $this->logger->warning('Error retrieving metadata ({error}) - ignoring provider {provider}.', ['error' => $e->getMessage(), 'provider' => $provider->getDescription(), 'exception' => $e]);
                continue;
            }
        }
        return null;
    }
}
