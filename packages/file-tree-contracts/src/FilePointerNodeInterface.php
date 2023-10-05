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

namespace Rekalogika\Contracts\File\Tree;

use Rekalogika\Contracts\File\FilePointerInterface;

/**
 * Represents a file pointer in a tree
 */
interface FilePointerNodeInterface extends NodeInterface, FilePointerInterface
{
    /**
     * Gets the directory containing the file pointer
     */
    public function getDirectory(): DirectoryInterface;
}
