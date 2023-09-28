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
        private RawMetadataInterface $embeddedMetadata,
        private RawMetadataInterface $fileMetadata,
    ) {
    }

    public function getIterator(): \Traversable
    {
        yield from $this->embeddedMetadata;
    }

    public function get(string $key): int|string|bool|null
    {
        return $this->embeddedMetadata->get($key);
    }

    public function tryGet(string $key): int|string|bool|null
    {
        return $this->embeddedMetadata->tryGet($key);
    }

    public function set(string $key, int|string|bool|null $value): void
    {
        $this->embeddedMetadata->set($key, $value);
        $this->fileMetadata->set($key, $value);
    }

    public function delete(string $key): void
    {
        $this->embeddedMetadata->delete($key);
        $this->fileMetadata->delete($key);
    }

    public function merge(iterable $metadata): void
    {
        $this->embeddedMetadata->merge($metadata);
        $this->fileMetadata->merge($metadata);
    }

    public function count(): int
    {
        return $this->embeddedMetadata->count();
    }
}
