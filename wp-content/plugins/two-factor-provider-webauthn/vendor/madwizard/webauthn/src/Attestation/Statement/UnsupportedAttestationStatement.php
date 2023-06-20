<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\AttestationObject;

class UnsupportedAttestationStatement extends AbstractAttestationStatement
{
    public function __construct(AttestationObject $attestationObject)
    {
        parent::__construct($attestationObject, $attestationObject->getFormat());
    }
}
