<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Statement;

interface AttestationStatementInterface
{
    public function getFormatId(): string;
}
