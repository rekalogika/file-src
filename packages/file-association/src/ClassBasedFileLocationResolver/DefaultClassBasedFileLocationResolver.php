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
use Rekalogika\File\Association\Contracts\ClassMetadataFactoryInterface;
use Rekalogika\File\Association\Model\FilePointer;

final readonly class DefaultClassBasedFileLocationResolver implements ClassBasedFileLocationResolverInterface
{
    public function __construct(
        private ClassMetadataFactoryInterface $classMetadataFactory,
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
        $splittedHash = str_split(sha1($id), 2);
        $hash = implode('/', \array_slice($splittedHash, 0, $this->hashLevel));

        $classSignature = $this->classMetadataFactory
            ->getClassMetadata($class)
            ->getSignature();

        $key = \sprintf(
            '%s/%s/%s/%s/%s',
            $this->prefix,
            $classSignature,
            $propertyName,
            $hash,
            $id,
        );

        $key = preg_replace('/\/+/', '/', $key);
        \assert(\is_string($key));

        return new FilePointer($this->filesystemIdentifier, $key);
    }
}
