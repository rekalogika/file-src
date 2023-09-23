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

namespace Rekalogika\File\Association\PropertyLister;

use Rekalogika\File\Association\Contracts\PropertyListerInterface;

/**
 * Chains multiple property listers
 */
class ChainPropertyLister implements PropertyListerInterface
{
    /**
     * @param iterable<PropertyListerInterface> $propertyListers
     */
    public function __construct(
        private iterable $propertyListers,
    ) {
    }

    public function getFileProperties(object $object): iterable
    {
        $properties = [];

        foreach ($this->propertyListers as $propertyLister) {
            $newProperties = $propertyLister->getFileProperties($object);
            $newProperties = is_array($newProperties) ? $newProperties : iterator_to_array($newProperties);
            $properties = array_merge($properties, $newProperties);
        }

        return $properties;
    }
}
