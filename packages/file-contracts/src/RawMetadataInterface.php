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

use Rekalogika\Contracts\File\Exception\MetadataNotFoundException;

/**
 * Represent the low-level metadata structure of a file.
 *
 * @extends \Traversable<string,int|string|bool|null>
 */
interface RawMetadataInterface extends \Traversable, \Countable
{
    /**
     * Gets a specific metadata.
     *
     * @throws MetadataNotFoundException
     */
    public function get(string $key): int|string|bool|null;

    /**
     * Gets a specific metadata. Returns null if the metadata is not found.
     */
    public function tryGet(string $key): int|string|bool|null;

    /**
     * Sets the specified metdata
     */
    public function set(string $key, int|string|bool|null $value): void;

    /**
     * Deletes the specified metadata
     */
    public function delete(string $key): void;

    /**
     * Merges the given list of metadata into the current one.
     *
     * @param iterable<string,string|int|bool|null> $metadata
     */
    public function merge(iterable $metadata): void;
}
