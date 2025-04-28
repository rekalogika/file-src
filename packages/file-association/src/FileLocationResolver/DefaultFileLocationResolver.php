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
use Rekalogika\File\Association\Contracts\ClassBasedFileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\FileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Util\ProxyUtil;

final readonly class DefaultFileLocationResolver implements FileLocationResolverInterface
{
    public function __construct(
        private ObjectIdResolverInterface $objectIdResolver,
        private ClassBasedFileLocationResolverInterface $classBasedFileLocationResolver,
    ) {}

    #[\Override]
    public function getFileLocation(
        object $object,
        string $propertyName,
    ): FilePointerInterface {
        $id = $this->objectIdResolver->getObjectId($object);
        $class = ProxyUtil::normalizeClassName($object::class);

        return $this->classBasedFileLocationResolver->getFileLocation(
            class: $class,
            id: $id,
            propertyName: $propertyName,
        );
    }
}
