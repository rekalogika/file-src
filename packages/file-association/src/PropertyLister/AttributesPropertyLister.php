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

use Rekalogika\File\Association\Attribute\AsFileAssociation;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;

/**
 * Determines all the file association properties by looking at
 * AsFileAssociation attributes.
 *
 * @todo: expensive
 */
class AttributesPropertyLister implements PropertyListerInterface
{
    public function getFileProperties(object $object): iterable
    {
        foreach (self::getReflectionPropertiesWithAttribute($object, AsFileAssociation::class) as $reflectionProperty) {
            yield $reflectionProperty->getName();
        }
    }

    /**
     * @return iterable<\ReflectionProperty>
     */
    private static function getReflectionProperties(object $object): iterable
    {
        $reflectionClass = (new \ReflectionClass(get_class($object)));
        while ($reflectionClass instanceof \ReflectionClass) {
            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                if ($reflectionProperty->isStatic()) {
                    continue;
                }

                yield $reflectionProperty;
            }

            $reflectionClass = $reflectionClass->getParentClass();
        }
    }

    /**
     * @param class-string $attribute
     * @return iterable<\ReflectionProperty>
     */
    private static function getReflectionPropertiesWithAttribute(
        object $object,
        string $attribute
    ): iterable {
        foreach (self::getReflectionProperties($object) as $reflectionProperty) {
            $attributes = $reflectionProperty
                ->getAttributes($attribute);

            if (count($attributes) === 1) {
                yield $reflectionProperty;
            }
        }
    }
}
