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

namespace Rekalogika\File\Association;

use Rekalogika\File\Association\Contracts\ClassMetadataFactoryInterface;
use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Util\ProxyUtil;

final readonly class FileAssociationManager
{
    public function __construct(
        private ClassMetadataFactoryInterface $classMetadataFactory,
        private ObjectIdResolverInterface $objectIdResolver,
        private FilePropertyManagerInterface $filePropertyManager,
    ) {}

    /**
     * @return class-string
     */
    private function getClass(object $object): string
    {
        return ProxyUtil::normalizeClassName($object::class);
    }

    /**
     * Called when the object is saved
     */
    public function save(object $object): void
    {
        $class = $this->getClass($object);
        $classMetadata = $this->classMetadataFactory->getClassMetadata($class);
        $id = $this->objectIdResolver->getObjectId($object);

        foreach ($classMetadata->getProperties() as $propertyMetadata) {
            $this->filePropertyManager->flushProperty(
                propertyMetadata: $propertyMetadata,
                object: $object,
                id: $id,
            );
        }
    }

    /**
     * Called when the object is removed
     */
    public function remove(object $object): void
    {
        $class = $this->getClass($object);
        $classMetadata = $this->classMetadataFactory->getClassMetadata($class);
        $id = $this->objectIdResolver->getObjectId($object);

        foreach ($classMetadata->getProperties() as $propertyMetadata) {
            $this->filePropertyManager->removeProperty(
                propertyMetadata: $propertyMetadata,
                object: $object,
                id: $id,
            );
        }
    }

    /**
     * Called after the object is loaded
     */
    public function load(object $object): void
    {
        $class = $this->getClass($object);
        $classMetadata = $this->classMetadataFactory->getClassMetadata($class);
        $id = $this->objectIdResolver->getObjectId($object);

        foreach ($classMetadata->getProperties() as $propertyMetadata) {
            $this->filePropertyManager->loadProperty(
                propertyMetadata: $propertyMetadata,
                object: $object,
                id: $id,
            );
        }
    }
}
