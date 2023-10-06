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

/**
 * Pointer to a file
 */
interface FilePointerInterface extends NodeInterface
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
     * Determines if the pointer is equal to another pointer.
     */
    public function isEqualTo(self|FileInterface $other): bool;

    /**
     * Determines if the pointer is on the same filesystem as the other pointer.
     */
    public function isSameFilesystem(self|FileInterface $other): bool;
}
