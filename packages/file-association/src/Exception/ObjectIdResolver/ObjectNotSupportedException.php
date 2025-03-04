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

final class ObjectNotSupportedException extends ObjectIdResolverException
{
    public function __construct(
        object $object,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            \sprintf(
                'Object "%s" is not supported.',
                $object::class,
            ),
            0,
            $previous,
        );
    }
}
