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
final class AttributesPropertyLister implements PropertyListerInterface
{
    /**
     * @var array<string,iterable<string>>
     */
    private array $cache = [];

    #[\Override]
    public function getFileProperties(object $object): iterable
    {
        if (isset($this->cache[$object::class])) {
            return $this->cache[$object::class];
        }

        $class = $object::class;
        $properties = [];

        foreach ($this->getReflectionPropertiesWithAttribute($class, AsFileAssociation::class) as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = 1;
        }

        return $this->cache[$class] = array_keys($properties);
    }

    /**
     * @param class-string $class
     * @return iterable<\ReflectionProperty>
     */
    private function getReflectionProperties(string $class): iterable
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
    private function getReflectionPropertiesWithAttribute(
        string $class,
        string $attribute,
    ): iterable {
        foreach ($this->getReflectionProperties($class) as $reflectionProperty) {
            $attributes = $reflectionProperty
                ->getAttributes($attribute);

            if (\count($attributes) === 1) {
                yield $reflectionProperty;
            }
        }
    }
}
