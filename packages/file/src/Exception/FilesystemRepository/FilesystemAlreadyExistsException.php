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

namespace Rekalogika\File\Exception\FilesystemRepository;

class FilesystemAlreadyExistsException extends FilesystemRepositoryException
{
    public function __construct(string $filesystemId)
    {
        parent::__construct(\sprintf('Filesystem with identifier "%s" is already exists', $filesystemId));
    }
}
