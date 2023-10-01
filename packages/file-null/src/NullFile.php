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

use Http\Discovery\Psr17Factory;
use Psr\Http\Message\StreamInterface;
use Rekalogika\Contracts\File\Exception\File\DerivationNotSupportedException;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Contracts\File\FileTypeInterface;
use Rekalogika\Contracts\File\NullFileInterface;

/**
 * A null-value pattern implementation for FileInterface. Usually used in place
 * of a null value when the file is expected to be present, but it is not.
 */
class NullFile extends \Exception implements NullFileInterface
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

    public function getName(): FileNameInterface
    {
        return new NullName;
    }

    public function setName(?string $fileName): void
    {
    }

    public function setContent(string $contents): void
    {
    }

    public function setContentFromStream(mixed $stream): void
    {
    }

    public function getContent(): string
    {
        return '';
    }

    public function getContentAsStream(): StreamInterface
    {
        $stream = fopen('php://memory', 'r+');
        if ($stream === false) {
            throw new \RuntimeException('Cannot create stream');
        }

        fwrite($stream, '');
        rewind($stream);

        return (new Psr17Factory())->createStreamFromResource($stream);
    }

    public function saveToLocalFile(string $path): \SplFileInfo
    {
        return new \SplFileInfo('/dev/null');
    }

    public function createLocalTemporaryFile(): \SplFileInfo
    {
        return new \SplFileInfo('/dev/null');
    }

    public function getType(): FileTypeInterface
    {
        return new NullType;
    }

    public function setType(string $type): void
    {
    }

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
