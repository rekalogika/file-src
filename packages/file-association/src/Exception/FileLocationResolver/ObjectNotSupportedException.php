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

namespace Rekalogika\File\Association\Exception\FileLocationResolver;

use Rekalogika\File\Association\Contracts\FileLocationResolverInterface;

class ObjectNotSupportedException extends FileLocationResolverException
{
    /**
     * @param class-string<FileLocationResolverInterface> $class
     */
    public function __construct(
        string $class,
        object $object,
        string $message,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            \sprintf(
                'File location resolver "%s" does not support object "%s": %s',
                $class,
                $object::class,
                $message,
            ),
            0,
            $previous,
        );
    }
}
