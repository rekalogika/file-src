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

namespace Rekalogika\File\Repository;

use League\Flysystem\FilesystemOperator;
use Psr\Http\Message\StreamInterface;
use Rekalogika\Contracts\File\Exception\File\FileNotFoundException;
use Rekalogika\Contracts\File\Exception\FileRepository\AdHocFilesystemException;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\FileProxy;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\File\Contracts\FilesystemRepositoryInterface;
use Rekalogika\File\Contracts\MetadataAwareFilesystemReader;
use Rekalogika\File\Contracts\MetadataAwareFilesystemWriter;
use Rekalogika\File\Exception\FilesystemRepository\FilesystemNotFoundException;
use Rekalogika\File\File;
use Rekalogika\File\MetadataGenerator\MetadataGeneratorInterface;
use Rekalogika\File\RawMetadata;
use Rekalogika\File\TemporaryFile;

class FileRepository implements FileRepositoryInterface
{
    /**
     * @var array<string,FileInterface>
     */
    private array $fileCache = [];


    public function __construct(
        private readonly FilesystemRepositoryInterface $filesystemRepository,
        private readonly MetadataGeneratorInterface $metadataGenerator,
        private readonly ?string $defaultFilesystemIdForTemporaryFile = null,
    ) {}

    #[\Override]
    public function clear(): void
    {
        $this->fileCache = [];
    }

    private function getFilesystemFromPointerOrFile(
        FilePointerInterface|FileInterface $file,
    ): FilesystemOperator {
        try {
            $identifier = $file->getFilesystemIdentifier();

            return $this->filesystemRepository->getFilesystem($identifier);
        } catch (FilesystemNotFoundException $e) {
            if ($file instanceof FileInterface) {
                throw new AdHocFilesystemException($file, $e);
            }

            throw $e;
        }
    }

    #[\Override]
    public function createFromString(
        FilePointerInterface $filePointer,
        string $contents,
        iterable $metadata = [],
    ): FileInterface {
        $this->getFilesystemFromPointerOrFile($filePointer)
            ->write($filePointer->getKey(), $contents, [
                'metadata' => $metadata,
            ]);

        return $this->get($filePointer);
    }

    #[\Override]
    public function createFromStream(
        FilePointerInterface $filePointer,
        mixed $stream,
        iterable $metadata = [],
    ): FileInterface {
        if ($stream instanceof StreamInterface) {
            $stream = $stream->detach();
        }

        if (!$stream) {
            throw new \InvalidArgumentException('Invalid stream');
        }

        $this->getFilesystemFromPointerOrFile($filePointer)
            ->writeStream($filePointer->getKey(), $stream, [
                'metadata' => $metadata,
            ]);

        return $this->get($filePointer);
    }

    #[\Override]
    public function createFromLocalFile(
        FilePointerInterface $filePointer,
        string $localFilePath,
        iterable $metadata = [],
    ): FileInterface {
        $newMetadata = new RawMetadata();

        $this->metadataGenerator
            ->generateMetadataFromFile($newMetadata, $localFilePath);
        $newMetadata->merge($metadata);

        $stream = fopen($localFilePath, 'rb');

        $this->getFilesystemFromPointerOrFile($filePointer)
            ->writeStream($filePointer->getKey(), $stream, [
                'metadata' => $newMetadata,
                'bypass_metadata_generation' => true,
            ]);

        return $this->get($filePointer);
    }

    #[\Override]
    public function get(FilePointerInterface $filePointer): FileInterface
    {
        $hash = $this->getFilePointerHash($filePointer);

        if (isset($this->fileCache[$hash])) {
            return $this->fileCache[$hash];
        }

        $filesystem = $this->getFilesystemFromPointerOrFile($filePointer);

        if (!$filesystem->fileExists($filePointer->getKey())) {
            throw new FileNotFoundException(
                $filePointer->getKey(),
                $filePointer->getFilesystemIdentifier(),
            );
        }

        if ($filePointer->getFilesystemIdentifier() === null) {
            return $this->fileCache[$hash] = new File(
                $filePointer->getKey(),
            );
        }

        return $this->fileCache[$hash] = new File(
            $filePointer->getKey(),
            $this->getFilesystemFromPointerOrFile($filePointer),
            $filePointer->getFilesystemIdentifier(),
        );
    }

    #[\Override]
    public function tryGet(FilePointerInterface $filePointer): ?FileInterface
    {
        try {
            return $this->get($filePointer);
        } catch (FileNotFoundException) {
            return null;
        }
    }

    #[\Override]
    public function getReference(FilePointerInterface $filePointer): FileInterface
    {
        $hash = $this->getFilePointerHash($filePointer);

        return $this->fileCache[$hash] ?? new FileProxy($filePointer, $this);
    }

    private function getMetadata(
        FilePointerInterface|FileInterface $file,
    ): RawMetadataInterface {
        if ($file instanceof FileInterface) {
            $metadata = $file->get(RawMetadataInterface::class);

            if (!$metadata) {
                throw new \InvalidArgumentException(
                    \sprintf(
                        'File "%s" does not have metadata',
                        $file->getKey(),
                    ),
                );
            }

            return $metadata;
        }

        $filesystem = $this->getFilesystemFromPointerOrFile($file);

        if ($filesystem instanceof MetadataAwareFilesystemReader) {
            $metadata = $filesystem->getMetadata($file->getKey());

            return new RawMetadata($metadata);
        }

        throw new FileNotFoundException(
            $file->getKey(),
            $file->getFilesystemIdentifier(),
        );
    }

    /**
     * @param iterable<string,int|string|bool|null> $metadata
     */
    private function setMetadata(
        FilePointerInterface|FileInterface $file,
        iterable $metadata,
    ): void {
        $filesystem = $this->getFilesystemFromPointerOrFile($file);

        if ($filesystem instanceof MetadataAwareFilesystemWriter) {
            $filesystem->setMetadata($file->getKey(), $metadata);
        }
    }

    #[\Override]
    public function delete(FilePointerInterface $filePointer): void
    {
        $this->getFilesystemFromPointerOrFile($filePointer)
            ->delete($filePointer->getKey());

        unset($this->fileCache[$this->getFilePointerHash($filePointer)]);
    }

    #[\Override]
    public function copy(
        FilePointerInterface|FileInterface $source,
        FilePointerInterface|FileInterface $destination,
    ): FileInterface {
        if ($source->isEqualTo($destination)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Source and destination file pointers are the same: %s',
                    $source->getKey(),
                ),
            );
        }

        if ($source->isSameFilesystem($destination)) {
            $this->getFilesystemFromPointerOrFile($source)
                ->copy(
                    $source->getKey(),
                    $destination->getKey(),
                );
        } else {
            if ($source instanceof FilePointerInterface) {
                $source = $this->get($source);
            }

            $sourceStream = $source->getContentAsStream()->detach();
            \assert(\is_resource($sourceStream));

            $this->getFilesystemFromPointerOrFile($destination)
                ->writeStream(
                    $destination->getKey(),
                    $sourceStream,
                );
        }

        $sourceMetadata = $this->getMetadata($source);

        // copy metadata
        $this->setMetadata($destination, $sourceMetadata);

        // get destination pointer
        $destination = $destination instanceof FileInterface ?
            $destination->getPointer() : $destination;

        // delete cache
        unset($this->fileCache[$this->getFilePointerHash($destination)]);

        return $this->get($destination);
    }

    #[\Override]
    public function move(
        FilePointerInterface|FileInterface $source,
        FilePointerInterface|FileInterface $destination,
    ): FileInterface {
        if ($source->isEqualTo($destination)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Source and destination file pointers are the same: %s',
                    $source->getKey(),
                ),
            );
        }

        $sourceMetadata = $this->getMetadata($source);

        // do the moving

        if ($source->isSameFilesystem($destination)) {
            $this->getFilesystemFromPointerOrFile($source)
                ->move(
                    $source->getKey(),
                    $destination->getKey(),
                );
        } else {
            if ($source instanceof FilePointerInterface) {
                $source = $this->get($source);
            }

            $sourceStream = $source->getContentAsStream()->detach();
            \assert(\is_resource($sourceStream));

            $this->getFilesystemFromPointerOrFile($destination)
                ->writeStream(
                    $destination->getKey(),
                    $sourceStream,
                );

            $this->getFilesystemFromPointerOrFile($source)
                ->delete($source->getKey());
        }

        // store the metadata
        $this->setMetadata($destination, $sourceMetadata);

        // delete cache
        $source = $source instanceof FileInterface ?
            $source->getPointer() : $source;
        unset($this->fileCache[$this->getFilePointerHash($source)]);

        // get destination pointer
        $destination = $destination instanceof FileInterface ?
            $destination->getPointer() : $destination;

        // delete cache
        unset($this->fileCache[$this->getFilePointerHash($destination)]);

        return $this->get($destination);
    }

    private function getFilePointerHash(
        FilePointerInterface $filePointer,
    ): string {
        return sha1(
            ($filePointer->getFilesystemIdentifier() ?? '') . $filePointer->getKey(),
        );
    }

    #[\Override]
    public function createTemporaryFile(
        ?string $prefix = null,
        ?string $filesystemId = null,
    ): FileInterface {
        $filesystemId ??= $this->defaultFilesystemIdForTemporaryFile;

        if ($filesystemId === null) {
            $tmpDir = sys_get_temp_dir();
            $tmpFile = tempnam($tmpDir, $prefix ?? 'rekalogika-file-');

            if ($tmpFile === false) {
                throw new \RuntimeException(
                    \sprintf(
                        'Failed to create temporary file in %s',
                        $tmpDir,
                    ),
                );
            }

            return TemporaryFile::createFromExisting($tmpFile);
        }

        $prefix ??= 'tmp/file-';

        $filesystem = $this->filesystemRepository
            ->getFilesystem($filesystemId);

        $key = $prefix . bin2hex(random_bytes(16));

        $filesystem->write($key, '');

        return TemporaryFile::createFromExisting(
            $key,
            $filesystem,
            $filesystemId,
        );
    }
}
