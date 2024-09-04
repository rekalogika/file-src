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

use Http\Discovery\Psr17Factory;
use League\Flysystem\FilesystemOperator;
use Psr\Http\Message\StreamInterface;
use Rekalogika\Contracts\File\Exception\File\DerivationNotSupportedException;
use Rekalogika\Contracts\File\Exception\File\FatalErrorException;
use Rekalogika\Contracts\File\Exception\File\FileNotFoundException;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Contracts\File\Trait\EqualityTrait;
use Rekalogika\Contracts\File\Trait\MetadataTrait;
use Rekalogika\Domain\File\Metadata\Constants;
use Rekalogika\Domain\File\Metadata\MetadataFactory;
use Rekalogika\File\Contracts\MetadataAwareFilesystemReader;
use Rekalogika\File\Contracts\MetadataAwareFilesystemWriter;
use Rekalogika\File\Repository\FilesystemRepository;

class File implements FileInterface
{
    use EqualityTrait;
    use MetadataTrait;

    private ?RawMetadata $metadataCache = null;

    private FilesystemOperator $filesystem;

    private bool $isAdHocFilesystem = false;

    private bool $isLocalFilesystem;

    /**
     * If only the $key is provided, then the filesystem is the local
     * filesystem. This is the primary usage when intantiated directly by end
     * users.
     *
     * If $filesystem is provided, but $filesystemIdentifier is not, then the
     * filesystem becomes an ad-hoc filesystem. An ad-hoc filesystem does not
     * come from the filesystem repository. This usually happens when using
     * adapters to convert file objects from other library.
     *
     * If both $filesystem and $filesystemIdentifier are provided, then the
     * filesystem is a registered filesystem in the filesystem repository. This
     * should only happen is the file is instantiated by the file repository.
     */
    public function __construct(
        private string $key,
        ?FilesystemOperator $filesystem = null,
        private ?string $filesystemIdentifier = null,
    ) {
        if (
            $filesystem !== null
            && $filesystemIdentifier === null
        ) {
            $this->filesystemIdentifier = bin2hex(random_bytes(16));
            $this->isAdHocFilesystem = true;
        } elseif (
            $filesystem === null
            && $filesystemIdentifier !== null
        ) {
            throw new \InvalidArgumentException('Filesystem is required when filesystem identifier is provided.');
        }

        // if the filesystem is not provided, assume local filesystem

        if ($filesystem === null) {
            $this->filesystem = FilesystemRepository::getLocalFilesystem();
            $this->isLocalFilesystem = true;

            $realpath = realpath($key);

            if ($realpath === false) {
                throw new FileNotFoundException($key);
            }

            if (!file_exists($realpath)) {
                throw new FileNotFoundException($key);
            }

            $this->key = $realpath;
        } else {
            $this->filesystem = $filesystem;
            $this->isLocalFilesystem = false;
        }
    }

    /**
     * @return array<array-key,mixed>
     */
    public function __serialize(): array
    {
        throw new \LogicException('Serialization is not supported. Use getPointer() to get a pointer to this file and serialize it instead.');
    }

    #[\Override]
    public function getPointer(): FilePointerInterface
    {
        return new FilePointer(
            $this->getFilesystemIdentifier(),
            $this->key,
        );
    }

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

    protected function getFilesystem(): FilesystemOperator
    {
        return $this->filesystem;
    }

    #[\Override]
    public function setContent(string $contents): void
    {
        // restore original filename if it is a local filesystem. because
        // the user has potentially set the filename before calling this method

        $oldFileName = null;
        if ($this->isLocalFilesystem()) {
            $oldFileName = $this->getRawMetadata()->tryGet(Constants::FILE_NAME);
        }

        $this->filesystem->write($this->key, $contents);
        $this->metadataCache = null;

        if ($this->isLocalFilesystem()) {
            $this->getRawMetadata()->set(Constants::FILE_NAME, $oldFileName);
        }
    }

    #[\Override]
    public function setContentFromStream(mixed $stream): void
    {
        $oldFileName = null;
        if ($this->isLocalFilesystem()) {
            $oldFileName = $this->getRawMetadata()->tryGet(Constants::FILE_NAME);
        }

        if ($stream instanceof StreamInterface) {
            $stream = $stream->detach();
        }

        if (!$stream) {
            throw new \InvalidArgumentException('Invalid stream');
        }

        $this->filesystem->writeStream($this->key, $stream);
        $this->metadataCache = null;

        if ($this->isLocalFilesystem()) {
            $this->getRawMetadata()->set(Constants::FILE_NAME, $oldFileName);
        }
    }

    #[\Override]
    public function getContent(): string
    {
        return $this->filesystem->read($this->key);
    }

    #[\Override]
    public function getContentAsStream(): StreamInterface
    {
        $stream = $this->filesystem->readStream($this->key);

        return (new Psr17Factory())->createStreamFromResource($stream);
    }

    #[\Override]
    public function saveToLocalFile(string $path): \SplFileInfo
    {
        $output = fopen($path, 'wb');
        $input = $this->getContentAsStream()->detach();

        if ($input === null) {
            throw new FatalErrorException('Failed to create temporary file');
        }

        if ($output === false) {
            throw new FatalErrorException('Failed to create temporary file');
        }

        $result = stream_copy_to_stream($input, $output);

        if ($result === false) {
            throw new FatalErrorException('Failed to create temporary file');
        }

        return new \SplFileInfo($path);
    }

    #[\Override]
    public function createLocalTemporaryFile(): \SplFileInfo
    {
        $temporaryFile = LocalTemporaryFile::create();
        $input = $this->getContentAsStream()->detach();

        if (null === $input) {
            throw new FatalErrorException('Failed to create temporary file');
        }

        $output = fopen($temporaryFile->getPathname(), 'wb');

        if ($output === false) {
            throw new FatalErrorException('Failed to create temporary file');
        }

        $result = stream_copy_to_stream($input, $output);

        if ($result === false) {
            throw new FatalErrorException('Failed to create temporary file');
        }

        return $temporaryFile;
    }

    protected function getRawMetadata(): RawMetadata
    {
        if ($this->metadataCache !== null) {
            return $this->metadataCache;
        }

        $filesystem = $this->filesystem;

        if ($filesystem instanceof MetadataAwareFilesystemReader) {
            $metadata = $filesystem->getMetadata($this->key);
        } else {
            $metadata = [];
        }

        if (!$metadata instanceof RawMetadata) {
            $metadata = new RawMetadata($metadata);
        }

        return $this->metadataCache = $metadata;
    }

    #[\Override]
    public function getDerivation(string $derivationId): FilePointerInterface
    {
        if ($this->getFilesystemIdentifier() === null) {
            throw new DerivationNotSupportedException('It is not allowed to create derivation from a file in an unbounded local filesystem.');
        }

        if ($this->hasAdHocFilesystem()) {
            throw new DerivationNotSupportedException('It is not allowed to create derivation from a file in an ad-hoc filesystem.');
        }

        if ((bool) preg_match('/^[a-zA-Z0-9_-]+$/', $derivationId) === false) {
            throw new \InvalidArgumentException('Derivation ID must consist of alphanumeric characters, dash, or underscore only.');
        }

        $derivationKey = \sprintf('%s.d/%s', $this->key, $derivationId);

        return new FilePointer(
            $this->getFilesystemIdentifier(),
            $derivationKey,
        );
    }

    #[\Override]
    public function get(string $id)
    {
        if ($id === RawMetadataInterface::class) {
            return $this->getRawMetadata();
        }

        /** @psalm-suppress MixedReturnStatement */
        return MetadataFactory::create($this->getRawMetadata())->get($id);
    }

    #[\Override]
    public function flush(): void
    {
        $filesystem = $this->filesystem;

        if (!$filesystem instanceof MetadataAwareFilesystemWriter) {
            return;
        }

        $filesystem->setMetadata($this->key, $this->getRawMetadata());
    }

    protected function hasAdHocFilesystem(): bool
    {
        return $this->isAdHocFilesystem;
    }

    public function isLocalFilesystem(): bool
    {
        return $this->isLocalFilesystem;
    }
}
