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
use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Contracts\PropertyReaderInterface;
use Rekalogika\File\Association\Contracts\PropertyWriterInterface;
use Rekalogika\File\Association\Model\FetchMode;
use Rekalogika\File\Association\Model\MissingFile;
use Rekalogika\File\Association\Model\PropertyMetadata;

final readonly class DefaultFilePropertyManager implements FilePropertyManagerInterface
{
    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private PropertyReaderInterface $reader,
        private PropertyWriterInterface $writer,
        private ClassBasedFileLocationResolverInterface $fileLocationResolver,
    ) {}

    /**
     * Process a potential incoming file upload on a property
     */
    #[\Override]
    public function saveProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): void {
        $propertyName = $propertyMetadata->getName();
        $class = $propertyMetadata->getClass();

        /** @psalm-suppress MixedAssignment */
        $currentFile = $this->reader->read($object, $propertyName);

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
                propertyMetadata: $propertyMetadata,
                object: $object,
                id: $id,
            );
        } elseif (null === $currentFile) {
            $this->removeProperty(
                propertyMetadata: $propertyMetadata,
                object: $object,
                id: $id,
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
     */
    #[\Override]
    public function removeProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): void {
        $propertyName = $propertyMetadata->getName();
        $class = $propertyMetadata->getClass();

        $filePointer = $this->fileLocationResolver->getFileLocation(
            class: $class,
            id: $id,
            propertyName: $propertyName,
        );

        $this->fileRepository->delete($filePointer);
    }

    /**
     * Process an attribute on an object loading
     */
    #[\Override]
    public function loadProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): void {
        $propertyName = $propertyMetadata->getName();
        $class = $propertyMetadata->getClass();

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
