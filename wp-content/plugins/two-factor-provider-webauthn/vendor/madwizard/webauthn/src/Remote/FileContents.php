<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Remote;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\SerializableTrait;
use Serializable;

class FileContents implements Serializable
{
    use SerializableTrait;

    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $contentType;

    public function __construct(string $data, string $contentType)
    {
        $this->data = $data;
        $this->contentType = $contentType;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function __serialize(): array
    {
        return [
            'contentType' => $this->contentType,
            'data' => $this->data,
        ];
    }

    public function __unserialize(array $serialized): void
    {
        $this->contentType = $serialized['contentType'];
        $this->data = $serialized['data'];
    }
}
