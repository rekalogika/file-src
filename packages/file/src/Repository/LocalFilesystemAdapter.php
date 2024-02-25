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

namespace Rekalogika\File\Repository;

use League\Flysystem\Local\LocalFilesystemAdapter as FlysystemLocalFilesystemAdapter;

class LocalFilesystemAdapter extends FlysystemLocalFilesystemAdapter
{
    protected function ensureDirectoryExists(string $dirname, int $visibility): void
    {
        if ($dirname === '/' || $dirname === '') {
            return;
        }
    }
}
