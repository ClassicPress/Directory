<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Statement;

final class AuthenticatorStatus
{
    public const NOT_FIDO_CERTIFIED = 'NOT_FIDO_CERTIFIED';

    public const FIDO_CERTIFIED = 'FIDO_CERTIFIED';

    public const USER_VERIFICATION_BYPASS = 'USER_VERIFICATION_BYPASS';

    public const ATTESTATION_KEY_COMPROMISE = 'ATTESTATION_KEY_COMPROMISE';

    public const USER_KEY_REMOTE_COMPROMISE = 'USER_KEY_REMOTE_COMPROMISE';

    public const USER_KEY_PHYSICAL_COMPROMISE = 'USER_KEY_PHYSICAL_COMPROMISE';

    public const UPDATE_AVAILABLE = 'UPDATE_AVAILABLE';

    public const REVOKED = 'REVOKED';

    public const SELF_ASSERTION_SUBMITTED = 'SELF_ASSERTION_SUBMITTED';

    public const FIDO_CERTIFIED_L1 = 'FIDO_CERTIFIED_L1';

    public const FIDO_CERTIFIED_L2 = 'FIDO_CERTIFIED_L2';

    public const FIDO_CERTIFIED_L3 = 'FIDO_CERTIFIED_L3';

    public const FIDO_CERTIFIED_L4 = 'FIDO_CERTIFIED_L4';

    public const FIDO_CERTIFIED_L = 'FIDO_CERTIFIED_L5';

    public const LIST_UNDESIRED_STATUS = [
        self::USER_VERIFICATION_BYPASS,
        self::ATTESTATION_KEY_COMPROMISE,
        self::USER_KEY_REMOTE_COMPROMISE,
        self::USER_KEY_PHYSICAL_COMPROMISE,
        self::REVOKED,
    ];

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
