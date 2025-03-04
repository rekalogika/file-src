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

namespace Rekalogika\File\Association\FileLocationResolver;

use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\File\Association\Contracts\FileLocationResolverInterface;
use Rekalogika\File\Association\Exception\FileLocationResolver\ChainedObjectNotSupportedException;
use Rekalogika\File\Association\Exception\FileLocationResolver\ObjectNotSupportedException;

final class ChainedFileLocationResolver implements FileLocationResolverInterface
{
    /**
     * @param iterable<FileLocationResolverInterface> $fileLocationResolvers
     */
    public function __construct(
        private readonly iterable $fileLocationResolvers,
    ) {}

    #[\Override]
    public function getFileLocation(
        object $object,
        string $propertyName,
    ): FilePointerInterface {
        $exceptions = [];

        foreach ($this->fileLocationResolvers as $fileLocationResolver) {
            try {
                return $fileLocationResolver->getFileLocation($object, $propertyName);
            } catch (ObjectNotSupportedException $e) {
                $exceptions[] = $e;
            }
        }

        throw new ChainedObjectNotSupportedException($object, $exceptions);
    }
}
