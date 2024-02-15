<?php

declare(strict_types=1);

/*
 * This file is part of rekalogika/file-src package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Rekalogika\Domain\File\Metadata\Metadata;

use cardinalby\ContentDisposition\ContentDisposition;
use Rekalogika\Contracts\File\Metadata\HttpMetadataInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Metadata\Constants;

final class HttpMetadata extends AbstractMetadata implements
    HttpMetadataInterface
{
    public static function create(
        RawMetadataInterface $metadata
    ): static {
        return new static($metadata);
    }

    private function __construct(
        private RawMetadataInterface $metadata
    ) {
    }

    public function getDate(): string
    {
        return (new \DateTimeImmutable())->format(\DateTimeInterface::RFC7231);
    }

    public function getCacheControl(): ?string
    {
        $data = $this->metadata->tryGet(Constants::HTTP_CACHE_CONTROL);
        if ($data === null) {
            return null;
        }

        return (string) $data;
    }

    public function setCacheControl(?string $cacheControl): void
    {
        if ($cacheControl === null) {
            $this->metadata->delete(Constants::HTTP_CACHE_CONTROL);
            return;
        }

        $this->metadata->set(Constants::HTTP_CACHE_CONTROL, $cacheControl);
    }

    public function getDisposition(): string
    {
        return (string) ($this->metadata->tryGet(Constants::HTTP_DISPOSITION) ?? 'inline');
    }

    public function setDisposition(string $disposition): void
    {
        if (!\in_array($disposition, ['inline', 'attachment'], true)) {
            throw new \InvalidArgumentException('Invalid disposition');
        }

        $this->metadata->set(Constants::HTTP_DISPOSITION, $disposition);
    }

    private function getContentDisposition(?string $disposition = null): string
    {
        $disposition = $disposition ?? $this->getDisposition();

        return ContentDisposition::create(
            $this->getFileName(),
            true,
            $disposition,
        )->format();
    }

    private function getFileName(): ?string
    {
        $fileName = $this->metadata->tryGet(Constants::FILE_NAME);

        return $fileName === null ? null : (string) $fileName;
    }

    private function getContentLength(): ?string
    {
        $contentLength = $this->metadata->tryGet(Constants::FILE_SIZE);
        if ($contentLength === null) {
            return null;
        }

        $contentLength = (int) $contentLength;
        if ($contentLength === 0) {
            return null;
        }

        return (string) $contentLength;
    }

    private function getContentType(): string
    {
        return (string) ($this->metadata->tryGet(Constants::FILE_TYPE) ?? 'application/octet-stream');
    }

    private function getLastModified(): ?string
    {
        $lastModified = $this->metadata->tryGet(Constants::FILE_MODIFICATION_TIME);
        $lastModified = \DateTimeImmutable::createFromFormat('U', (string) $lastModified);

        return $lastModified === false ? null : $lastModified->format(\DateTimeInterface::RFC7231);
    }

    public function getETag(): ?string
    {
        $eTag = $this->metadata->tryGet(Constants::HTTP_ETAG);

        return $eTag === null ? null : (string) $eTag;
    }

    private function getWidth(): ?string
    {
        $width = $this->metadata->tryGet(Constants::MEDIA_WIDTH);

        return $width === null ? null : (string) $width;
    }

    private function getHeight(): ?string
    {
        $height = $this->metadata->tryGet(Constants::MEDIA_HEIGHT);

        return $height === null ? null : (string) $height;
    }

    /**
     * @return iterable<string,string>
     */
    public function getHeaders(?string $disposition = null): iterable
    {
        yield 'Date' => $this->getDate();

        if (($cacheControl = $this->getCacheControl()) !== null) {
            yield 'Cache-Control' => $cacheControl;
        }

        yield 'Content-Disposition' => $this->getContentDisposition($disposition);

        if (($contentLength = $this->getContentLength()) !== null) {
            yield 'Content-Length' => $contentLength;
        }

        yield 'Content-Type' => $this->getContentType();

        if (($lastModified = $this->getLastModified()) !== null) {
            yield 'Last-Modified' => $lastModified;
        }

        if (($eTag = $this->getETag()) !== null) {
            yield 'ETag' => $eTag;
        }

        if (($width = $this->getWidth()) !== null) {
            yield 'X-Width' => $width;
        }

        if (($height = $this->getHeight()) !== null) {
            yield 'X-Height' => $height;
        }
    }
}
