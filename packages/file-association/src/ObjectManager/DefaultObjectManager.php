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

namespace Rekalogika\File\Association\ObjectManager;

use Rekalogika\File\Association\Contracts\ClassMetadataFactoryInterface;
use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Contracts\ObjectManagerInterface;
use Rekalogika\File\Association\Model\ObjectOperationResult;
use Rekalogika\File\Association\Model\ObjectOperationType;
use Rekalogika\File\Association\Util\ProxyUtil;

final readonly class DefaultObjectManager implements ObjectManagerInterface
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
    #[\Override]
    public function flushObject(object $object): ObjectOperationResult
    {
        $class = $this->getClass($object);
        $classMetadata = $this->classMetadataFactory->getClassMetadata($class);
        $id = $this->objectIdResolver->getObjectId($object);
        $propertyResults = [];

        foreach ($classMetadata->getProperties() as $propertyMetadata) {
            $propertyResults[] = $this->filePropertyManager->flushProperty(
                propertyMetadata: $propertyMetadata,
                object: $object,
                id: $id,
            );
        }

        return new ObjectOperationResult(
            type: ObjectOperationType::Flush,
            class: $class,
            objectId: $id,
            propertyResults: $propertyResults,
        );
    }

    /**
     * Called when the object is removed
     */
    #[\Override]
    public function removeObject(object $object): ObjectOperationResult
    {
        $class = $this->getClass($object);
        $classMetadata = $this->classMetadataFactory->getClassMetadata($class);
        $id = $this->objectIdResolver->getObjectId($object);
        $propertyResults = [];

        foreach ($classMetadata->getProperties() as $propertyMetadata) {
            $propertyResults[] = $this->filePropertyManager->removeProperty(
                propertyMetadata: $propertyMetadata,
                object: $object,
                id: $id,
            );
        }

        return new ObjectOperationResult(
            type: ObjectOperationType::Remove,
            class: $class,
            objectId: $id,
            propertyResults: $propertyResults,
        );
    }

    /**
     * Called after the object is loaded
     */
    #[\Override]
    public function loadObject(object $object): ObjectOperationResult
    {
        $class = $this->getClass($object);
        $classMetadata = $this->classMetadataFactory->getClassMetadata($class);
        $id = $this->objectIdResolver->getObjectId($object);
        $propertyResults = [];

        foreach ($classMetadata->getProperties() as $propertyMetadata) {
            $propertyResults[] = $this->filePropertyManager->loadProperty(
                propertyMetadata: $propertyMetadata,
                object: $object,
                id: $id,
            );
        }

        return new ObjectOperationResult(
            type: ObjectOperationType::Load,
            class: $class,
            objectId: $id,
            propertyResults: $propertyResults,
        );
    }
}
