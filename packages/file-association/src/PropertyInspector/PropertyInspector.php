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

namespace Rekalogika\File\Association\PropertyInspector;

use Rekalogika\File\Association\Attribute\AsFileAssociation;
use Rekalogika\File\Association\Contracts\PropertyInspectorInterface;
use Rekalogika\File\Association\Exception\PropertyInspector\MissingPropertyException;
use Rekalogika\File\Association\Model\PropertyInspectorResult;

final class PropertyInspector implements PropertyInspectorInterface
{
    /**
     * @var array<string,PropertyInspectorResult>
     */
    private array $cache = [];

    #[\Override]
    public function inspect(object $object, string $propertyName): PropertyInspectorResult
    {
        $cacheKey = $object::class . '::' . $propertyName;

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperty = null;

        while ($reflectionClass instanceof \ReflectionClass) {
            if ($reflectionClass->hasProperty($propertyName)) {
                $reflectionProperty = $reflectionClass->getProperty($propertyName);
                break;
            }

            $reflectionClass = $reflectionClass->getParentClass();
        }

        if (!$reflectionProperty instanceof \ReflectionProperty) {
            throw new MissingPropertyException(
                propertyName: $propertyName,
                object: $object,
            );
        }

        $reflectionType = $reflectionProperty->getType();
        $mandatory = !($reflectionType?->allowsNull() ?? true);

        $attributes = $reflectionProperty
            ->getAttributes(AsFileAssociation::class);

        if ($attributes === []) {
            return $this->cache[$cacheKey] = new PropertyInspectorResult(
                mandatory: $mandatory,
                fetch: 'EAGER',
            );
        }

        $attribute = $attributes[0]->newInstance();

        return $this->cache[$cacheKey] = new PropertyInspectorResult(
            mandatory: $mandatory,
            fetch: $attribute->fetch,
        );
    }
}
