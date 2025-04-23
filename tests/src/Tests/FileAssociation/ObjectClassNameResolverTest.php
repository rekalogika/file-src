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
use Rekalogika\File\Association\ObjectClassNameResolver\DefaultObjectClassNameResolver;
use Rekalogika\File\Association\ObjectClassNameResolver\DoctrineObjectClassNameResolver;
use Rekalogika\File\Tests\App\Entity\User;

final class ObjectClassNameResolverTest extends DoctrineTestCase
{
    public function testDefaultObjectClassNameResolver(): void
    {
        $objectClassNameResolver = new DefaultObjectClassNameResolver();
        $entity = new User('foo');

        $resolvedClassName = $objectClassNameResolver->getObjectClassName($entity);
        $this->assertSame(User::class, $resolvedClassName);
    }

    public function testDoctrineProxyObjectClassNameResolver(): void
    {
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);

        $entityClass = User::class;
        $entityProxy = $entityManager->getReference($entityClass, 123);
        $this->assertNotNull($entityProxy);
        $this->assertNotSame($entityClass, $entityProxy::class);

        $doctrineObjectClassNameResolver = new DoctrineObjectClassNameResolver();
        $resolvedClassName = $doctrineObjectClassNameResolver->getObjectClassName($entityProxy);

        $this->assertSame($entityClass, $resolvedClassName);
    }
}
