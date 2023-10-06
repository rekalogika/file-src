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
use Rekalogika\Contracts\File\Exception\File\DerivationNotSupportedException;

/**
 * Represents a file in the system.
 */
interface FileInterface extends NodeInterface
{
    /**
     * Identifies the filesystem that the file is stored on. Null means that
     * the file is accessed directly on the local filesystem.
     */
    public function getFilesystemIdentifier(): ?string;

    /**
     * The key that identifies the object. Usually in the form of a file path.
     * It is the access key to the file and unique across the same filesystem.
     * It does not need to have a filename.
     */
    public function getKey(): string;

    /**
     * Gets the pointer to the file.
     */
    public function getPointer(): FilePointerInterface;

    /**
     * Determines if the file or pointer refers to the same file. Implementors
     * should use `EqualityTrait` instead of implementing manually.
     */
    public function isEqualTo(self|FilePointerInterface $other): bool;

    /**
     * Determines if the pointer is on the same filesystem as the other pointer.
     * Implementors should use `EqualityTrait` instead of implementing manually.
     */
    public function isSameFilesystem(self|FilePointerInterface $other): bool;

    /**
     * Gets the file name.
     */
    public function getName(): FileNameInterface;

    /**
     * Sets the file name.
     */
    public function setName(?string $fileName): void;

    /**
     * Replaces the file content with the given string.
     */
    public function setContent(string $contents): void;

    /**
     * Replaces the file content with the given stream.
     *
     * @param resource|StreamInterface $stream
     */
    public function setContentFromStream(mixed $stream): void;

    /**
     * Gets the content as a string.
     */
    public function getContent(): string;

    /**
     * Gets the content as a stream.
     */
    public function getContentAsStream(): StreamInterface;

    /**
     * Saves the file to the local filesystem
     */
    public function saveToLocalFile(string $path): \SplFileInfo;

    /**
     * Creates a temporary file in the local filesystem.
     */
    public function createLocalTemporaryFile(): \SplFileInfo;

    /**
     * Gets the media type (MIME type) of the file.
     */
    public function getType(): FileTypeInterface;

    /**
     * Sets the media type (MIME type) of the file.
     */
    public function setType(string $type): void;

    /**
     * Gets the size of the file in bytes.
     *
     * @return int<0,max>
     */
    public function getSize(): int;

    /**
     * Gets the last modified date of the file.
     */
    public function getLastModified(): \DateTimeInterface;

    /**
     * Gets the pointer to the derived file with the given derivation ID.
     *
     * @throws DerivationNotSupportedException
     */
    public function getDerivation(string $derivationId): FilePointerInterface;

    /**
     * Gets an associated object of the file.
     *
     * @template T of object
     * @param class-string<T>|string $id
     * @return ($id is class-string<T> ? T|null : mixed)
     */
    public function get(string $id);

    /**
     * Flushes metadata to the storage.
     *
     * @return void
     */
    public function flush(): void;
}
