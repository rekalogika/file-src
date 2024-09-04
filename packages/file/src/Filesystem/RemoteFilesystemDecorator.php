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

namespace Rekalogika\File\Filesystem;

use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Metadata\Constants;
use Rekalogika\File\Contracts\MetadataAwareFilesystemOperator;
use Rekalogika\File\MetadataGenerator\MetadataGeneratorInterface;
use Rekalogika\File\MetadataSerializer\MetadataSerializerInterface;
use Rekalogika\File\RawMetadata;

/**
 * A decorator for Flysystem Operators that stores and caches metadata in a
 * sidecar file.
 */
class RemoteFilesystemDecorator implements MetadataAwareFilesystemOperator
{
    private ?FilesystemOperator $wrapped = null;

    public function __construct(
        private readonly MetadataSerializerInterface $serializer,
        private readonly MetadataGeneratorInterface $metadataGenerator,
        private readonly string $suffix = '.metadata',
    ) {}

    public function withFilesystem(FilesystemOperator $filesystem): self
    {
        $clone = clone $this;
        $clone->wrapped = $filesystem;

        return $clone;
    }

    private function getWrapped(): FilesystemOperator
    {
        if ($this->wrapped === null) {
            throw new \LogicException('No wrapped filesystem. Call withFilesystem() first.');
        }

        return $this->wrapped;
    }

    //
    // metadata
    //

    private function getMetadataFromSidecarFile(
        string $location,
    ): ?RawMetadata {
        try {
            $serialized = $this->getWrapped()
                ->read($this->getMetadataKey($location));

            $metadata = $this->serializer->deserialize($serialized);

            if (!$metadata instanceof RawMetadata) {
                $metadata = new RawMetadata();
            }

            return $metadata;
        } catch (FilesystemException | UnableToReadFile) {
            return null;
        }
    }

    private function getMetadataKey(string $location): string
    {
        return $location . $this->suffix;
    }

    private function generateMetadataFromStoredData(
        RawMetadataInterface $rawMetadata,
        string $location,
    ): void {
        $input = $this->getWrapped()->readStream($location);

        $this->metadataGenerator
            ->generateMetadataFromStream($rawMetadata, $input);
    }

    //
    // overrides
    //

    #[\Override]
    public function getMetadata(string $location): iterable
    {
        return $this->getOrInitMetadata($location);
    }

    /**
     * @return iterable<string,string|int|bool|null> $metadata
     */
    private function getOrInitMetadata(string $location): iterable
    {
        // first try to get metadata from the sidecar file
        $metadata = $this->getMetadataFromSidecarFile($location);

        // if metadata is present in the sidecar file, return it
        if ($metadata !== null) {
            return $metadata;
        }

        // if metadata is not present in the sidecar file, we need to read it
        // from the wrapped filesystem and store it in the sidecar file for
        // future use

        $wrapped = $this->getWrapped();

        if ($wrapped instanceof MetadataAwareFilesystemOperator) {
            $metadata = $wrapped->getMetadata($location);
        } else {
            $metadata = [];
        }

        try {
            $metadata = [
                ...$metadata,
                Constants::FILE_SIZE => $this->fileSize($location),
            ];
        } catch (UnableToRetrieveMetadata) {
        }

        $this->saveMetadata($location, $metadata);

        return $metadata;
    }

    #[\Override]
    public function setMetadata(string $location, iterable $metadata): void
    {
        $savedMetadata = $this->getOrInitMetadata($location);
        $savedMetadata = new RawMetadata($savedMetadata);

        // merge from supplied metadata
        $savedMetadata->merge($metadata);

        // save
        $this->saveMetadata($location, $savedMetadata);
    }

    /**
     * @param iterable<string,string|int|bool|null> $metadata
     */
    private function saveMetadata(
        string $location,
        iterable $metadata,
    ): void {
        if (!$metadata instanceof RawMetadata) {
            $metadata = new RawMetadata($metadata);
        }

        $serialized = $this->serializer->serialize($metadata);

        $this->getWrapped()->write(
            $this->getMetadataKey($location),
            $serialized,
        );
    }

    /**
     * @param array<array-key,mixed> $config
     */
    #[\Override]
    public function write(
        string $location,
        string $contents,
        array $config = [],
    ): void {
        $rawMetadata = $this->getMetadataFromSidecarFile($location)
            ?? new RawMetadata();

        /** @var array<string,string|int|bool|null> */
        $additionalMetadata = $config['metadata'] ?? [];
        $rawMetadata->merge($additionalMetadata);

        $this->metadataGenerator
            ->generateMetadataFromString($rawMetadata, $contents);

        $rawMetadata->set(Constants::FILE_MODIFICATION_TIME, time());

        $this->getWrapped()->write($location, $contents);
        $this->saveMetadata($location, $rawMetadata);
    }

    /**
     * @param array<array-key,mixed> $config
     */
    #[\Override]
    public function writeStream(
        string $location,
        mixed $contents,
        array $config = [],
    ): void {
        \assert(\is_resource($contents));

        $rawMetadata = $this->getMetadataFromSidecarFile($location)
            ?? new RawMetadata();

        /** @var array<string,string|int|bool|null> */
        $additionalMetadata = $config['metadata'] ?? [];
        $rawMetadata->merge($additionalMetadata);

        /** @var bool */
        $bypassMetadataGeneration = $config['bypass_metadata_generation'] ?? false;

        if ($this->isStreamSeekable($contents)) {
            if (!$bypassMetadataGeneration) {
                $this->metadataGenerator
                    ->generateMetadataFromStream($rawMetadata, $contents);
            }

            fseek($contents, 0);
            $this->getWrapped()->writeStream($location, $contents, $config);
        } else {
            $this->getWrapped()->writeStream($location, $contents);
            if (!$bypassMetadataGeneration) {
                $this->generateMetadataFromStoredData($rawMetadata, $location);
            }
        }

        $pos = ftell($contents);
        if ($pos !== false) {
            $rawMetadata->set(Constants::FILE_SIZE, $pos);
        } else {
            $rawMetadata->set(Constants::FILE_SIZE, $this->fileSize($location));
        }

        $rawMetadata->set(Constants::FILE_MODIFICATION_TIME, time());

        $this->saveMetadata($location, $rawMetadata);
    }

    #[\Override]
    public function delete(string $location): void
    {
        try {
            $this->getWrapped()->delete($location);
        } catch (FilesystemException | UnableToDeleteFile) {
        }

        try {
            $this->getWrapped()->delete($this->getMetadataKey($location));
        } catch (FilesystemException | UnableToDeleteFile) {
        }

        try {
            $this->getWrapped()->deleteDirectory($location . '.d');
        } catch (FilesystemException | UnableToDeleteDirectory) {
        }
    }

    #[\Override]
    public function deleteDirectory(string $location): void
    {
        $this->getWrapped()->deleteDirectory($location);
    }

    //
    // helpers
    //

    /**
     * @param resource $stream
     */
    private function isStreamSeekable(mixed $stream): bool
    {
        $meta = stream_get_meta_data($stream);

        return $meta['seekable'];
    }

    //
    // noop forwarders
    //

    /**
     * @param array<array-key,mixed> $config
     */
    public function publicUrl(string $path, $config = []): string
    {
        return $this->getWrapped()->publicUrl($path, $config);
    }

    /**
     * @param array<array-key,mixed> $config
     */
    public function temporaryUrl(
        string $path,
        \DateTimeInterface $expiresAt,
        $config = [],
    ): string {
        return $this->getWrapped()->temporaryUrl($path, $expiresAt, $config);
    }

    /**
     * @param string $path
     * @param array<array-key,mixed> $config
     */
    public function checksum($path, $config = []): string
    {
        return $this->getWrapped()->checksum($path, $config);
    }

    #[\Override]
    public function fileExists(string $location): bool
    {
        return $this->getWrapped()->fileExists($location);
    }

    #[\Override]
    public function directoryExists(string $location): bool
    {
        return $this->getWrapped()->directoryExists($location);
    }

    #[\Override]
    public function has(string $location): bool
    {
        return $this->getWrapped()->has($location);
    }

    #[\Override]
    public function read(string $location): string
    {
        return $this->getWrapped()->read($location);
    }

    #[\Override]
    public function readStream(string $location)
    {
        return $this->getWrapped()->readStream($location);
    }

    #[\Override]
    public function listContents(string $location, bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        return $this->getWrapped()->listContents($location, $deep);
    }

    #[\Override]
    public function lastModified(string $path): int
    {
        return $this->getWrapped()->lastModified($path);
    }

    #[\Override]
    public function fileSize(string $path): int
    {
        return $this->getWrapped()->fileSize($path);
    }

    #[\Override]
    public function mimeType(string $path): string
    {
        return $this->getWrapped()->mimeType($path);
    }

    #[\Override]
    public function visibility(string $path): string
    {
        return $this->getWrapped()->visibility($path);
    }

    #[\Override]
    public function setVisibility(string $path, string $visibility): void
    {
        $this->getWrapped()->setVisibility($path, $visibility);
    }

    /**
     * @param array<array-key,mixed> $config
     */
    #[\Override]
    public function createDirectory(string $location, array $config = []): void
    {
        $this->getWrapped()->createDirectory($location, $config);
    }

    /**
     * @param array<array-key,mixed> $config
     */
    #[\Override]
    public function move(string $source, string $destination, array $config = []): void
    {
        $this->getWrapped()->move($source, $destination, $config);
    }

    /**
     * @param array<array-key,mixed> $config
     */
    #[\Override]
    public function copy(string $source, string $destination, array $config = []): void
    {
        $this->getWrapped()->copy($source, $destination, $config);
    }
}
