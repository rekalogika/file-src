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

namespace Rekalogika\File\Contracts;

use League\Flysystem\FilesystemOperator;
use Rekalogika\File\Exception\FilesystemRepository\FilesystemAlreadyExistsException;
use Rekalogika\File\Exception\FilesystemRepository\FilesystemNotFoundException;

/**
 * Repository for filesystems. Keeps all the filesystems registered in the
 * application. A filesystem repository has one filesystem by default: the
 * local filesystem identified by null.
 */
interface FilesystemRepositoryInterface
{
    /**
     * Gets a filesystem by its identifier. Null identifier means the local
     * filesystem.
     *
     * @throws FilesystemNotFoundException
     */
    public function getFilesystem(
        ?string $identifier,
    ): MetadataAwareFilesystemOperator;

    /**
     * Adds a filesystem to the repository.
     *
     * @throws FilesystemAlreadyExistsException
     */
    public function addFilesystem(
        string $identifier,
        FilesystemOperator $filesystem,
    ): void;
}
