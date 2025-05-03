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

final class IdNotSupportedException extends \LogicException implements ObjectIdResolverException
{
    public function __construct(
        object $object,
        string $method,
        mixed $id,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            \sprintf(
                'Method "%s" of object "%s" returned an unsupported identifier "%s"',
                $method,
                $object::class,
                get_debug_type($id),
            ),
            0,
            $previous,
        );
    }
}
