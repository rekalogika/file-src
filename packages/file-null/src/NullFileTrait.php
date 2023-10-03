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
use Rekalogika\Contracts\File\Exception\File\NullFileOperationException;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;

/**
 * Trait to be implemented by null file objects.
 */
trait NullFileTrait
{
    private function throwException(string $message): never
    {
        if ($this instanceof \Throwable) {
            throw new NullFileOperationException($message, 0, $this);
        } else {
            throw new NullFileOperationException($message);
        }
    }

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
        $this->throwException('Cannot set the name of a null file');
    }

    public function setContent(string $contents): void
    {
        $this->throwException('Cannot set the content of a null file');
    }

    public function setContentFromStream(mixed $stream): void
    {
        $this->throwException('Cannot set the content of a null file');
    }

    public function getContent(): string
    {
        $this->throwException('Cannot get the content of a null file');
    }

    public function getContentAsStream(): StreamInterface
    {
        $this->throwException('Cannot get the content of a null file');
    }

    public function saveToLocalFile(string $path): \SplFileInfo
    {
        $this->throwException('Cannot save a null file');
    }

    public function createLocalTemporaryFile(): \SplFileInfo
    {
        $this->throwException('Cannot create a temporary file from a null file');
    }

    public function setType(string $type): void
    {
        $this->throwException('Cannot set the type of a null file');
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
        $this->throwException('Cannot derive from a null file');
    }

    public function get(string $id)
    {
        return null;
    }

    public function flush(): void
    {
    }
}
