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
 * Lists all the properties of an object that are considered file association.
 *
 * @internal
 */
interface PropertyListerInterface
{
    /**
     * Returns the property names of the object that are file association.
     *
     * @return iterable<string> Property names of the object that are file association.
     *
     */
    public function getFileProperties(object $object): iterable;
}
