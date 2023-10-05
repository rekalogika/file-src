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

use Rekalogika\Contracts\File\FileInterface;

/**
 * Represents a file in a tree
 */
interface FileNodeInterface extends NodeInterface, FileInterface
{
    /**
     * Gets the directory containing the file
     */
    public function getDirectory(): DirectoryInterface;
}
