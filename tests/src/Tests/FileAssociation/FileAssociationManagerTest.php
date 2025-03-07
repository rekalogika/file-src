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
use Rekalogika\Contracts\File\FileProxy;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Association\FileAssociationManager;
use Rekalogika\File\Association\Model\MissingFile;
use Rekalogika\File\File;
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Tests\Tests\File\FileTestTrait;
use Rekalogika\File\Tests\Tests\Model\Entity;
use Rekalogika\File\Tests\Tests\Model\EntityWithLazyFile;
use Rekalogika\File\Tests\Tests\Model\EntityWithMandatoryFile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FileAssociationManagerTest extends KernelTestCase
{
    use FileTestTrait;

    private ?FileAssociationManager $fileAssociationManager = null;

    private ?FileRepositoryInterface $fileRepository = null;

    #[\Override]
    protected function setUp(): void
    {

        $fileAssociationManager = static::getContainer()
            ->get(FileAssociationManager::class);
        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertInstanceOf(
            FileAssociationManager::class,
            $fileAssociationManager,
        );

        $this->fileAssociationManager = $fileAssociationManager;

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
        // @phpstan-ignore method.impossibleType
        $this->assertInstanceOf(TemporaryFile::class, $file);
        $oldPointer = $file->getPointer();

        // persist
        $this->fileAssociationManager?->save($entity);
        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);
        $newPointer = $file->getPointer();

        // @phpstan-ignore argument.type
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
        $this->fileAssociationManager?->save($entity);

        try {
            $file = $this->fileRepository?->get($pointer);
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
        $this->fileAssociationManager?->save($entity);

        // remove
        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);
        $pointer = $file->getPointer();
        $this->fileAssociationManager?->remove($entity);

        try {
            $file = $this->fileRepository?->get($pointer);
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
        $this->fileAssociationManager?->save($entity);
        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);
        $pointer = $file->getPointer();

        // reload
        $entity = new Entity('entity_id');
        $this->assertNull($entity->getFile());
        $this->fileAssociationManager?->load($entity);

        $file = $entity->getFile();
        /** @psalm-suppress DocblockTypeContradiction */
        $this->assertInstanceOf(File::class, $file);

        // remove
        $this->fileAssociationManager?->remove($entity);
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
        $this->fileAssociationManager?->save($entity);
        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);

        // clear cache
        $this->fileRepository?->clear();

        // reload
        $entity = new EntityWithLazyFile('entity_id');
        $this->assertNull($entity->getFile());
        $this->fileAssociationManager?->load($entity);

        $file = $entity->getFile();
        /** @psalm-suppress DocblockTypeContradiction */
        $this->assertInstanceOf(FileProxy::class, $file);

        // remove
        $this->fileAssociationManager?->remove($entity);
    }

    public function testEntityWithNonNullableFile(): void
    {
        // create new entity
        $file = TemporaryFile::createFromString('testContent');
        $entity = new EntityWithMandatoryFile('entity_id');
        $entity->setFile($file);

        // persist
        $this->fileAssociationManager?->save($entity);
        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);

        // clear cache
        $this->fileRepository?->clear();

        // reload
        $entity = new EntityWithMandatoryFile('entity_id');
        $this->fileAssociationManager?->load($entity);

        $file = $entity->getFile();
        $this->assertInstanceOf(File::class, $file);

        // remove
        $this->fileAssociationManager?->remove($entity);

        // reload again
        $entity = new EntityWithMandatoryFile('entity_id');
        $this->fileAssociationManager?->load($entity);

        $file = $entity->getFile();
        $this->assertInstanceOf(MissingFile::class, $file);
    }
}
