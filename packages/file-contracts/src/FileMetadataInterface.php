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
 * Represent metadata that is applicable to all files.
 */
interface FileMetadataInterface
{
    /**
     * Gets the file name.
     */
    public function getName(): FileNameInterface;

    /**
     * Sets the file name.
     */
    public function setName(?string $fileName): void;

    /**
     * Gets the file size.
     *
     * @return int<0,max>
     */
    public function getSize(): int;

    /**
     * Gets the file media type (MIME type).
     */
    public function getType(): FileTypeInterface;

    /**
     * Sets the file media type (MIME type).
     */
    public function setType(string $type): void;

    /**
     * Gets the file modification time.
     */
    public function getModificationTime(): \DateTimeInterface;
}
