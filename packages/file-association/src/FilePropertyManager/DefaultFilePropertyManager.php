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

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Association\Contracts\ClassBasedFileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\ClassMetadataFactoryInterface;
use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Contracts\PropertyReaderInterface;
use Rekalogika\File\Association\Contracts\PropertyWriterInterface;
use Rekalogika\File\Association\Model\FetchMode;
use Rekalogika\File\Association\Model\MissingFile;

final readonly class DefaultFilePropertyManager implements FilePropertyManagerInterface
{
    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private PropertyReaderInterface $reader,
        private PropertyWriterInterface $writer,
        private ClassMetadataFactoryInterface $classMetadataFactory,
        private ClassBasedFileLocationResolverInterface $fileLocationResolver,
    ) {}

    /**
     * Process a potential incoming file upload on a property
     *
     * @param class-string $class
     */
    #[\Override]
    public function saveProperty(
        object $object,
        string $class,
        string $id,
        string $propertyName,
    ): void {
        $currentFile = $this->reader->read($object, $propertyName);
        \assert($currentFile instanceof FileInterface || null === $currentFile);

        if ($currentFile instanceof FileInterface) {
            $filePointer = $this->fileLocationResolver->getFileLocation(
                class: $class,
                id: $id,
                propertyName: $propertyName,
            );

            if ($currentFile->isEqualTo($filePointer)) {
                return;
            }

            $this->fileRepository->copy($currentFile, $filePointer);

            // replace with the new file
            $this->loadProperty(
                class: $class,
                id: $id,
                object: $object,
                propertyName: $propertyName,
            );
        } elseif (null === $currentFile) {
            $this->removeProperty(
                class: $class,
                id: $id,
                object: $object,
                propertyName: $propertyName,
            );
        } else {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Property "%s" on object "%s" is not a %s instance',
                    $propertyName,
                    $object::class,
                    FileInterface::class,
                ),
            );
        }
    }

    /**
     * Process a file removal on an attribute
     *
     * @param class-string $class
     */
    #[\Override]
    public function removeProperty(
        object $object,
        string $class,
        string $id,
        string $propertyName,
    ): void {
        $filePointer = $this->fileLocationResolver->getFileLocation(
            class: $class,
            id: $id,
            propertyName: $propertyName,
        );

        $this->fileRepository->delete($filePointer);
    }

    /**
     * Process an attribute on an object loading
     *
     * @param class-string $class
     */
    #[\Override]
    public function loadProperty(
        object $object,
        string $class,
        string $id,
        string $propertyName,
    ): void {
        $propertyMetadata = $this->classMetadataFactory
            ->getClassMetadata($class)
            ->getProperty($propertyName);

        $filePointer = $this->fileLocationResolver->getFileLocation(
            class: $class,
            id: $id,
            propertyName: $propertyName,
        );

        if ($propertyMetadata->getFetch() === FetchMode::Eager) {
            $file = $this->fileRepository->tryGet($filePointer);

            if ($file === null && $propertyMetadata->isMandatory()) {
                $file = new MissingFile(
                    $filePointer->getFilesystemIdentifier(),
                    $filePointer->getKey(),
                );
            }
        } else {
            $file = $this->fileRepository->getReference($filePointer);
        }

        $this->writer->write($object, $propertyName, $file);
    }
}
