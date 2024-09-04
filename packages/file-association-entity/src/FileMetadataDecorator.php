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

use Rekalogika\Contracts\File\RawMetadataInterface;

/**
 * @implements \IteratorAggregate<string,int|string|bool|null>
 */
class FileMetadataDecorator implements RawMetadataInterface, \IteratorAggregate
{
    /**
     * @param RawMetadataInterface $embeddedMetadata Metadata embedded in entities
     * @param RawMetadataInterface $fileMetadata Metadata from the real file
     */
    public function __construct(
        private readonly RawMetadataInterface $embeddedMetadata,
        private readonly RawMetadataInterface $fileMetadata,
    ) {}

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
        $this->fileMetadata->set($key, $value);
    }

    #[\Override]
    public function delete(string $key): void
    {
        $this->embeddedMetadata->delete($key);
        $this->fileMetadata->delete($key);
    }

    #[\Override]
    public function merge(iterable $metadata): void
    {
        $this->embeddedMetadata->merge($metadata);
        $this->fileMetadata->merge($metadata);
    }

    #[\Override]
    public function count(): int
    {
        return $this->embeddedMetadata->count();
    }
}
