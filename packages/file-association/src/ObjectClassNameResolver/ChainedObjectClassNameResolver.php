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

namespace Rekalogika\File\Association\ObjectClassNameResolver;

use Rekalogika\File\Association\Contracts\ObjectClassNameResolverInterface;
use Rekalogika\File\Association\Exception\ObjectClassNameResolver\ChainedObjectClassNameResolverException;
use Rekalogika\File\Association\Exception\ObjectClassNameResolver\ObjectClassNameResolverException;

final class ChainedObjectClassNameResolver implements ObjectClassNameResolverInterface
{
    /**
     * @var \WeakMap<object,string>
     */
    private \WeakMap $cache;

    /**
     * @param iterable<ObjectClassNameResolverInterface> $objectClassNameResolvers
     */
    public function __construct(
        private readonly iterable $objectClassNameResolvers,
    ) {
        /** @var \WeakMap<object,string> */
        $map = new \WeakMap();

        $this->cache = $map;
    }

    #[\Override]
    public function getObjectClassName(object $object): string
    {
        if (isset($this->cache[$object])) {
            /** @var string */
            return $this->cache[$object];
        }

        $exceptions = [];

        foreach ($this->objectClassNameResolvers as $objectClassNameResolver) {
            try {
                return $this->cache[$object]
                    = $objectClassNameResolver->getObjectClassName($object);
            } catch (ObjectClassNameResolverException $e) {
                $exceptions[] = $e;
            }
        }

        throw new ChainedObjectClassNameResolverException($object, $exceptions);
    }
}
