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
use Rekalogika\File\File;
use Rekalogika\File\Tests\App\Entity\User;

final class EntityTest extends DoctrineTestCase
{
    private function createUserWithImage(): User
    {
        $image = new File(__DIR__ . '/../Resources/smiley.png');
        $user = new User('foo');
        $user->setImage($image);

        return $user;
    }

    public function testCreate(): void
    {
        $user = $this->createUserWithImage();
        $image = $user->getImage();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertInstanceOf(File::class, $user->getImage());
        $this->assertNotSame($image, $user->getImage());
    }

    public function testCreateLoad(): void
    {
        $user = $this->createUserWithImage();

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $image = $user->getImage();

        $this->entityManager->clear();

        $user = $this->entityManager->find(User::class, $user->getId());
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(File::class, $user->getImage());
        $this->assertEquals($image, $user->getImage());
    }

    public function testProxy(): void
    {
        $user = $this->createUserWithImage();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->entityManager->clear();

        $user = $this->entityManager->getReference(User::class, $user->getId());
        $this->assertIsProxy($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(File::class, $user->getImage());
    }

    public function testRemove(): void
    {
        $user = $this->createUserWithImage();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $image = $user->getImage();
        $this->assertInstanceOf(File::class, $image);
        $pointer = $image->getPointer();

        $this->entityManager->clear();

        $user = $this->entityManager->find(User::class, $user->getId());
        $this->assertInstanceOf(User::class, $user);

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->expectException(FileNotFoundException::class);
        $newFile = $this->fileRepository->get($pointer);
    }

    public function testRemoveProxy(): void
    {
        $user = $this->createUserWithImage();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $image = $user->getImage();
        $this->assertInstanceOf(File::class, $image);
        $pointer = $image->getPointer();

        $this->entityManager->clear();

        $user = $this->entityManager->getReference(User::class, $user->getId());
        $this->assertInstanceOf(User::class, $user);
        $this->assertIsProxy($user);

        $this->entityManager->remove($user);
        $this->assertNotProxy($user);
        $this->entityManager->flush();
        $this->assertNotProxy($user);

        $this->expectException(FileNotFoundException::class);
        $newFile = $this->fileRepository->get($pointer);
    }
}
