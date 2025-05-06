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

namespace Rekalogika\File\Association\FilePropertyManager;

use Rekalogika\Contracts\File\FileProxy;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Association\Contracts\ClassBasedFileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Contracts\PropertyReaderInterface;
use Rekalogika\File\Association\Contracts\PropertyWriterInterface;
use Rekalogika\File\Association\Model\FetchMode;
use Rekalogika\File\Association\Model\MissingFile;
use Rekalogika\File\Association\Model\ObjectOperationType;
use Rekalogika\File\Association\Model\PropertyMetadata;
use Rekalogika\File\Association\Model\PropertyOperationAction;
use Rekalogika\File\Association\Model\PropertyOperationResult;
use Rekalogika\File\Association\PropertyReaderWriter\DefaultPropertyReaderWriter;
use Rekalogika\File\Association\PropertyRecorder\PropertyRecorder;
use Symfony\Contracts\Service\ResetInterface;

final readonly class DefaultFilePropertyManager implements
    FilePropertyManagerInterface,
    ResetInterface
{
    private PropertyReaderInterface $reader;
    private PropertyWriterInterface $writer;

    private PropertyRecorder $propertyRecorder;

    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private ClassBasedFileLocationResolverInterface $fileLocationResolver,
    ) {
        $this->reader = $this->writer = new DefaultPropertyReaderWriter();
        $this->propertyRecorder = new PropertyRecorder();
    }

    #[\Override]
    public function reset(): void
    {
        $this->propertyRecorder->reset();
    }

    /**
     * Process a potential incoming file upload on a property
     */
    #[\Override]
    public function flushProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult {
        $propertyName = $propertyMetadata->getName();
        $class = $propertyMetadata->getClass();

        // determine the file location
        $filePointer = $this->fileLocationResolver->getFileLocation(
            class: $class,
            id: $id,
            propertyName: $propertyName,
        );

        /** @psalm-suppress MixedAssignment */
        $initialProperty = $this->propertyRecorder->getInitialProperty(
            object: $object,
            propertyName: $propertyName,
        );

        /** @psalm-suppress MixedAssignment */
        $currentFile = $this->reader->read($object, $propertyMetadata);

        // if property is unchanged, we don't need to do anything

        if ($initialProperty === $currentFile) {
            return new PropertyOperationResult(
                type: ObjectOperationType::Flush,
                action: PropertyOperationAction::Nothing,
                class: $propertyMetadata->getClass(),
                scopeClass: $propertyMetadata->getScopeClass(),
                property: $propertyName,
                objectId: $id,
                filePointer: $filePointer,
            );
        }

        // if the file is null, we need to remove the file from storage, then
        // we are done

        if (null === $currentFile) {
            $this->fileRepository->delete($filePointer);

            // record the initial property

            $this->propertyRecorder->saveInitialProperty(
                object: $object,
                propertyName: $propertyName,
                value: null,
            );

            return new PropertyOperationResult(
                type: ObjectOperationType::Flush,
                action: PropertyOperationAction::Removed,
                class: $propertyMetadata->getClass(),
                scopeClass: $propertyMetadata->getScopeClass(),
                property: $propertyName,
                objectId: $id,
                filePointer: $filePointer,
            );
        }

        // if the file location of the current file is the same as the
        // file location of the property, we don't need to do anything.
        // possibly not necessary.

        if ($currentFile->isEqualTo($filePointer)) {
            return new PropertyOperationResult(
                type: ObjectOperationType::Flush,
                action: PropertyOperationAction::Nothing,
                class: $propertyMetadata->getClass(),
                scopeClass: $propertyMetadata->getScopeClass(),
                property: $propertyName,
                objectId: $id,
                filePointer: $filePointer,
            );
        }

        // copy file to storage
        $this->fileRepository->copy($currentFile, $filePointer);

        // get reference of the file from storage
        $file = $this->fileRepository->getReference($filePointer);

        // inject the reference to the object
        $this->writer->write($object, $propertyMetadata, $file);

        // record the initial property
        $this->propertyRecorder->saveInitialProperty(
            object: $object,
            propertyName: $propertyName,
            value: $file,
        );

        return new PropertyOperationResult(
            type: ObjectOperationType::Flush,
            action: PropertyOperationAction::Saved,
            class: $propertyMetadata->getClass(),
            scopeClass: $propertyMetadata->getScopeClass(),
            property: $propertyName,
            objectId: $id,
            filePointer: $filePointer,
        );
    }

    /**
     * Process a file removal on a property
     */
    #[\Override]
    public function removeProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult {
        $propertyName = $propertyMetadata->getName();
        $class = $propertyMetadata->getClass();

        $filePointer = $this->fileLocationResolver->getFileLocation(
            class: $class,
            id: $id,
            propertyName: $propertyName,
        );

        /** @psalm-suppress MixedAssignment */
        $initialProperty = $this->propertyRecorder->getInitialProperty(
            object: $object,
            propertyName: $propertyName,
        );

        // remove the initial property
        $this->propertyRecorder->removeInitialProperty(
            object: $object,
            propertyName: $propertyName,
        );

        // if the initial property is null, we don't need to do anything
        if ($initialProperty === null) {
            return new PropertyOperationResult(
                type: ObjectOperationType::Remove,
                action: PropertyOperationAction::Nothing,
                class: $propertyMetadata->getClass(),
                scopeClass: $propertyMetadata->getScopeClass(),
                property: $propertyName,
                objectId: $id,
                filePointer: $filePointer,
            );
        }

        $this->fileRepository->delete($filePointer);

        return new PropertyOperationResult(
            type: ObjectOperationType::Remove,
            action: PropertyOperationAction::Removed,
            class: $propertyMetadata->getClass(),
            scopeClass: $propertyMetadata->getScopeClass(),
            property: $propertyName,
            objectId: $id,
            filePointer: $filePointer,
        );
    }

    /**
     * Process an property on an object loading
     */
    #[\Override]
    public function loadProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult {
        $propertyName = $propertyMetadata->getName();
        $class = $propertyMetadata->getClass();

        $filePointer = $this->fileLocationResolver->getFileLocation(
            class: $class,
            id: $id,
            propertyName: $propertyName,
        );

        if ($propertyMetadata->getFetch() === FetchMode::Eager) {
            $file = $this->fileRepository->tryGet($filePointer);

            if ($file === null) {
                if ($propertyMetadata->isMandatory()) {
                    $file = new MissingFile(
                        $filePointer->getFilesystemIdentifier(),
                        $filePointer->getKey(),
                    );

                    $result = new PropertyOperationResult(
                        type: ObjectOperationType::Load,
                        action: PropertyOperationAction::LoadedMissing,
                        class: $propertyMetadata->getClass(),
                        scopeClass: $propertyMetadata->getScopeClass(),
                        property: $propertyName,
                        objectId: $id,
                        filePointer: $filePointer,
                    );
                } else {
                    $result = new PropertyOperationResult(
                        type: ObjectOperationType::Load,
                        action: PropertyOperationAction::LoadedNotFound,
                        class: $propertyMetadata->getClass(),
                        scopeClass: $propertyMetadata->getScopeClass(),
                        property: $propertyName,
                        objectId: $id,
                        filePointer: $filePointer,
                    );
                }
            } else {
                $result = new PropertyOperationResult(
                    type: ObjectOperationType::Load,
                    action: PropertyOperationAction::LoadedNormal,
                    class: $propertyMetadata->getClass(),
                    scopeClass: $propertyMetadata->getScopeClass(),
                    property: $propertyName,
                    objectId: $id,
                    filePointer: $filePointer,
                );
            }
        } else {
            $file = $this->fileRepository->getReference($filePointer);

            if ($file instanceof FileProxy) {
                $action = PropertyOperationAction::LoadedLazy;
            } else {
                $action = PropertyOperationAction::LoadedNormal;
            }

            $result = new PropertyOperationResult(
                type: ObjectOperationType::Load,
                action: $action,
                class: $propertyMetadata->getClass(),
                scopeClass: $propertyMetadata->getScopeClass(),
                property: $propertyName,
                objectId: $id,
                filePointer: $filePointer,
            );
        }

        $this->writer->write($object, $propertyMetadata, $file);

        $this->propertyRecorder->saveInitialProperty(
            object: $object,
            propertyName: $propertyName,
            value: $file,
        );

        return $result;
    }
}
