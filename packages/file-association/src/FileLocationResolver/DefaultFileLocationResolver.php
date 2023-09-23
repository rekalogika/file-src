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
use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Model\FilePointer;

class DefaultFileLocationResolver implements FileLocationResolverInterface
{
    public function __construct(
        private ObjectIdResolverInterface $objectIdResolver,
        private string $filesystemIdentifier = 'default',
        private string $prefix = 'entity',
        private int $hashLevel = 4,
    ) {
    }

    public function getFileLocation(
        object $object,
        string $propertyName
    ): FilePointerInterface {
        $id = $this->objectIdResolver->getObjectId($object);

        $splittedHash = str_split(sha1($id), 2);
        $hash = implode('/', array_slice($splittedHash, 0, $this->hashLevel));

        $classHash = sha1($object::class);

        $key = sprintf(
            '%s/%s/%s/%s/%s',
            $this->prefix,
            $classHash,
            $propertyName,
            $hash,
            $id,
        );

        $key = preg_replace('/\/+/', '/', $key);
        assert(is_string($key));

        return new FilePointer($this->filesystemIdentifier, $key);
    }
}
