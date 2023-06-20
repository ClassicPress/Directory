<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;

final class PublicKeyCredentialParameters extends AbstractDictionary
{
    /**
     * Algorithm from COSE.
     *
     * @var int
     */
    private $alg;

    /**
     * @var string
     */
    private $type;

    /**
     * PublicKeyCredentialParameters constructor.
     *
     * @param int $alg COSEAlgorithmIdentifier;
     */
    public function __construct(int $alg, string $type = PublicKeyCredentialType::PUBLIC_KEY)
    {
        if (!PublicKeyCredentialType::isValidType($type)) {
            throw new WebAuthnException(sprintf('Value %s is not a valid PublicKeyCredentialType.', $type));
        }

        $this->alg = $alg;
        $this->type = $type;
    }

    public function getAsArray(): array
    {
        return [
            'type' => $this->type,
            'alg' => $this->alg,
        ];
    }
}
