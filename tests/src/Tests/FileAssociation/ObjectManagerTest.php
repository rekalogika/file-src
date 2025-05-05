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

namespace Rekalogika\File\Tests\Tests\FileAssociation;

use Rekalogika\Contracts\File\Exception\File\FileNotFoundException;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileProxy;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Association\Contracts\ObjectManagerInterface;
use Rekalogika\File\Association\Model\MissingFile;
use Rekalogika\File\File;
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Tests\Tests\File\FileTestTrait;
use Rekalogika\File\Tests\Tests\Model\Entity;
use Rekalogika\File\Tests\Tests\Model\EntityWithLazyFile;
use Rekalogika\File\Tests\Tests\Model\EntityWithMandatoryFile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ObjectManagerTest extends KernelTestCase
{
    use FileTestTrait;

    private ObjectManagerInterface $objectManager;

    private FileRepositoryInterface $fileRepository;

    #[\Override]
    protected function setUp(): void
    {

        $fileAssociationManager = static::getContainer()
            ->get('rekalogika.file.association.object_manager');

        $this->assertInstanceOf(
            ObjectManagerInterface::class,
            $fileAssociationManager,
        );

        $this->objectManager = $fileAssociationManager;

        $fileRepository = static::getContainer()
            ->get(FileRepositoryInterface::class);

        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertInstanceOf(
            FileRepositoryInterface::class,
            $fileRepository,
        );

        $this->fileRepository = $fileRepository;
    }

    public function testPersistEntity(): void
    {
        // create new entity
        $entity = new Entity('entity_id');
        $this->assertNull($entity->getFile());

        // set file
        $newFile = TemporaryFile::createFromString('testContent');
        $newFile->setName('newname.txt');

        $entity->setFile($newFile);

        $file = $entity->getFile();
        $this->assertInstanceOf(TemporaryFile::class, $file);
        $oldPointer = $file->getPointer();

        // persist
        $this->objectManager->flushObject($entity);
        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);
        $newPointer = $file->getPointer();

        $this->assertFalse($newPointer->isEqualTo($oldPointer));
        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: 'default',
            key: $newPointer->getKey(),
            content: 'testContent',
            fileName: 'newname.txt',
            type: 'text/plain',
        );

        // unset file
        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);
        $pointer = $file->getPointer();
        $entity->setFile(null);
        $this->assertNull($entity->getFile());

        // persist
        $this->objectManager->flushObject($entity);

        try {
            $file = $this->fileRepository->get($pointer);
        } catch (FileNotFoundException) {
            $file = null;
        }

        $this->assertNull($file);
    }

    public function testRemoveEntity(): void
    {
        // create new entity
        $entity = new Entity('entity_id');
        $this->assertNull($entity->getFile());

        // set file
        $newFile = TemporaryFile::createFromString('testContent');
        $newFile->setName('newname.txt');

        $entity->setFile($newFile);

        // persist
        $this->objectManager->flushObject($entity);

        // remove
        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);
        $pointer = $file->getPointer();
        $this->objectManager->removeObject($entity);

        try {
            $file = $this->fileRepository->get($pointer);
        } catch (FileNotFoundException) {
            $file = null;
        }

        $this->assertNull($file);
    }

    public function testLoadEntity(): void
    {
        // create new entity
        $entity = new Entity('entity_id');
        $this->assertNull($entity->getFile());

        // set file
        $newFile = TemporaryFile::createFromString('testContent');
        $newFile->setName('newname.txt');

        $entity->setFile($newFile);

        // persist
        $this->objectManager->flushObject($entity);
        $file = $entity->getFile();
        $this->assertInstanceOf(FileInterface::class, $file);
        $pointer = $file->getPointer();

        // reload
        $entity = new Entity('entity_id');
        $this->assertNull($entity->getFile());
        $this->objectManager->loadObject($entity);

        $file = $entity->getFile();
        $this->assertInstanceOf(FileInterface::class, $file);

        // remove
        $this->objectManager->removeObject($entity);
    }


    public function testLazyLoadEntity(): void
    {
        // create new entity
        $entity = new EntityWithLazyFile('entity_id');
        $this->assertNull($entity->getFile());

        // set file
        $newFile = TemporaryFile::createFromString('testContent');
        $newFile->setName('newname.txt');

        $entity->setFile($newFile);

        // persist
        $this->objectManager->flushObject($entity);
        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);

        // clear cache
        $this->fileRepository->clear();

        // reload
        $entity = new EntityWithLazyFile('entity_id');
        $this->assertNull($entity->getFile());
        $this->objectManager->loadObject($entity);

        $file = $entity->getFile();
        /** @psalm-suppress DocblockTypeContradiction */
        $this->assertInstanceOf(FileProxy::class, $file);

        // remove
        $this->objectManager->removeObject($entity);
    }

    public function testEntityWithNonNullableFile(): void
    {
        // create new entity
        $file = TemporaryFile::createFromString('testContent');
        $entity = new EntityWithMandatoryFile('entity_id');
        $entity->setFile($file);

        // persist
        $this->objectManager->flushObject($entity);
        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);

        // clear cache
        $this->fileRepository->clear();

        // reload
        $entity = new EntityWithMandatoryFile('entity_id');
        $this->objectManager->loadObject($entity);

        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);

        // remove
        $this->objectManager->removeObject($entity);

        // reload again
        $entity = new EntityWithMandatoryFile('entity_id');
        $this->objectManager->loadObject($entity);

        $file = $entity->getFile();
        $this->assertInstanceOf(MissingFile::class, $file);
    }
}
