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

namespace Rekalogika\Contracts\File;

use Psr\Http\Message\StreamInterface;
use Rekalogika\Contracts\File\Exception\File\FileNotFoundException;

/**
 * Repository for files
 */
interface FileRepositoryInterface
{
    /**
     * Gets a file from the filesystem. Throws an exception if the file does not
     * exist.
     *
     * @throws FileNotFoundException
     */
    public function get(FilePointerInterface $filePointer): FileInterface;

    /**
     * Get a file from the filesystem. Returns null if the file does not exist.
     */
    public function tryGet(FilePointerInterface $filePointer): ?FileInterface;

    /**
     * Gets a lazy-loading proxy to the file specified by the pointer.
     */
    public function getReference(FilePointerInterface $filePointer): FileInterface;

    /**
     * Clears the repository's cache
     */
    public function clear(): void;

    /**
     * Writes data to a file in the filesystem.
     *
     * @param iterable<string,string> $metadata
     */
    public function createFromString(
        FilePointerInterface $filePointer,
        string $contents,
        iterable $metadata = []
    ): FileInterface;

    /**
     * Writes data to a file in the filesystem from a stream.
     *
     * @param resource|StreamInterface $stream
     * @param iterable<string,string> $metadata
     */
    public function createFromStream(
        FilePointerInterface $filePointer,
        mixed $stream,
        iterable $metadata = []
    ): FileInterface;

    /**
     * @param iterable<string,string> $metadata
     */
    public function createFromLocalFile(
        FilePointerInterface $filePointer,
        string $localFilePath,
        iterable $metadata = []
    ): FileInterface;

    /**
     * Creates a temporary file using the specified file prefix in the
     * optionally specified filesystem. If filesystem is not specified, the
     * default filesystem is used.
     */
    public function createTemporaryFile(
        ?string $prefix = null,
        ?string $filesystemId = null,
    ): FileInterface;

    /**
     * Deletes a file
     */
    public function delete(FilePointerInterface $filePointer): void;

    /**
     * Copies a file
     */
    public function copy(
        FilePointerInterface|FileInterface $source,
        FilePointerInterface|FileInterface $destination
    ): FileInterface;

    /**
     * Moves a file
     */
    public function move(
        FilePointerInterface|FileInterface $source,
        FilePointerInterface|FileInterface $destination
    ): FileInterface;
}
