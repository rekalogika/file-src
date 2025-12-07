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
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class CommandTest extends DoctrineTestCase
{
    public function testCommand(): void
    {
        /** @var EntityManagerInterface */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $image = new File(__DIR__ . '/../Resources/smiley.png');
        $user = new User('foo');
        $user->setImage($image);

        $entityManager->persist($user);
        $entityManager->flush();

        $kernel = self::$kernel;
        $this->assertNotNull($kernel);

        $application = new Application($kernel);
        $command = $application->find('rekalogika:file:resolve');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'class' => User::class,
            'id' => (string) $user->getId(),
            'property' => 'image',
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('entity/', $output);
    }
}
