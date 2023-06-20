<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Source;

class StatementDirectorySource implements MetadataSourceInterface
{
    /**
     * @var string
     */
    private $metadataDir;

    public function __construct(string $metadataDir)
    {
        $this->metadataDir = $metadataDir;
    }

    public function getMetadataDir(): string
    {
        return $this->metadataDir;
    }
}
