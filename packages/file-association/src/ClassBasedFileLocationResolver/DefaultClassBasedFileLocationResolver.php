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
use Rekalogika\File\Association\Contracts\ClassSignatureResolverInterface;
use Rekalogika\File\Association\Model\FilePointer;
use Rekalogika\File\Association\Util\FileLocationUtil;

final readonly class DefaultClassBasedFileLocationResolver implements ClassBasedFileLocationResolverInterface
{
    public function __construct(
        private ClassSignatureResolverInterface $classSignatureResolver,
        private string $filesystemIdentifier = 'default',
        private string $prefix = 'entity',
        private int $hashLevel = 4,
    ) {}

    #[\Override]
    public function getFileLocation(
        string $class,
        string $id,
        string $propertyName,
    ): FilePointerInterface {
        $hash = FileLocationUtil::createHashedDirectory($id, $this->hashLevel);

        $classSignature = $this->classSignatureResolver
            ->getClassSignature($class)
            ?? throw new \LogicException(\sprintf(
                'Class signature resolver not found for class "%s"',
                $class,
            ));

        $key = \sprintf(
            '%s/%s/%s/%s/%s',
            $this->prefix,
            $classSignature,
            $propertyName,
            $hash,
            $id,
        );

        return new FilePointer($this->filesystemIdentifier, $key);
    }
}
