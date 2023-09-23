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

interface MetadataAwareFilesystemReader extends FilesystemOperator
{
    /**
     * Gets the metadata of the file.
     *
     * @return iterable<string,string|int|bool|null>
     */
    public function getMetadata(string $location): iterable;
}
