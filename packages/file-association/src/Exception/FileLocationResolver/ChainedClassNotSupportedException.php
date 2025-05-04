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

final class ChainedClassNotSupportedException extends \RuntimeException implements FileLocationResolverException
{
    /**
     * @param class-string $class
     * @param iterable<FileLocationResolverException> $exceptions
     */
    public function __construct(
        string $class,
        private readonly iterable $exceptions,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            \sprintf(
                'Cannot find a resolver that supports class "%s"',
                $class,
            ),
            0,
            $previous,
        );
    }

    /**
     * @return iterable<FileLocationResolverException>
     */
    public function getExceptions(): iterable
    {
        return $this->exceptions;
    }
}
