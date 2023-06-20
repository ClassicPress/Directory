<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Statement;

use DateTimeImmutable;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Identifier\Aaguid;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Identifier\Aaid;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Identifier\AttestationKeyIdentifier;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\Identifier\IdentifierInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\DataValidationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\VerificationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\DataValidator;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\SerializableTrait;
use Serializable;

class MetadataToc implements Serializable
{
    use SerializableTrait;

    /**
     * @var DateTimeImmutable
     */
    private $nextUpdate;

    /**
     * @var array
     */
    private $index = [];

    public static function fromJson(array $json): self
    {
        try {
            DataValidator::checkArray($json, [
                'nextUpdate' => 'string',
                'entries' => 'array',
            ], false);
        } catch (DataValidationException $e) {
            throw new VerificationException(sprintf('Unexpected or missing entries in MDS: %s.', $e->getMessage()), 0, $e);
        }

        $toc = new self();

        $toc->nextUpdate = new DateTimeImmutable($json['nextUpdate']); // TODO validate date?
        foreach ($json['entries'] as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            if (is_string($entry['aaguid'] ?? false)) {
                $toc->index[Aaguid::TYPE][strtolower($entry['aaguid'])] = $entry;
            }
            if (is_string($entry['aaid'] ?? false)) {
                $toc->index[Aaid::TYPE][strtoupper($entry['aaid'])] = $entry;
            }
            if (is_array($entry['attestationCertificateKeyIdentifiers'] ?? false)) {
                foreach ($entry['attestationCertificateKeyIdentifiers'] as $id) {
                    if (is_string($id)) {
                        $toc->index[AttestationKeyIdentifier::TYPE][strtolower($id)] = $entry;
                    }
                }
            }
        }
        return $toc;
    }

    public function getNextUpdate(): DateTimeImmutable
    {
        return $this->nextUpdate;
    }

    public function findItem(IdentifierInterface $identifier): ?TocItem
    {
        $entry = $this->index[$identifier->getType()][$identifier->toString()] ?? null;
        if ($entry === null) {
            return null;
        }

        DataValidator::checkArray($entry, [
            'url' => '?string',
            'hash' => '?string',
            'statusReports' => '?array',
        ], false);

        $statusReports = array_map(function ($item) {
            if (!is_array($item)) {
                throw new ParseException('Invalid status report.');
            }
            return StatusReport::fromArray($item);
        }, $entry['statusReports']);

        return new TocItem(
            $identifier,
            $entry['hash'] === null ? null : ByteBuffer::fromBase64Url($entry['hash']),
            $entry['url'],
            $statusReports
        );
    }

    public function __serialize(): array
    {
        return [
            'nextUpdate' => $this->nextUpdate,
            'index' => $this->index,
        ];
    }

    public function __unserialize(array $serialized): void
    {
        $this->nextUpdate = $serialized['nextUpdate'];
        $this->index = $serialized['index'];
    }
}
