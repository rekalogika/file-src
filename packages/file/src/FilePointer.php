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

use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\Trait\EqualityTrait;

class FilePointer implements FilePointerInterface
{
    use EqualityTrait;

    public function __construct(
        private ?string $filesystemIdentifier,
        private string $key,
    ) {}

    #[\Override]
    public function getFilesystemIdentifier(): ?string
    {
        return $this->filesystemIdentifier;
    }

    #[\Override]
    public function getKey(): string
    {
        return $this->key;
    }
}
