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

namespace Rekalogika\File\Association\ClassBasedFileLocationResolver;

use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\File\Association\Contracts\ClassBasedFileLocationResolverInterface;
use Rekalogika\File\Association\Exception\FileLocationResolver\ChainedClassNotSupportedException;
use Rekalogika\File\Association\Exception\FileLocationResolver\ObjectNotSupportedException;

final class ChainedClassBasedFileLocationResolver implements ClassBasedFileLocationResolverInterface
{
    /**
     * @param iterable<ClassBasedFileLocationResolverInterface> $classBasedFileLocationResolvers
     */
    public function __construct(
        private readonly iterable $classBasedFileLocationResolvers,
    ) {}

    #[\Override]
    public function getFileLocation(
        string $class,
        string $id,
        string $propertyName,
    ): FilePointerInterface {
        $exceptions = [];

        foreach ($this->classBasedFileLocationResolvers as $classBasedFileLocationResolver) {
            try {
                return $classBasedFileLocationResolver->getFileLocation(
                    class: $class,
                    id: $id,
                    propertyName: $propertyName,
                );
            } catch (ObjectNotSupportedException $e) {
                $exceptions[] = $e;
            }
        }

        throw new ChainedClassNotSupportedException($class, $exceptions);
    }
}
