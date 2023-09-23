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

namespace Rekalogika\File\Association\ObjectIdResolver;

use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Exception\ObjectIdResolver\ChainedObjectIdResolverException;
use Rekalogika\File\Association\Exception\ObjectIdResolver\ObjectIdResolverException;

class ChainedObjectIdResolver implements ObjectIdResolverInterface
{
    /**
     * @param iterable<ObjectIdResolverInterface> $objectIdResolvers
     */
    public function __construct(
        private iterable $objectIdResolvers
    ) {
    }

    public function getObjectId(object $object): string
    {
        $exceptions = [];

        foreach ($this->objectIdResolvers as $objectIdResolver) {
            try {
                return $objectIdResolver->getObjectId($object);
            } catch (ObjectIdResolverException $e) {
                $exceptions[] = $e;
            }
        }

        throw new ChainedObjectIdResolverException($object, $exceptions);
    }
}
