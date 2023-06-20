<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki;

final class NullCertificateStatusResolver implements CertificateStatusResolverInterface
{
    public function isRevoked(X509Certificate $subject, X509Certificate ...$caCertificates): bool
    {
        return false;
    }
}
