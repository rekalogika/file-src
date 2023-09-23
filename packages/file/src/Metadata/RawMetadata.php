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

namespace Rekalogika\File\Metadata;

use Rekalogika\Contracts\File\RawMetadataInterface;

/**
 * @implements \IteratorAggregate<string,int|string|bool|null>
 */
class RawMetadata implements RawMetadataInterface, \IteratorAggregate
{
    /**
     * @var array<string,int|string|bool|null> $metadata
     */
    private array $metadata = [];

    /**
     * @param iterable<string,int|string|bool|null> $metadata
     */
    public function __construct(iterable $metadata = [])
    {
        $this->merge($metadata);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->metadata;
    }

    public function get(string $key): int|string|bool|null
    {
        if (!array_key_exists($key, $this->metadata)) {
            return null;
        }

        return $this->metadata[$key];
    }

    public function set(string $key, int|string|bool|null $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function delete(string $key): void
    {
        unset($this->metadata[$key]);
    }

    public function merge(iterable $metadata): void
    {
        foreach ($metadata as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function count(): int
    {
        return \count($this->metadata);
    }
}
