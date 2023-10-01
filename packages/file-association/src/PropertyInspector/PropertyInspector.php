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
use Rekalogika\File\Association\Model\PropertyInspectorResult;

class PropertyInspector implements PropertyInspectorInterface
{
    /**
     * @var array<string,PropertyInspectorResult>
     */
    private array $cache = [];

    public function inspect(object $object, string $propertyName): PropertyInspectorResult
    {
        $cacheKey = get_class($object) . '::' . $propertyName;

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        $reflectionType = $reflectionProperty->getType();
        $mandatory = !($reflectionType?->allowsNull() ?? true);

        $attributes = $reflectionProperty
            ->getAttributes(AsFileAssociation::class);

        if (count($attributes) === 0) {
            return $this->cache[$cacheKey] = new PropertyInspectorResult(
                mandatory: $mandatory,
                fetch: 'EAGER',
            );
        }

        $attribute = $attributes[0]->newInstance();

        if (!$attribute instanceof AsFileAssociation) {
            throw new \LogicException('Attribute must be instance of ' . AsFileAssociation::class);
        }

        return $this->cache[$cacheKey] = new PropertyInspectorResult(
            mandatory: $mandatory,
            fetch: $attribute->fetch,
        );
    }
}
