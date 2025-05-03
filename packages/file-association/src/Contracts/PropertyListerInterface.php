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

use Rekalogika\File\Association\Model\Property;

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
     * @param class-string $class
     * @return iterable<Property> Property names of the object that are file association.
     *
     */
    public function getFileProperties(string $class): iterable;
}
