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

use League\Flysystem\FilesystemOperator;
use Rekalogika\Contracts\File\Exception\File\TemporaryFileException;

final class TemporaryFile extends File
{
    private function __construct(
        string $key,
        ?FilesystemOperator $filesystem = null,
        ?string $filesystemIdentifier = null,
    ) {
        parent::__construct($key, $filesystem, $filesystemIdentifier);
    }

    public function __destruct()
    {
        $this->getFilesystem()->delete($this->getKey());
    }

    /**
     * Creates a temporary file in the local filesystem
     */
    final public static function create(
        ?string $prefix = null,
    ): self {
        $prefix ??= 'temporaryfile-';
        $path = tempnam(sys_get_temp_dir(), $prefix);

        if ($path === false) {
            throw new TemporaryFileException($prefix);
        }

        $file = new self($path);
        $file->setName(null);

        return $file;
    }

    /**
     * Creates a local temporary file with the specified content
     */
    final public static function createFromString(
        string $content,
        ?string $prefix = null,
    ): self {
        $file = self::create($prefix);
        $file->setContent($content);

        return $file;
    }

    final public static function createFromExisting(
        string $key,
        ?FilesystemOperator $filesystem = null,
        ?string $filesystemIdentifier = null,
    ): self {
        $file = new self($key, $filesystem, $filesystemIdentifier);
        $file->setName(null);

        return $file;
    }
}
