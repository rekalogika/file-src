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

use Rekalogika\Contracts\File\Exception\MetadataNotFoundException;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Metadata\Constants;

/**
 * A Doctrine embeddable designed to store file metadata inside entities.
 *
 * @implements \IteratorAggregate<string,int|string|bool|null>
 */
class NullFileMetadata implements RawMetadataInterface, \IteratorAggregate
{
    public function getIterator(): \Traversable
    {
        yield Constants::FILE_NAME => '/dev/null';
        yield Constants::FILE_SIZE => 0;
        yield Constants::FILE_TYPE => 'application/x-zerosize';
        yield Constants::FILE_MODIFICATION_TIME => (new \DateTimeImmutable)->getTimestamp();
    }

    public function get(string $key): int|string|bool|null
    {
        return match ($key) {
            Constants::FILE_NAME => '/dev/null',
            Constants::FILE_SIZE => 0,
            Constants::FILE_TYPE => 'application/x-zerosize',
            Constants::FILE_MODIFICATION_TIME => (new \DateTimeImmutable)->getTimestamp(),
            default => throw new MetadataNotFoundException($key),
        };
    }

    public function tryGet(string $key): int|string|bool|null
    {
        try {
            return $this->get($key);
        } catch (MetadataNotFoundException) {
            return null;
        }
    }

    public function set(string $key, int|string|bool|null $value): void
    {
    }

    public function delete(string $key): void
    {
    }

    public function merge(iterable $metadata): void
    {
    }

    public function count(): int
    {
        return 4;
    }
}
