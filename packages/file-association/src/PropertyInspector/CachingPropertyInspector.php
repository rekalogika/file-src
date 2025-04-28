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

use Psr\Cache\CacheItemPoolInterface;
use Rekalogika\File\Association\Contracts\PropertyInspectorInterface;
use Rekalogika\File\Association\Model\PropertyInspectorResult;

final readonly class CachingPropertyInspector implements PropertyInspectorInterface
{
    public function __construct(
        private PropertyInspectorInterface $propertyInspector,
        private CacheItemPoolInterface $cache,
    ) {}

    #[\Override]
    public function inspect(string $class, string $propertyName): PropertyInspectorResult
    {
        $cacheKey = \sprintf('%s::%s', $class, $propertyName);
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            /** @psalm-suppress MixedAssignment */
            $result = $cacheItem->get();

            if ($result instanceof PropertyInspectorResult) {
                return $result;
            }

            $this->cache->deleteItem($cacheKey);
        }

        $result = $this->propertyInspector->inspect($class, $propertyName);

        $cacheItem->set($result);
        $this->cache->save($cacheItem);

        return $result;
    }
}
