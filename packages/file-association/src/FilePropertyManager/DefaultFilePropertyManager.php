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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Association\Contracts\ClassBasedFileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Contracts\FilePropertyOperation;
use Rekalogika\File\Association\Contracts\PropertyReaderInterface;
use Rekalogika\File\Association\Contracts\PropertyWriterInterface;
use Rekalogika\File\Association\Model\FetchMode;
use Rekalogika\File\Association\Model\MissingFile;
use Rekalogika\File\Association\Model\PropertyMetadata;

final class DefaultFilePropertyManager implements
    FilePropertyManagerInterface,
    LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly FileRepositoryInterface $fileRepository,
        private readonly PropertyReaderInterface $reader,
        private readonly PropertyWriterInterface $writer,
        private readonly ClassBasedFileLocationResolverInterface $fileLocationResolver,
    ) {}

    private function log(
        PropertyMetadata $propertyMetadata,
        string $id,
        FilePointerInterface $filePointer,
        FilePropertyOperation $operation,
    ): void {
        if ($operation === FilePropertyOperation::FlushNothing) {
            return;
        }

        $context = [
            'class' => $propertyMetadata->getClass(),
            'property' => $propertyMetadata->getName(),
            'scopeClass' => $propertyMetadata->getScopeClass(),
            'objectId' => $id,
            'fileKey' => $filePointer->getKey(),
            'fileFilesystemIdentifier' => $filePointer->getFilesystemIdentifier(),
        ];

        $this->logger?->debug(
            $operation->getDescription(),
            $context,
        );
    }

    /**
     * Process a potential incoming file upload on a property
     */
    #[\Override]
    public function flushProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): FilePropertyOperation {
        $propertyName = $propertyMetadata->getName();
        $class = $propertyMetadata->getClass();

        /** @psalm-suppress MixedAssignment */
        $currentFile = $this->reader->read($object, $propertyName);

        // determine the file location
        $filePointer = $this->fileLocationResolver->getFileLocation(
            class: $class,
            id: $id,
            propertyName: $propertyName,
        );

        if ($currentFile instanceof FileInterface) {
            // if the file location of the current file is the same as the
            // file location of the property, we don't need to do anything
            if ($currentFile->isEqualTo($filePointer)) {
                $this->log(
                    propertyMetadata: $propertyMetadata,
                    id: $id,
                    filePointer: $filePointer,
                    operation: FilePropertyOperation::FlushNothing,
                );

                return FilePropertyOperation::FlushNothing;
            }

            // copy file to storage
            $this->fileRepository->copy($currentFile, $filePointer);

            // get reference of the file from storage
            $file = $this->fileRepository->getReference($filePointer);

            // write the file to the object
            $this->writer->write($object, $propertyName, $file);

            // log
            $this->log(
                propertyMetadata: $propertyMetadata,
                id: $id,
                filePointer: $filePointer,
                operation: FilePropertyOperation::FlushSave,
            );

            return FilePropertyOperation::FlushSave;
        } elseif (null === $currentFile) {
            $this->fileRepository->delete($filePointer);

            $this->log(
                propertyMetadata: $propertyMetadata,
                id: $id,
                filePointer: $filePointer,
                operation: FilePropertyOperation::FlushRemove,
            );

            return FilePropertyOperation::FlushRemove;
        }

        throw new \InvalidArgumentException(
            \sprintf(
                'Property "%s" on object "%s" is not a %s instance',
                $propertyName,
                $object::class,
                FileInterface::class,
            ),
        );
    }

    /**
     * Process a file removal on an attribute
     */
    #[\Override]
    public function removeProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): FilePropertyOperation {
        $propertyName = $propertyMetadata->getName();
        $class = $propertyMetadata->getClass();

        $filePointer = $this->fileLocationResolver->getFileLocation(
            class: $class,
            id: $id,
            propertyName: $propertyName,
        );

        $this->fileRepository->delete($filePointer);

        $this->log(
            propertyMetadata: $propertyMetadata,
            id: $id,
            filePointer: $filePointer,
            operation: FilePropertyOperation::Remove,
        );

        return FilePropertyOperation::Remove;
    }

    /**
     * Process an attribute on an object loading
     */
    #[\Override]
    public function loadProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): FilePropertyOperation {
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

                    $result = FilePropertyOperation::LoadMissing;
                } else {
                    $result = FilePropertyOperation::LoadNull;
                }
            } else {
                $result = FilePropertyOperation::LoadNormal;
            }
        } else {
            $file = $this->fileRepository->getReference($filePointer);
            $result = FilePropertyOperation::LoadLazy;
        }

        $this->writer->write($object, $propertyName, $file);

        $this->log(
            propertyMetadata: $propertyMetadata,
            id: $id,
            filePointer: $filePointer,
            operation: $result,
        );

        return $result;
    }
}
