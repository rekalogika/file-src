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

use Http\Discovery\Psr17Factory;
use Psr\Http\Message\StreamInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Contracts\File\Trait\EqualityTrait;
use Rekalogika\Contracts\File\Trait\MetadataTrait;
use Rekalogika\Domain\File\Metadata\MetadataFactory;

/**
 * Null file is a file that does not exist. It is used in place of a null value
 * when the file should be present but is not. It avoids a fatal error in such
 * cases, while allowing the programmer or system administrator to know about
 * the problem.
 */
final class NullFile implements FileInterface
{
    use MetadataTrait;

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
        return new class implements FilePointerInterface
        {
            use EqualityTrait;

            public function getFilesystemIdentifier(): ?string
            {
                return null;
            }

            public function getKey(): string
            {
                return '/dev/null';
            }
        };
    }

    public function isEqualTo(FileInterface|FilePointerInterface $other): bool
    {
        return false;
    }

    public function isSameFilesystem(FileInterface|FilePointerInterface $other): bool
    {
        return false;
    }

    public function setContent(string $contents): void
    {
        throw new \LogicException('Cannot set the content of a null file');
    }

    public function setContentFromStream(mixed $stream): void
    {
        throw new \LogicException('Cannot set the content of a null file');
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
        throw new \LogicException('Cannot save from a null file');
    }

    public function createLocalTemporaryFile(): \SplFileInfo
    {
        throw new \LogicException('Cannot create a temporary file from a null file');
    }

    public function getDerivation(string $derivationId): FilePointerInterface
    {
        throw new \LogicException('Cannot derive a null file');
    }

    public function get(string $id)
    {
        if ($id == RawMetadataInterface::class) {
            return new NullFileMetadata();
        }

        /** @psalm-suppress MixedReturnStatement */
        return MetadataFactory::create(new NullFileMetadata())->get($id);
    }

    public function flush(): void
    {
    }
}
