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

namespace Rekalogika\File\Zip\Model;

use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\NodeInterface;
use Rekalogika\Domain\File\Metadata\Model\FileName;

/**
 * @implements \IteratorAggregate<array-key,NodeInterface>
 * @implements DirectoryInterface<array-key,NodeInterface>
 */
final class Directory implements DirectoryInterface, \IteratorAggregate
{
    /**
     * @var array<int,Directory|FilePointer>
     */
    private array $entries = [];

    public function __construct(
        private ?string $name = null,
        private ?Directory $parent = null
    ) {
    }

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
        return count($this->entries);
    }

    public function getContainingDirectory(): ?Directory
    {
        return $this->parent;
    }

    public function addPointer(FilePointerInterface $pointer): void
    {
        $this->entries[] = FilePointer::createFromInterface($pointer, $this);
    }

    public function createDirectory(string $name): self
    {
        $directory = new self($name, $this);
        $this->entries[] = $directory;

        return $directory;
    }
}
