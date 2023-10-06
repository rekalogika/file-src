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

namespace Rekalogika\Contracts\File;

/**
 * Represents a directory, which is a collection of files, file pointers, and
 * other directories.
 *
 * @template TKey of array-key
 * @template T of NodeInterface
 * @extends \Traversable<TKey,T>
 */
interface DirectoryInterface extends
    NodeInterface,
    \Traversable,
    \Countable
{
    /**
     * The name for this set of files, will be used as the name of the
     * downloaded folder or archive file, etc.
     */
    public function getName(): FileNameInterface;
}
