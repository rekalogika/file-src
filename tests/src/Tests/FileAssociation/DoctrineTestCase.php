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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\Proxy;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DoctrineTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected FileRepositoryInterface $fileRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        // save entity manager to class property

        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);
        $this->entityManager = $entityManager;

        // create schema

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        // save file repository to class property

        $fileRepository = static::getContainer()->get(FileRepositoryInterface::class);
        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertInstanceOf(FileRepositoryInterface::class, $fileRepository);
        $this->fileRepository = $fileRepository;
    }


    //
    // assertions
    //

    protected function assertIsProxy(mixed $object): void
    {
        $this->assertIsObject($object, 'Expected an object');

        if (\PHP_VERSION_ID >= 80400) {
            $reflection = new \ReflectionClass($object);
            /**
             * @psalm-suppress UndefinedMethod
             * @psalm-suppress MixedAssignment
             */
            $isProxy = $reflection->isUninitializedLazyObject($object);

            if ($isProxy) {
                return;
            }
        }

        $this->assertInstanceOf(Proxy::class, $object, 'Object is not a proxy');
        $this->assertFalse($object->__isInitialized(), 'Object is not an uninitialized proxy');
    }

    protected function assertNotProxy(mixed $object): void
    {
        $this->assertIsObject($object, 'Expected an object');

        if ($object instanceof Proxy) {
            $this->assertTrue($object->__isInitialized(), 'Object is a proxy, but should not be');

            return;
        }

        if (\PHP_VERSION_ID >= 80400) {
            $reflection = new \ReflectionClass($object);

            /**
             * @psalm-suppress UndefinedMethod
             */
            if ($reflection->isUninitializedLazyObject($object)) {
                static::fail('Object is a proxy, but should not be');
            }
        }
    }
}
