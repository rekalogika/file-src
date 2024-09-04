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

namespace Rekalogika\File;

use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Contracts\File\NodeInterface;
use Rekalogika\Domain\File\Metadata\Model\FileName;

/**
 * A simple implementation of DirectoryInterface
 *
 * @template TKey of array-key
 * @template T of NodeInterface
 * @implements \IteratorAggregate<TKey,T>
 * @implements DirectoryInterface<TKey,T>
 */
class Directory implements DirectoryInterface, \IteratorAggregate
{
    /**
     * @param array<TKey,T> $entries
     */
    public function __construct(
        private readonly string $name,
        private readonly array $entries = [],
    ) {}

    #[\Override]
    public function getIterator(): \Traversable
    {
        yield from $this->entries;
    }

    #[\Override]
    public function getName(): FileNameInterface
    {
        return new FileName($this->name);
    }

    #[\Override]
    public function count(): int
    {
        return \count($this->entries);
    }
}
