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
 */
class AttributesPropertyLister implements PropertyListerInterface
{
    /**
     * @var array<string,iterable<string>>
     */
    private array $cache = [];

    public function getFileProperties(object $object): iterable
    {
        if (isset($this->cache[get_class($object)])) {
            return $this->cache[get_class($object)];
        }

        $class = get_class($object);
        $properties = [];

        foreach (self::getReflectionPropertiesWithAttribute($class, AsFileAssociation::class) as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = 1;
        }

        return $this->cache[$class] = array_keys($properties);
    }

    /**
     * @param class-string $class
     * @return iterable<\ReflectionProperty>
     */
    private static function getReflectionProperties(string $class): iterable
    {
        $reflectionClass = (new \ReflectionClass($class));
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
     * @param class-string $class
     * @param class-string $attribute
     * @return iterable<\ReflectionProperty>
     */
    private static function getReflectionPropertiesWithAttribute(
        string $class,
        string $attribute
    ): iterable {
        foreach (self::getReflectionProperties($class) as $reflectionProperty) {
            $attributes = $reflectionProperty
                ->getAttributes($attribute);

            if (count($attributes) === 1) {
                yield $reflectionProperty;
            }
        }
    }
}
