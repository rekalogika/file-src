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

use Psr\Http\Message\StreamInterface;
use Rekalogika\Contracts\File\Exception\File\DerivationNotSupportedException;
use Rekalogika\Contracts\File\Exception\File\NullFileOperationException;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;

/**
 * Trait to be implemented by null file objects.
 */
trait NullFileTrait
{
    public function getFilesystemIdentifier(): ?string
    {
        return null;
    }

    public function getKey(): string
    {
        return '/dev/null';
    }

    public function getPointer(): FilePointerInterface
    {
        return new NullPointer();
    }

    public function isEqualTo(FileInterface|FilePointerInterface $other): bool
    {
        return false;
    }

    public function isSameFilesystem(FileInterface|FilePointerInterface $other): bool
    {
        return false;
    }

    public function setName(?string $fileName): void
    {
        throw new NullFileOperationException('Cannot set the name of a null file');
    }

    public function setContent(string $contents): void
    {
        throw new NullFileOperationException('Cannot set the content of a null file');
    }

    public function setContentFromStream(mixed $stream): void
    {
        throw new NullFileOperationException('Cannot set the content of a null file');
    }

    public function getContent(): string
    {
        throw new NullFileOperationException('Cannot get the content of a null file');
    }

    public function getContentAsStream(): StreamInterface
    {
        throw new NullFileOperationException('Cannot get the content of a null file');
    }

    public function saveToLocalFile(string $path): \SplFileInfo
    {
        throw new NullFileOperationException('Cannot save a null file');
    }

    public function createLocalTemporaryFile(): \SplFileInfo
    {
        throw new NullFileOperationException('Cannot create a temporary file from a null file');
    }

    public function setType(string $type): void
    {
        throw new NullFileOperationException('Cannot set the type of a null file');
    }

    /**
     * @return int<0,max>
     */
    public function getSize(): int
    {
        return 0;
    }

    public function getLastModified(): \DateTimeInterface
    {
        return new \DateTimeImmutable();
    }

    public function getDerivation(string $derivationId): FilePointerInterface
    {
        throw new DerivationNotSupportedException('Cannot derive from a null file');
    }

    public function get(string $id)
    {
        return null;
    }

    public function flush(): void
    {
    }
}
