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

namespace Rekalogika\Domain\File\Association\Entity;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\File\RawMetadata;

/**
 * @implements \IteratorAggregate<string,int|string|bool|null>
 */
final class FileMetadataDecorator implements RawMetadataInterface, \IteratorAggregate
{
    private ?RawMetadataInterface $cachedFileMetadata = null;

    /**
     * @param RawMetadataInterface $embeddedMetadata Metadata embedded in entities
     * @param FileInterface $file The underlying file object from which metadata will be lazily loaded
     */
    public function __construct(
        private readonly RawMetadataInterface $embeddedMetadata,
        private readonly FileInterface $file,
    ) {}

    private function getFileMetadata(): RawMetadataInterface
    {
        return $this->cachedFileMetadata
            ??= $this->file->get(RawMetadataInterface::class) ?? new RawMetadata();
    }

    #[\Override]
    public function getIterator(): \Traversable
    {
        yield from $this->embeddedMetadata;
    }

    #[\Override]
    public function get(string $key): int|string|bool|null
    {
        return $this->embeddedMetadata->get($key);
    }

    #[\Override]
    public function tryGet(string $key): int|string|bool|null
    {
        return $this->embeddedMetadata->tryGet($key);
    }

    #[\Override]
    public function set(string $key, int|string|bool|null $value): void
    {
        $this->embeddedMetadata->set($key, $value);
        $this->getFileMetadata()->set($key, $value);
    }

    #[\Override]
    public function delete(string $key): void
    {
        $this->embeddedMetadata->delete($key);
        $this->getFileMetadata()->delete($key);
    }

    #[\Override]
    public function merge(iterable $metadata): void
    {
        $this->embeddedMetadata->merge($metadata);
        $this->getFileMetadata()->merge($metadata);
    }

    #[\Override]
    public function count(): int
    {
        return $this->embeddedMetadata->count();
    }
}
