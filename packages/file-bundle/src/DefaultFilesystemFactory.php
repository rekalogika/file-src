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

namespace Rekalogika\File\Bundle;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;

/**
 * Provides a good default, ready-to-use filesystem post-installation without
 * any dependency except on Flysystem.
 */
final class DefaultFilesystemFactory
{
    private ?FilesystemOperator $filesystem = null;

    public function __construct(private readonly string $directory) {}

    public function getDefaultFilesystem(): FilesystemOperator
    {
        if ($this->filesystem !== null) {
            return $this->filesystem;
        }

        $adapter = new LocalFilesystemAdapter($this->directory);

        return $this->filesystem = new Filesystem($adapter);
    }
}
