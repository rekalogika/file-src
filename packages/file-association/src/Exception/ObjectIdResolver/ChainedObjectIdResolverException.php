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

namespace Rekalogika\File\Association\Exception\ObjectIdResolver;

class ChainedObjectIdResolverException extends ObjectIdResolverException
{
    /**
     * @param iterable<ObjectIdResolverException> $exceptions
     */
    public function __construct(
        object $object,
        private readonly iterable $exceptions,
        ?\Throwable $previous = null,
    ) {
        \Exception::__construct(
            \sprintf(
                'None of the object ID resolvers registered in the system supports the object "%s"',
                $object::class,
            ),
            0,
            $previous,
        );
    }

    /**
     * @return iterable<ObjectIdResolverException>
     */
    public function getExceptions(): iterable
    {
        return $this->exceptions;
    }
}
