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

/**
 * Represents a file or directory in a tree
 */
interface NodeInterface
{
    /**
     * Gets the containing directory
     */
    public function getContainingDirectory(): ?DirectoryInterface;
}
