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

namespace Rekalogika\File\Association\Util;

final readonly class ClassUtil
{
    private function __construct() {}

    /**
     * @param class-string $class
     * @return iterable<\ReflectionProperty>
     */
    public static function getReflectionProperties(string $class): iterable
    {
        $reflectionClass = (new \ReflectionClass($class));

        // process leaf class
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isStatic()) {
                continue;
            }

            yield $reflectionProperty;
        }

        $reflectionClass = $reflectionClass->getParentClass();

        // process parent classes
        while ($reflectionClass instanceof \ReflectionClass) {
            $privateProperties = $reflectionClass
                ->getProperties(\ReflectionProperty::IS_PRIVATE);

            foreach ($privateProperties as $reflectionProperty) {
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
    public static function getReflectionPropertiesWithAttribute(
        string $class,
        string $attribute,
    ): iterable {
        foreach (self::getReflectionProperties($class) as $reflectionProperty) {
            $attributes = $reflectionProperty->getAttributes($attribute);

            if (\count($attributes) === 1) {
                yield $reflectionProperty;
            }
        }
    }
}
