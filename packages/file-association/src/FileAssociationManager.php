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

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Association\Contracts\ClassMetadataFactoryInterface;
use Rekalogika\File\Association\Contracts\FileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\PropertyReaderInterface;
use Rekalogika\File\Association\Contracts\PropertyWriterInterface;
use Rekalogika\File\Association\Model\FetchMode;
use Rekalogika\File\Association\Model\MissingFile;
use Rekalogika\File\Association\Util\ProxyUtil;

final readonly class FileAssociationManager
{
    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private PropertyReaderInterface $reader,
        private PropertyWriterInterface $writer,
        private ClassMetadataFactoryInterface $classMetadataFactory,
        private FileLocationResolverInterface $fileLocationResolver,
    ) {}

    /**
     * Called when the object is saved
     */
    public function save(object $object): void
    {
        $class = ProxyUtil::normalizeClassName($object::class);
        $classMetadata = $this->classMetadataFactory->getClassMetadata($class);

        foreach ($classMetadata->getPropertyNames() as $propertyName) {
            $this->saveProperty($object, $propertyName);
        }
    }

    /**
     * Called when the object is removed
     */
    public function remove(object $object): void
    {
        $class = ProxyUtil::normalizeClassName($object::class);
        $classMetadata = $this->classMetadataFactory->getClassMetadata($class);

        foreach ($classMetadata->getPropertyNames() as $propertyName) {
            $this->removeProperty($object, $propertyName);
        }
    }

    /**
     * Called after the object is loaded
     */
    public function load(object $object): void
    {
        $class = ProxyUtil::normalizeClassName($object::class);
        $classMetadata = $this->classMetadataFactory->getClassMetadata($class);

        foreach ($classMetadata->getPropertyNames() as $propertyName) {
            $this->loadProperty($object, $propertyName);
        }
    }

    /**
     * Process a potential incoming file upload on a property
     */
    private function saveProperty(
        object $object,
        string $propertyName,
    ): void {
        $currentFile = $this->reader->read($object, $propertyName);
        \assert($currentFile instanceof FileInterface || null === $currentFile);

        if ($currentFile instanceof FileInterface) {
            $filePointer = $this->fileLocationResolver
                ->getFileLocation($object, $propertyName);

            if ($currentFile->isEqualTo($filePointer)) {
                return;
            }

            $this->fileRepository->copy($currentFile, $filePointer);

            // replace with the new file
            $this->loadProperty($object, $propertyName);
        } elseif (null === $currentFile) {
            $this->removeProperty($object, $propertyName);
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
    private function removeProperty(
        object $object,
        string $propertyName,
    ): void {
        $filePointer = $this->fileLocationResolver
            ->getFileLocation($object, $propertyName);
        $this->fileRepository->delete($filePointer);
    }

    /**
     * Process an attribute on an object loading
     */
    private function loadProperty(
        object $object,
        string $propertyName,
    ): void {
        $class = ProxyUtil::normalizeClassName($object::class);

        $propertyMetadata = $this->classMetadataFactory
            ->getClassMetadata($class)
            ->getProperty($propertyName);

        $filePointer = $this->fileLocationResolver
            ->getFileLocation($object, $propertyName);

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
