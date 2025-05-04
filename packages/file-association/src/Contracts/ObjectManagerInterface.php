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

namespace Rekalogika\File\Association\Contracts;

/**
 * @internal
 */
interface ObjectManagerInterface
{
    /**
     * Called when the object is saved
     */
    public function flushObject(object $object): void;

    /**
     * Called when the object is removed
     */
    public function removeObject(object $object): void;

    /**
     * Called when the object is loaded
     */
    public function loadObject(object $object): void;
}
