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

namespace Rekalogika\File\Association\ClassMetadataFactory;

use Psr\Cache\CacheItemPoolInterface;
use Rekalogika\File\Association\Contracts\ClassMetadataFactoryInterface;
use Rekalogika\File\Association\Model\ClassMetadata;

final readonly class CachingClassMetadataFactory implements ClassMetadataFactoryInterface
{
    public function __construct(
        private ClassMetadataFactoryInterface $classMetadataFactory,
        private CacheItemPoolInterface $cache,
    ) {}

    #[\Override]
    public function getClassMetadata(string $class): ClassMetadata
    {
        $cacheKey = hash('xxh128', $class);
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            /** @psalm-suppress MixedAssignment */
            $result = $cacheItem->get();

            if ($result instanceof ClassMetadata) {
                return $result;
            }

            $this->cache->deleteItem($cacheKey);
        }

        $result = $this->classMetadataFactory->getClassMetadata($class);

        $cacheItem->set($result);
        $this->cache->save($cacheItem);

        return $result;
    }
}
