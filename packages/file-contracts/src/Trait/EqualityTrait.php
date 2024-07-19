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

namespace Rekalogika\Contracts\File\Trait;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\NullFileInterface;
use Rekalogika\Contracts\File\NullFilePointerInterface;

trait EqualityTrait
{
    abstract public function getFilesystemIdentifier(): ?string;

    abstract public function getKey(): string;

    public function isEqualTo(FilePointerInterface|FileInterface $other): bool
    {
        return !$other instanceof NullFileInterface
            && !$other instanceof NullFilePointerInterface
            && $this->isSameFilesystem($other)
            && $this->getKey() === $other->getKey();
    }

    public function isSameFilesystem(FilePointerInterface|FileInterface $other): bool
    {
        return !$other instanceof NullFileInterface
            && !$other instanceof NullFilePointerInterface
            && $this->getFilesystemIdentifier() === $other->getFilesystemIdentifier();
    }
}
