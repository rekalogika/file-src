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
use Rekalogika\File\File;
use Rekalogika\File\Tests\App\Entity\User;

final class EntityTest extends DoctrineTestCase
{
    public function testProxy(): void
    {
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);

        $image = new File(__DIR__ . '/../Resources/smiley.png');
        $user = new User('foo');
        $user->setImage($image);

        $entityManager->persist($user);
        $entityManager->flush();

        $entityManager->clear();

        $user = $entityManager->getReference(User::class, $user->getId());
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(File::class, $user->getImage());
    }
}
