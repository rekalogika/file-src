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
use Rekalogika\Contracts\File\FileNameInterface;

/**
 * Represents a directory in a tree
 *
 * @extends \Traversable<int,NodeInterface>
 */
interface DirectoryInterface extends
    NodeInterface,
    \Traversable,
    \Countable
{
    /**
     * Gets the parent directory
     */
    public function getDirectory(): ?DirectoryInterface;

    /**
     * Gets the directory name.
     */
    public function getName(): FileNameInterface;

    /**
     * Sets the directory name.
     */
    public function setName(?string $name): void;

    /**
     * Adds a file
     */
    public function addFile(FileInterface $file): void;

    /**
     * Removes a file
     */
    public function removeFile(FileNodeInterface $file): void;

    /**
     * Creates a directory
     */
    public function createDirectory(string $name): DirectoryInterface;

    /**
     * Removes a directory
     */
    public function removeDirectory(DirectoryInterface $directory): void;

    /**
     * Checks if a file or directory is in the directory
     */
    public function contains(NodeInterface $file): bool;
}
