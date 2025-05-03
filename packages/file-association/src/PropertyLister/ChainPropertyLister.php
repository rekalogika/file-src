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
final readonly class ChainPropertyLister implements PropertyListerInterface
{
    /**
     * @param iterable<PropertyListerInterface> $propertyListers
     */
    public function __construct(
        private iterable $propertyListers,
    ) {}

    #[\Override]
    public function getFileProperties(string $class): iterable
    {
        $properties = [];

        foreach ($this->propertyListers as $propertyLister) {
            $newProperties = $propertyLister->getFileProperties($class);

            $newProperties = \is_array($newProperties)
                ? $newProperties
                : iterator_to_array($newProperties);

            foreach ($newProperties as $property) {
                $properties[$property->getSignature()] = $property;
            }
        }

        yield from $properties;
    }
}
