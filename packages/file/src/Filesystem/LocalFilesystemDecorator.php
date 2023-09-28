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
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToRetrieveMetadata;
use Rekalogika\File\Contracts\MetadataAwareFilesystemOperator;
use Rekalogika\File\RawMetadata;
use Rekalogika\Domain\File\Metadata\Constants;

class LocalFilesystemDecorator implements MetadataAwareFilesystemOperator
{
    public function __construct(
        private FilesystemOperator $wrapped,
    ) {
    }

    private function getWrapped(): FilesystemOperator
    {
        return $this->wrapped;
    }

    //
    // implementations
    //

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function getImageSize(string $location): array
    {
        $stream = $this->readStream($location);

        $temporaryFile = tempnam(
            sys_get_temp_dir(),
            'metadata_aware_filesystem_operator_decorator_'
        );

        if ($temporaryFile === false) {
            return [null, null];
        }

        $temporaryStream = fopen($temporaryFile, 'w+b');

        if ($temporaryStream === false) {
            return [null, null];
        }

        stream_copy_to_stream($stream, $temporaryStream);
        $result = @getimagesize($location);
        unlink($temporaryFile);

        if ($result === false) {
            return [null, null];
        }

        return [
            $result[0],
            $result[1],
        ];
    }

    public function getMetadata(string $location): RawMetadata
    {
        $metadata = new RawMetadata();
        $metadata->set(Constants::FILE_NAME, pathinfo($location, PATHINFO_BASENAME));
        $metadata->set(Constants::FILE_SIZE, $this->fileSize($location));
        $metadata->set(Constants::FILE_MODIFICATION_TIME, $this->lastModified($location));
        $metadata->set(Constants::FILE_TYPE, $this->mimeType($location));

        $imagesize = $this->getImageSize($location);

        if ($imagesize[0] !== null) {
            $metadata->set(Constants::MEDIA_WIDTH, $imagesize[0]);
        }

        if ($imagesize[1] !== null) {
            $metadata->set(Constants::MEDIA_HEIGHT, $imagesize[1]);
        }

        return $metadata;
    }

    public function setMetadata(string $location, iterable $metadata): void
    {
        // noop
    }

    //
    // forwarders
    //

    public function mimeType(string $path): string
    {
        try {
            return $this->getWrapped()->mimeType($path);
        } catch (UnableToRetrieveMetadata) {
            return 'application/octet-stream';
        }
    }

    //
    // noop forwarders
    //

    /**
     * @param array<array-key,mixed> $config
     */
    public function write(string $location, string $contents, array $config = []): void
    {
        $this->getWrapped()->write($location, $contents, $config);
    }

    /**
     * @param array<array-key,mixed> $config
     */
    public function writeStream(string $location, mixed $contents, array $config = []): void
    {
        $this->getWrapped()->writeStream($location, $contents, $config);
    }

    public function delete(string $location): void
    {
        $this->getWrapped()->delete($location);
    }

    public function deleteDirectory(string $location): void
    {
        $this->getWrapped()->deleteDirectory($location);
    }

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
        $config = []
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

    public function fileExists(string $location): bool
    {
        return $this->getWrapped()->fileExists($location);
    }

    public function directoryExists(string $location): bool
    {
        return $this->getWrapped()->directoryExists($location);
    }

    public function has(string $location): bool
    {
        return $this->getWrapped()->has($location);
    }

    public function read(string $location): string
    {
        return $this->getWrapped()->read($location);
    }

    public function readStream(string $location)
    {
        return $this->getWrapped()->readStream($location);
    }

    public function listContents(string $location, bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        return $this->getWrapped()->listContents($location, $deep);
    }

    public function lastModified(string $path): int
    {
        return $this->getWrapped()->lastModified($path);
    }

    public function fileSize(string $path): int
    {
        return $this->getWrapped()->fileSize($path);
    }

    public function visibility(string $path): string
    {
        return $this->getWrapped()->visibility($path);
    }

    public function setVisibility(string $path, string $visibility): void
    {
        $this->getWrapped()->setVisibility($path, $visibility);
    }

    /**
     * @param array<array-key,mixed> $config
     */
    public function createDirectory(string $location, array $config = []): void
    {
        $this->getWrapped()->createDirectory($location, $config);
    }

    /**
     * @param array<array-key,mixed> $config
     */
    public function move(string $source, string $destination, array $config = []): void
    {
        $this->getWrapped()->move($source, $destination, $config);
    }

    /**
     * @param array<array-key,mixed> $config
     */
    public function copy(string $source, string $destination, array $config = []): void
    {
        $this->getWrapped()->copy($source, $destination, $config);
    }
}
