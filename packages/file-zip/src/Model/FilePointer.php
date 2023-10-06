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

use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\Trait\EqualityTrait;

final class FilePointer implements FilePointerInterface
{
    use EqualityTrait;

    public static function createFromInterface(
        FilePointerInterface $filePointer,
        Directory $directory
    ): self {
        return new self(
            $filePointer->getFilesystemIdentifier(),
            $filePointer->getKey(),
            $directory
        );
    }

    public function __construct(
        private ?string $filesystemIdentifier,
        private string $key,
        private ?Directory $directory = null
    ) {
    }

    public function getFilesystemIdentifier(): ?string
    {
        return $this->filesystemIdentifier;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getContainingDirectory(): ?Directory
    {
        return $this->directory;
    }
}
