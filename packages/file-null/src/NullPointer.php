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

namespace Rekalogika\Domain\File\Null;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\NullFilePointerInterface;

class NullPointer implements NullFilePointerInterface
{
    #[\Override]
    public function getFilesystemIdentifier(): ?string
    {
        return null;
    }

    #[\Override]
    public function getKey(): string
    {
        return '/dev/null';
    }

    #[\Override]
    public function isEqualTo(FileInterface|FilePointerInterface $other): bool
    {
        return false;
    }

    #[\Override]
    public function isSameFilesystem(FileInterface|FilePointerInterface $other): bool
    {
        return false;
    }
}
