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

class MethodNotFoundException extends ObjectIdResolverException
{
    public function __construct(
        object $object,
        string $method,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            \sprintf(
                "Method '%s' not found in object '%s'",
                $method,
                $object::class,
            ),
            0,
            $previous,
        );
    }
}
