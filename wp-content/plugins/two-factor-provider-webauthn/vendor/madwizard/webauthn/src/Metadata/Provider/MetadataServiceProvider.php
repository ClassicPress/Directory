<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Provider;

use DateTimeImmutable;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Attestation\TrustAnchor\MetadataInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Cache\CacheProviderInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\ParseException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\VerificationException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\WebAuthnException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\Base64UrlEncoding;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format\ByteBuffer;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Source\MetadataServiceSource;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Statement\MetadataStatement;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Metadata\Statement\MetadataToc;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\ChainValidatorInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\Jwt\Jwt;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\Jwt\JwtInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\Jwt\JwtValidator;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\Jwt\ValidationContext;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\Jwt\X5cParameterReader;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Pki\X509Certificate;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Remote\DownloaderInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Server\Registration\RegistrationResultInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Cache\CacheItemPoolInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\LoggerAwareInterface;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\LoggerAwareTrait;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\Psr\Log\NullLogger;

final class MetadataServiceProvider implements MetadataProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var DownloaderInterface
     */
    private $downloader;

    /**
     * @var CacheItemPoolInterface
     */
    private $cachePool;

    /**
     * @var CacheProviderInterface
     */
    private $cacheProvider;

    /**
     * @var ChainValidatorInterface
     */
    private $chainValidator;

    /**
     * Cache time for specific metadata statements. Because caching of metadata statements is done by the hash of its
     * contents (which is also included in the TOC) in theory these items can be cached indefinitely because any update
     * would cause the hash in the TOC to change and trigger a new download.
     * Set to 1 day by default to prevent keeping stale data.
     */
    private const METADATA_HASH_CACHE_TTL = 86400;

    /**
     * @var MetadataServiceSource
     */
    private $mdsSource;

    public function __construct(
        MetadataServiceSource $mdsSource,
        DownloaderInterface $downloader,
        CacheProviderInterface $cacheProvider,
        ChainValidatorInterface $chainValidator
    ) {
        $this->mdsSource = $mdsSource;
        $this->cachePool = $cacheProvider->getCachePool('metadataService');
        $this->downloader = $downloader;
        $this->cacheProvider = $cacheProvider;
        $this->chainValidator = $chainValidator;
        $this->logger = new NullLogger();
    }

    private function getTokenUrl(string $url): string
    {
        $token = $this->mdsSource->getAccessToken();
        if ($token === null) {
            return $url;
        }

        // Only add token if host matches host of main TOC URL to prevent leaking token to other hosts.
        $mainHost = parse_url($this->mdsSource->getUrl(), PHP_URL_HOST);
        $host = parse_url($url, PHP_URL_HOST);

        if (strcasecmp($mainHost, $host) === 0) {
            return $url . '?' . http_build_query(['token' => $token]);
        }
        return $url;
    }

    public function getMetadata(RegistrationResultInterface $registrationResult): ?MetadataInterface
    {
        $identifier = $registrationResult->getIdentifier();
        if ($identifier === null) {
            return null;
        }
        $toc = $this->getCachedToc();
        $tocItem = $toc->findItem($identifier);

        $this->logger->debug('Searching MDS for identifier {id}.', ['id' => $identifier->toString()]);

        if ($tocItem === null) {
            return null;
        }

        $url = $tocItem->getUrl();
        $hash = $tocItem->getHash();
        if ($url === null || $hash === null) {
            return null;
        }

        $meta = $this->getMetadataItem($url, $hash);
        $meta->setStatusReports($tocItem->getStatusReports());
        return $meta;
    }

    private function getMetadataItem(string $url, ByteBuffer $hash): MetadataStatement
    {
        $item = $this->cachePool->getItem($hash->getHex());

        if ($item->isHit()) {
            $meta = $item->get();
        } else {
            $urlWithToken = $this->getTokenUrl($url);
            $meta = $this->downloader->downloadFile($urlWithToken);
            $fileHash = hash('sha256', $meta->getData(), true);
            if (!hash_equals($hash->getBinaryString(), $fileHash)) {
                throw new VerificationException(sprintf('Hash mismatch for url %s, ignoring metadata entry.', $url));
            }

            $item->set($meta);
            $item->expiresAfter(self::METADATA_HASH_CACHE_TTL);
            $this->cachePool->save($item);
        }
        return MetadataStatement::decodeString(Base64UrlEncoding::decode($meta->getData()));
    }

    private function getCachedToc(): MetadataToc
    {
        $url = $this->getTokenUrl($this->mdsSource->getUrl());
        $urlHash = hash('sha256', $url);
        $item = $this->cachePool->getItem($urlHash);

        if ($item->isHit()) {
            $data = $item->get();
            if ($data instanceof MetadataToc) {
                if ($data->getNextUpdate() > new DateTimeImmutable()) {           // TODO: abstract time for unit tests
                    return $data;
                }
            }
        }

        $data = $this->downloadToc($url);

        if ($data !== null) {
            $item->set($data);
            $item->expiresAt($data->getNextUpdate());
            $this->cachePool->save($item);
        }
        return $data;
    }

    private function downloadToc(string $url): MetadataToc
    {
        $this->logger->debug('Dowloading TOC {url}', ['url' => preg_replace('~\?.*$~', '', $url)]);   // Remove parameters to hide token in logs
        $a = $this->downloader->downloadFile($url);
        if (!in_array(strtolower($a->getContentType()), ['application/octet-stream', 'application/jose'])) {
            throw new ParseException('Unexpected mime type.');
        }

        $jwt = new Jwt($a->getData());

        $x5cParam = X5cParameterReader::getX5cParameter($jwt);
        if ($x5cParam === null) {
            throw new ParseException('MDS has no x5c certificate chain in header.');
        }

        $jwtValidator = new JwtValidator();
        $context = new ValidationContext(JwtInterface::ES_AND_RSA, $x5cParam->getCoseKey());
        try {
            $claims = $jwtValidator->validate($jwt, $context);
        } catch (WebAuthnException $e) {
            throw new VerificationException(sprintf('Failed to verify JWT: %s', $e->getMessage()), 0, $e);
        }

        if (!$this->chainValidator->validateChain(X509Certificate::fromPem($this->mdsSource->getRootCert()), ...array_reverse($x5cParam->getCertificates()))) {
            throw new VerificationException('Failed to verify x5c chain in JWT.');
        }
        return MetadataToc::fromJson($claims);
    }

    public function getDescription(): string
    {
        return sprintf('Metadata service url=%s', $this->mdsSource->getUrl());
    }
}
